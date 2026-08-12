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
        PDO::ATTR_PERSISTENT => false,
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
    $host = (string) $parts['host'];
    $database = rawurldecode(ltrim((string) $parts['path'], '/'));
    $connectionOptions = [];

    if ($driver === 'pgsql') {
        parse_str((string) ($parts['query'] ?? ''), $query);
        $connectionOptions = postgres_connection_options($host, $query);
    }

    $dsn = build_database_dsn($driver, $host, $port, $database, $connectionOptions);

    return [
        $dsn,
        rawurldecode((string) ($parts['user'] ?? '')),
        rawurldecode((string) ($parts['pass'] ?? '')),
        $driver,
    ];
}

function build_database_dsn(
    string $driver,
    string $host,
    string $port,
    string $database,
    array $connectionOptions = []
): string
{
    if (!preg_match('/^\d{1,5}$/', $port) || (int) $port < 1 || (int) $port > 65535) {
        throw new RuntimeException('Porta de banco inválida.');
    }

    foreach ([$host, $database] as $value) {
        if ($value === '' || preg_match('/[;\r\n]/', $value)) {
            throw new RuntimeException('Configuração de banco inválida.');
        }
    }

    if ($driver === 'mysql') {
        return "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
    }

    if ($driver === 'pgsql') {
        $dsn = "pgsql:host={$host};port={$port};dbname={$database};connect_timeout=10;application_name=adota_pet";

        foreach ($connectionOptions as $key => $value) {
            $dsn .= ";{$key}={$value}";
        }

        return $dsn;
    }

    throw new RuntimeException('Driver de banco não suportado: ' . $driver);
}

function postgres_connection_options(string $host, array $query): array
{
    $options = [];
    $sslModes = ['disable', 'allow', 'prefer', 'require', 'verify-ca', 'verify-full'];
    $channelBindingModes = ['disable', 'prefer', 'require'];

    $sslMode = strtolower(trim((string) ($query['sslmode'] ?? '')));
    $channelBinding = strtolower(trim((string) ($query['channel_binding'] ?? '')));
    $isNeon = str_ends_with(strtolower($host), '.neon.tech');

    if ($isNeon && $sslMode === '') {
        $sslMode = 'require';
    }

    if ($isNeon && $channelBinding === '') {
        $channelBinding = 'require';
    }

    if ($sslMode !== '') {
        if (!in_array($sslMode, $sslModes, true)) {
            throw new RuntimeException('sslmode inválido em DATABASE_URL.');
        }
        $options['sslmode'] = $sslMode;
    }

    if ($channelBinding !== '') {
        if (!in_array($channelBinding, $channelBindingModes, true)) {
            throw new RuntimeException('channel_binding inválido em DATABASE_URL.');
        }
        $options['channel_binding'] = $channelBinding;
    }

    return $options;
}
