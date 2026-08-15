<?php

declare(strict_types=1);

$projectRoot = (string) (getenv('APP_ROOT') ?: dirname(__DIR__));
$schemaDirectory = (string) (getenv('SCHEMA_DIR') ?: $projectRoot . '/database');

require_once $projectRoot . '/components/database.php';

/**
 * Divide um arquivo SQL sem quebrar textos entre aspas.
 *
 * Os esquemas do projeto contêm apenas instruções SQL comuns, mas executar o
 * arquivo inteiro em uma única chamada deixa a sessão PostgreSQL abortada
 * quando uma das tabelas legadas diverge do esquema atual. A execução por
 * instrução preserva o banco existente e também mantém o erro original.
 *
 * @return list<string>
 */
function split_sql_statements(string $sql): array
{
    $statements = [];
    $buffer = '';
    $quote = null;
    $length = strlen($sql);

    for ($index = 0; $index < $length; $index++) {
        $character = $sql[$index];
        $buffer .= $character;

        if ($quote !== null) {
            if ($character === $quote) {
                $next = $sql[$index + 1] ?? '';
                if ($next === $quote) {
                    $buffer .= $next;
                    $index++;
                } else {
                    $quote = null;
                }
            } elseif ($character === '\\' && $quote !== '`' && $index + 1 < $length) {
                $buffer .= $sql[++$index];
            }
            continue;
        }

        if ($character === "'" || $character === '"' || $character === '`') {
            $quote = $character;
            continue;
        }

        if ($character === ';') {
            $statement = trim(substr($buffer, 0, -1));
            if ($statement !== '') {
                $statements[] = $statement;
            }
            $buffer = '';
        }
    }

    $statement = trim($buffer);
    if ($statement !== '') {
        $statements[] = $statement;
    }

    return $statements;
}

function database_table_exists(PDO $pdo, string $driver, string $table): bool
{
    $sql = $driver === 'pgsql'
        ? 'SELECT 1 FROM information_schema.tables WHERE table_schema = current_schema() AND table_name = :table'
        : 'SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :table';
    $statement = $pdo->prepare($sql);
    $statement->execute(['table' => $table]);
    return $statement->fetchColumn() !== false;
}

function execute_schema_statements(PDO $pdo, string $driver, string $sql, string $schemaPath): void
{
    foreach (split_sql_statements($sql) as $position => $statement) {
        if (
            preg_match('/^CREATE\s+TABLE\s+IF\s+NOT\s+EXISTS\s+[`"]?([a-zA-Z0-9_]+)[`"]?/i', $statement, $matches) === 1
            && database_table_exists($pdo, $driver, $matches[1])
        ) {
            continue;
        }

        try {
            $pdo->exec($statement);
        } catch (Throwable $exception) {
            throw new RuntimeException(
                'Falha no comando ' . ($position + 1) . ' de ' . basename($schemaPath) . ': ' . $exception->getMessage(),
                0,
                $exception
            );
        }
    }
}

try {
    $pdo = create_database_connection();
    $driver = (string) $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    $schemaPath = $schemaDirectory . '/schema.' . $driver . '.sql';

    if (!is_file($schemaPath)) {
        throw new RuntimeException('Esquema não encontrado para o driver ' . $driver);
    }

    $sql = file_get_contents($schemaPath);
    if ($sql === false) {
        throw new RuntimeException('Não foi possível ler o esquema do banco.');
    }

    execute_schema_statements($pdo, $driver, $sql, $schemaPath);

    $migrationTableSql = $driver === 'pgsql'
        ? 'CREATE TABLE IF NOT EXISTS schema_migrations (version VARCHAR(120) PRIMARY KEY, applied_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP)'
        : 'CREATE TABLE IF NOT EXISTS schema_migrations (version VARCHAR(120) NOT NULL, applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (version)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
    $pdo->exec($migrationTableSql);

    $migrationDirectory = $schemaDirectory . '/migrations';
    $migrationFiles = glob($migrationDirectory . '/*.php') ?: [];
    sort($migrationFiles, SORT_STRING);

    foreach ($migrationFiles as $migrationFile) {
        $version = basename($migrationFile, '.php');
        $check = $pdo->prepare('SELECT 1 FROM schema_migrations WHERE version = :version');
        $check->execute(['version' => $version]);
        if ($check->fetchColumn() !== false) {
            continue;
        }

        $migration = require $migrationFile;
        if (!is_callable($migration)) {
            throw new RuntimeException('Migração inválida: ' . $version);
        }

        if ($driver === 'pgsql') {
            $pdo->beginTransaction();
        }
        try {
            $migration($pdo, $driver);
            $record = $pdo->prepare('INSERT INTO schema_migrations (version) VALUES (:version)');
            $record->execute(['version' => $version]);
            if ($pdo->inTransaction()) {
                $pdo->commit();
            }
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $exception;
        }
    }

    fwrite(STDOUT, "Migrações concluídas para {$driver}." . PHP_EOL);
} catch (Throwable $exception) {
    fwrite(STDERR, 'Falha na migração: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}
