<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/components/database.php';

$baseUrl = rtrim((string) (getenv('BASE_URL') ?: 'http://127.0.0.1:8080'), '/');
$cookies = [];
$failures = [];
$testEmail = 'e2e-' . bin2hex(random_bytes(6)) . '@example.test';
$pdo = create_database_connection();

function expectation(bool $condition, string $message): void
{
    global $failures;
    if (!$condition) {
        $failures[] = $message;
    }
}

function request(string $method, string $path, array $data = []): array
{
    global $baseUrl, $cookies;

    $headers = ['Accept: text/html,application/json;q=0.9'];
    if ($cookies !== []) {
        $cookieParts = [];
        foreach ($cookies as $name => $value) {
            $cookieParts[] = $name . '=' . $value;
        }
        $headers[] = 'Cookie: ' . implode('; ', $cookieParts);
    }

    $options = [
        'method' => $method,
        'header' => implode("\r\n", $headers),
        'ignore_errors' => true,
        'follow_location' => 0,
        'timeout' => 15,
    ];
    if ($method === 'POST') {
        $options['header'] .= "\r\nContent-Type: application/x-www-form-urlencoded";
        $options['content'] = http_build_query($data);
    }

    $context = stream_context_create(['http' => $options]);
    $body = file_get_contents($baseUrl . $path, false, $context);
    $responseHeaders = $http_response_header ?? [];
    $status = 0;
    foreach ($responseHeaders as $header) {
        if (preg_match('#^HTTP/\S+\s+(\d{3})#', $header, $matches)) {
            $status = (int) $matches[1];
        }
        if (preg_match('/^Set-Cookie:\s*([^=;]+)=([^;]*)/i', $header, $matches)) {
            $cookies[$matches[1]] = $matches[2];
        }
    }

    return ['status' => $status, 'body' => $body === false ? '' : $body, 'headers' => $responseHeaders];
}

