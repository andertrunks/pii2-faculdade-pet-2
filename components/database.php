<?php

declare(strict_types=1);

function create_database_connection(): PDO
{
    $databaseUrl = trim((string) getenv('DATABASE_URL'));

    if ($databaseUrl !== '') {
        [$dsn, $user, $password, $driver] = database_config_from_url($databaseUrl);
    } else {
        $driver = strtolower(trim((string) (getenv('DB_DRIVER') ?: 'mysql')));
        $host = (string) (getenv('DB_HOST') ?: 'localhost');
        $port = (string) (getenv('DB_PORT') ?: ($driver === 'pgsql' ? '5432' : '3306'));
        $database = (string) (getenv('DB_NAME') ?: 'adota_pet');
        $user = (string) (getenv('DB_USER') ?: 'root');
        $password = (string) (getenv('DB_PASS') ?: '');
        $dsn = build_database_dsn($driver, $host, $port, $database);
    }

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_STRINGIFY_FETCHES => false,
    ];

    if ($driver === 'mysql') {
        $options[PDO::ATTR_EMULATE_PREPARES] = false;
    }

    return new PDO($dsn, $user, $password, $options);
}

function database_config_from_url(string $databaseUrl): array
{
    $parts = parse_url($databaseUrl);

    if ($parts === false || empty($parts['scheme']) || empty($parts['host']) || empty($parts['path'])) {
        throw new RuntimeException('DATABASE_URL inválida.');
    }

    $scheme = strtolower((string) $parts['scheme']);
    $driver = in_array($scheme, ['postgres', 'postgresql', 'pgsql'], true) ? 'pgsql' : $scheme;
    $port = (string) ($parts['port'] ?? ($driver === 'pgsql' ? '5432' : '3306'));
    $database = ltrim((string) $parts['path'], '/');
    $dsn = build_database_dsn($driver, (string) $parts['host'], $port, $database);

    return [
        $dsn,
        rawurldecode((string) ($parts['user'] ?? '')),
        rawurldecode((string) ($parts['pass'] ?? '')),
        $driver,
    ];
}

function build_database_dsn(string $driver, string $host, string $port, string $database): string
{
    if ($driver === 'mysql') {
        return "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
    }

    if ($driver === 'pgsql') {
        return "pgsql:host={$host};port={$port};dbname={$database}";
    }

    throw new RuntimeException('Driver de banco não suportado: ' . $driver);
}
