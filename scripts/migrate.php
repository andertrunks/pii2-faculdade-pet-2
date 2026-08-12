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
    fwrite(STDOUT, "Migração concluída para {$driver}." . PHP_EOL);
} catch (Throwable $exception) {
    fwrite(STDERR, 'Falha na migração: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}
