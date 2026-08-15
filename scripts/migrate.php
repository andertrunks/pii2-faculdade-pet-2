<?php

declare(strict_types=1);

$projectRoot = (string) (getenv('APP_ROOT') ?: dirname(__DIR__));
$schemaDirectory = (string) (getenv('SCHEMA_DIR') ?: $projectRoot . '/database');

require_once $projectRoot . '/components/database.php';

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

    $pdo->exec($sql);

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