function csrfFrom(string $body): string
{
    if (!preg_match('/name="csrf_token"\s+value="([^"]+)"/', $body, $matches)) {
        return '';
    }
    return html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function hasHeader(array $headers, string $name): bool
{
    foreach ($headers as $header) {
        if (str_starts_with(strtolower($header), strtolower($name) . ':')) {
            return true;
        }
    }
    return false;
}

try {
    $health = request('GET', '/health.php');
    expectation($health['status'] === 200, 'Health check não retornou HTTP 200.');
    expectation(str_contains($health['body'], '"status":"ok"'), 'Health check não confirmou o banco.');

    $login = request('GET', '/components/login.php');
    expectation($login['status'] === 200, 'Tela de login não abriu.');
    expectation(hasHeader($login['headers'], 'Content-Security-Policy'), 'CSP ausente nas páginas PHP.');
    $csrf = csrfFrom($login['body']);
    expectation($csrf !== '', 'Token CSRF não foi renderizado no login.');

    $signup = request('POST', '/components/efetuar_login.php', [
        'hidden' => '1',
        'csrf_token' => $csrf,
        'name' => 'Usuário E2E',
        'email' => $testEmail,
        'password' => 'Senha-E2E-123!',
        'cep' => '14800100',
        'rua' => 'Rua de Teste',
        'cidade' => 'Araraquara',
        'uf' => 'SP',
    ]);
    expectation($signup['status'] === 303, 'Cadastro não redirecionou após sucesso.');

    $login = request('GET', '/components/login.php');
    $csrf = csrfFrom($login['body']);
    $authenticated = request('POST', '/components/logar.php', [
        'csrf_token' => $csrf,
        'email' => $testEmail,
        'password' => 'Senha-E2E-123!',
    ]);
    expectation($authenticated['status'] === 303, 'Login válido não redirecionou.');

    $catalog = request('GET', '/components/adote.php');
    expectation($catalog['status'] === 200, 'Catálogo autenticado não abriu.');
    expectation(preg_match('/animal\.php\?id=(\d+)/', $catalog['body'], $animalMatch) === 1, 'Catálogo não exibiu animais do banco.');
    $animalId = isset($animalMatch[1]) ? (int) $animalMatch[1] : 0;

    $missingAnimal = request('GET', '/components/animal.php?id=999999999');
    expectation($missingAnimal['status'] === 404, 'Animal inexistente não retornou HTTP 404.');

    $adoptionForm = request('GET', '/components/formulario_adocao.php?animal_id=' . $animalId);
    expectation($adoptionForm['status'] === 200, 'Formulário do animal escolhido não abriu.');
    expectation(str_contains($adoptionForm['body'], 'name="animal_id" value="' . $animalId . '"'), 'Animal escolhido não foi preservado no formulário.');
    $csrf = csrfFrom($adoptionForm['body']);
    $adoption = request('POST', '/components/recebe_form.php', [
        'csrf_token' => $csrf,
        'animal_id' => (string) $animalId,
        'nome' => 'Usuário E2E',
        'telefone' => '16999999999',
        'idade' => '30',
        'profissao' => 'Pessoa testadora',
        'residencia' => 'Casa',
        'espaco' => 'Quintal seguro e telado',
        'acordo' => 'Todos os moradores concordam',
        'animais' => 'Já cuidou de outros animais',
        'pq_animais' => 'Os animais anteriores faleceram por idade avançada',
        'tempo' => 'Quatro horas por dia e acompanhamento contínuo',
        'deseja_adotar' => 'Deseja oferecer um lar responsável e permanente',
        'ciente' => 'Sim, está ciente dos cuidados veterinários',
    ]);
    expectation($adoption['status'] === 303, 'Solicitação de adoção não foi aceita.');

    $donationForm = request('GET', '/components/doar.php');
    $csrf = csrfFrom($donationForm['body']);
    $donation = request('POST', '/components/cadastro_doar.php', [
        'hidden' => '1',
        'csrf_token' => $csrf,
        'nome_pet' => 'Pet E2E',
        'idade_pet' => '2 anos',
        'nome' => 'Usuário E2E',
        'telefone' => '16999999999',
        'email' => $testEmail,
        'cep' => '14800100',
        'cidade' => 'Araraquara',
        'uf' => 'SP',
        'sobre' => 'Animal saudável e sociável cadastrado somente pelo teste automatizado.',
    ]);
    expectation($donation['status'] === 303, 'Cadastro de doação não foi aceito.');

    $reportForm = request('GET', '/components/denuncia.php');
    $csrf = csrfFrom($reportForm['body']);
    $report = request('POST', '/components/efetuar_denuncia.php', [
        'hidden' => '1',
        'csrf_token' => $csrf,
        'titulo' => 'Denúncia temporária E2E',
        'data_denuncia' => '2026-08-15',
        'descricao' => 'Registro temporário criado exclusivamente para validar o fluxo automatizado.',
    ]);
    expectation($report['status'] === 303, 'Envio de denúncia não foi aceito.');

    $userStatement = $pdo->prepare('SELECT id_cadastro FROM cadastro WHERE email = :email');
    $userStatement->execute(['email' => $testEmail]);
    $userId = (int) $userStatement->fetchColumn();
    expectation($userId > 0, 'Conta temporária não foi persistida.');

    $checks = [
        ['SELECT COUNT(*) FROM adocao WHERE user_id = :user_id AND animal_id = :animal_id', ['user_id' => $userId, 'animal_id' => $animalId], 'Adoção não está relacionada ao usuário e animal.'],
        ['SELECT COUNT(*) FROM doar WHERE user_id = :user_id AND email = :email', ['user_id' => $userId, 'email' => $testEmail], 'Doação não está relacionada ao usuário.'],
        ['SELECT COUNT(*) FROM denuncia WHERE user_id = :user_id', ['user_id' => $userId], 'Denúncia não está relacionada ao usuário.'],
    ];
    foreach ($checks as [$sql, $parameters, $message]) {
        $statement = $pdo->prepare($sql);
        $statement->execute($parameters);
        expectation((int) $statement->fetchColumn() === 1, $message);
    }
} catch (Throwable $exception) {
    $failures[] = 'Exceção durante o E2E: ' . $exception->getMessage();
} finally {
    try {
        $userStatement = $pdo->prepare('SELECT id_cadastro FROM cadastro WHERE email = :email');
        $userStatement->execute(['email' => $testEmail]);
        $userId = $userStatement->fetchColumn();
        if ($userId !== false) {
            foreach (['adocao', 'denuncia', 'doar'] as $table) {
                $delete = $pdo->prepare("DELETE FROM {$table} WHERE user_id = :user_id");
                $delete->execute(['user_id' => $userId]);
            }
            $delete = $pdo->prepare('DELETE FROM cadastro WHERE id_cadastro = :user_id');
            $delete->execute(['user_id' => $userId]);
        }
    } catch (Throwable $cleanupException) {
        $failures[] = 'Falha ao remover os dados temporários do E2E: ' . $cleanupException->getMessage();
    }
}

if ($failures !== []) {
    fwrite(STDERR, "Falhas no E2E:" . PHP_EOL);
    foreach ($failures as $failure) {
        fwrite(STDERR, '- ' . $failure . PHP_EOL);
    }
    exit(1);
}

fwrite(STDOUT, 'E2E concluído sem falhas em ' . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . '.' . PHP_EOL);
