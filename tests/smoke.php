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

[$dsn, $user, $password, $driver] = database_config_from_url(
    'postgresql://adota:p%40ss@ep-example-pooler.us-east-1.aws.neon.tech:5432/adota_pet?sslmode=require&channel_binding=require'
);
check(
    $dsn === 'pgsql:host=ep-example-pooler.us-east-1.aws.neon.tech;port=5432;dbname=adota_pet;connect_timeout=10;application_name=adota_pet;sslmode=require;channel_binding=require',
    'DSN PostgreSQL/Neon incorreta.'
);
check($user === 'adota' && $password === 'p@ss' && $driver === 'pgsql', 'Leitura de DATABASE_URL incorreta.');

[$neonDsn] = database_config_from_url('postgresql://usuario:senha@ep-example.neon.tech/adota_pet');
check(str_contains($neonDsn, ';sslmode=require;channel_binding=require'), 'TLS obrigatório do Neon não foi aplicado.');

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

    if (str_contains($content, '<head>')) {
        check(
            str_contains($content, '<meta name="viewport" content="width=device-width, initial-scale=1.0">'),
            'Viewport responsivo ausente em ' . basename($pageFile) . '.'
        );
    }

    check(
        !preg_match('/AnaJuliaN|ana-júlia|archv\.naju/i', $content),
        'Perfil social de terceiro encontrado em ' . basename($pageFile) . '.'
    );

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

$socialProfiles = [
    'LinkedIn' => 'https://www.linkedin.com/in/andersonluiscosta/',
    'GitHub' => 'https://github.com/andertrunks',
    'Facebook' => 'https://www.facebook.com/andersonluis.costa',
    'Instagram' => 'https://www.instagram.com/anderluiscosta/',
    'X' => 'https://x.com/anderluiscosta',
];

foreach (['components/index.html', 'components/inicio.php'] as $relativePath) {
    $content = (string) file_get_contents($projectRoot . '/' . $relativePath);
    $previousPosition = -1;

    foreach ($socialProfiles as $network => $url) {
        $needle = '<a href="' . $url . '" target="_blank" rel="noopener noreferrer" aria-label="'
            . $network . ' de Anderson Luis Costa">';
        $position = strpos($content, $needle);
        check($position !== false, "Link acessível do {$network} ausente em {$relativePath}.");
        check($position !== false && $position > $previousPosition, "Ordem social incorreta em {$relativePath}.");
        if ($position !== false) {
            $previousPosition = $position;
        }
    }
}

check(is_file($projectRoot . '/database/schema.mysql.sql'), 'Esquema MySQL ausente.');
check(is_file($projectRoot . '/database/schema.pgsql.sql'), 'Esquema PostgreSQL ausente.');
check(is_file($projectRoot . '/docker-compose.yml'), 'Docker Compose ausente.');
check(is_file($projectRoot . '/docker/php-production.ini'), 'Configuração PHP de produção ausente.');

$dockerfile = file_get_contents($projectRoot . '/Dockerfile');
check(str_contains((string) $dockerfile, 'HEALTHCHECK'), 'HEALTHCHECK do contêiner ausente.');

$renderBlueprint = file_get_contents($projectRoot . '/render.yaml');
check(str_contains((string) $renderBlueprint, 'plan: free'), 'Blueprint não usa a instância gratuita.');
check(str_contains((string) $renderBlueprint, 'sync: false'), 'DATABASE_URL não está declarada como secret.');
check(!str_contains((string) $renderBlueprint, 'databases:'), 'Blueprint não deve criar banco temporário no Render.');

if ($failures !== []) {
    fwrite(STDERR, "Falhas no smoke test:" . PHP_EOL);
    foreach ($failures as $failure) {
        fwrite(STDERR, '- ' . $failure . PHP_EOL);
    }
    exit(1);
}

fwrite(STDOUT, 'Smoke test concluído sem falhas.' . PHP_EOL);
