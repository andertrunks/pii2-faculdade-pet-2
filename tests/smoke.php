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
$attributePattern = '/(?:href|src|action)=(["\'])(.*?)\1/is';
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
    check(!preg_match('/href=["\']#["\']/', $content), 'Link sem destino em ' . basename($pageFile) . '.');
    check(!preg_match('/Lorem ipsum|Naju|9999-9999/i', $content), 'Conteúdo de preenchimento em ' . basename($pageFile) . '.');

    preg_match_all($attributePattern, $content, $attributeMatches);
    preg_match_all($onclickPattern, $content, $onclickMatches);
    $references = array_merge($attributeMatches[2] ?? [], $onclickMatches[1] ?? []);

    foreach ($references as $reference) {
        $reference = trim(html_entity_decode($reference));
        if ($reference === '' || $reference[0] === '#' || str_contains($reference, '<?') || preg_match('#^(?:https?:|mailto:|tel:|data:|javascript:)#i', $reference)) {
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

    check(
        substr_count($content, '<a class="fe-box" href=') === 4,
        "Cards de ação sem links completos em {$relativePath}."
    );
    check(
        substr_count($content, '<a class="information" href=') === 3,
        "Botões Saiba Mais sem destino em {$relativePath}."
    );
    check(
        !str_contains($content, 'href="#" class="btn"'),
        "Chamada principal sem destino em {$relativePath}."
    );

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
check(is_file($projectRoot . '/database/migrations/20260815_01_animais_relacionamentos.php'), 'Migração versionada ausente.');
check(is_file($projectRoot . '/docker-compose.yml'), 'Docker Compose ausente.');
check(is_file($projectRoot . '/docker/php-production.ini'), 'Configuração PHP de produção ausente.');

$migrationRunner = (string) file_get_contents($projectRoot . '/scripts/migrate.php');
check(
    str_contains($migrationRunner, 'execute_schema_statements($pdo, $driver, $sql, $schemaPath)'),
    'O esquema ainda nao e aplicado de forma isolada e compativel com bancos legados.'
);
check(
    str_contains($migrationRunner, 'database_table_exists($pdo, $driver, $matches[1])'),
    'As tabelas legadas existentes nao sao preservadas pelo inicializador.'
);
check(
    !str_contains($migrationRunner, '$pdo->beginTransaction()'),
    'A migracao incremental nao deve manter toda a conexao PostgreSQL em uma unica transacao.'
);

$dockerfile = file_get_contents($projectRoot . '/Dockerfile');
check(str_contains((string) $dockerfile, 'HEALTHCHECK'), 'HEALTHCHECK do contêiner ausente.');
check(str_contains((string) $dockerfile, 'ServerName localhost'), 'ServerName global do Apache ausente.');

$mysqlSchema = (string) file_get_contents($projectRoot . '/database/schema.mysql.sql');
$pgsqlSchema = (string) file_get_contents($projectRoot . '/database/schema.pgsql.sql');
foreach ([$mysqlSchema, $pgsqlSchema] as $schema) {
    check(str_contains($schema, 'CREATE TABLE IF NOT EXISTS animais'), 'Tabela de animais ausente do esquema.');
    check(str_contains($schema, 'user_id'), 'Relacionamento com usuário ausente do esquema.');
    check(str_contains($schema, 'animal_id'), 'Relacionamento entre adoção e animal ausente do esquema.');
}

$loginPage = (string) file_get_contents($projectRoot . '/components/login.php');
check(!str_contains($loginPage, 'social-icons'), 'Login social decorativo ainda está presente.');
check(!str_contains($loginPage, 'href="#"'), 'Controle falso ainda está presente no login.');

$adoptionHandler = (string) file_get_contents($projectRoot . '/components/recebe_form.php');
check(str_contains($adoptionHandler, 'animal_id'), 'Adoção não registra o animal escolhido.');
check(str_contains($adoptionHandler, 'user_id'), 'Adoção não registra o usuário autenticado.');

$indexCss = file_get_contents($projectRoot . '/css/index.css');
check(substr_count((string) $indexCss, 'overflow-x: hidden') >= 2, 'Proteção contra overflow horizontal ausente.');
check(str_contains((string) $indexCss, 'section.container'), 'Estilos do carrossel não estão isolados.');
check(
    !preg_match('/section\s*\{\s*height:\s*65vh;/s', (string) $indexCss),
    'Altura fixa global ainda pode sobrepor as seções da página.'
);

$readme = file_get_contents($projectRoot . '/README.md');
check(str_contains((string) $readme, 'https://adota-pet-jdzq.onrender.com'), 'URL pública ausente do README.');

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
