<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__);
require_once $projectRoot . '/components/functions.php';
require_once $projectRoot . '/components/database.php';

$failures = [];

function check(bool $condition, string $message): void
{
    global $failures;
    if (!$condition) {
        $failures[] = $message;
    }
}

check(
    build_database_dsn('mysql', 'db', '3306', 'adota_pet') === 'mysql:host=db;port=3306;dbname=adota_pet;charset=utf8mb4',
    'DSN MySQL incorreta.'
);

[$dsn, $user, $password, $driver] = database_config_from_url('postgresql://adota:p%40ss@db:5432/adota_pet');
check($dsn === 'pgsql:host=db;port=5432;dbname=adota_pet', 'DSN PostgreSQL incorreta.');
check($user === 'adota' && $password === 'p@ss' && $driver === 'pgsql', 'Leitura de DATABASE_URL incorreta.');

$_POST = ['email' => ' Pessoa@Example.com ', 'password' => ' senha com espaço ', 'idade' => '35', 'data' => '2026-08-11'];
check(post_email() === 'pessoa@example.com', 'Normalização de email incorreta.');
check(post_password() === ' senha com espaço ', 'A senha não deve ser alterada pela validação.');
check(post_integer('idade', 18, 120) === 35, 'Validação de inteiro incorreta.');
check(post_iso_date('data') === '2026-08-11', 'Validação de data incorreta.');

$handlerFiles = [
    'components/efetuar_login.php',
    'components/logar.php',
    'components/cadastro_doar.php',
    'components/efetuar_denuncia.php',
    'components/recebe_form.php',
];

foreach ($handlerFiles as $relativePath) {
    $content = file_get_contents($projectRoot . '/' . $relativePath);
    check($content !== false, "Não foi possível ler {$relativePath}.");
    check(!preg_match('/VALUES\s*\([^)]*\$[a-z_]/is', (string) $content), "SQL interpolada em {$relativePath}.");
    check(!str_contains((string) $content, 'addslashes('), "Uso inseguro de addslashes em {$relativePath}.");
}

$formFiles = [
    'components/login.php',
    'components/doar.php',
    'components/denuncia.php',
    'components/formulario_adocao.php',
];

foreach ($formFiles as $relativePath) {
    $content = file_get_contents($projectRoot . '/' . $relativePath);
    check(str_contains((string) $content, 'name="csrf_token"'), "Token CSRF ausente em {$relativePath}.");
}

$pageFiles = glob($projectRoot . '/components/*.{php,html}', GLOB_BRACE) ?: [];
$attributePattern = '/(?:href|src|action)=["\']([^"\']+)["\']/i';
$onclickPattern = '/window\.location\.href=["\']([^"\']+)["\']/i';

foreach ($pageFiles as $pageFile) {
    $content = file_get_contents($pageFile);
    if ($content === false) {
        $failures[] = 'Não foi possível ler ' . basename($pageFile) . '.';
        continue;
    }

    preg_match_all($attributePattern, $content, $attributeMatches);
    preg_match_all($onclickPattern, $content, $onclickMatches);
    $references = array_merge($attributeMatches[1] ?? [], $onclickMatches[1] ?? []);

    foreach ($references as $reference) {
        $reference = trim(html_entity_decode($reference));
        if ($reference === '' || $reference[0] === '#' || preg_match('#^(?:https?:|mailto:|tel:|data:|javascript:)#i', $reference)) {
            continue;
        }

        $path = parse_url($reference, PHP_URL_PATH);
        if (!is_string($path) || $path === '' || str_contains($path, '<?')) {
            continue;
        }

        $target = $path[0] === '/'
            ? $projectRoot . '/' . ltrim($path, '/')
            : dirname($pageFile) . '/' . $path;

        check(file_exists($target), basename($pageFile) . " aponta para arquivo inexistente: {$reference}");
    }
}

check(is_file($projectRoot . '/database/schema.mysql.sql'), 'Esquema MySQL ausente.');
check(is_file($projectRoot . '/database/schema.pgsql.sql'), 'Esquema PostgreSQL ausente.');
check(is_file($projectRoot . '/docker-compose.yml'), 'Docker Compose ausente.');

if ($failures !== []) {
    fwrite(STDERR, "Falhas no smoke test:" . PHP_EOL);
    foreach ($failures as $failure) {
        fwrite(STDERR, '- ' . $failure . PHP_EOL);
    }
    exit(1);
}

fwrite(STDOUT, 'Smoke test concluído sem falhas.' . PHP_EOL);
