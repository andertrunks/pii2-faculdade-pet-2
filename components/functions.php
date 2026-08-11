<?php

declare(strict_types=1);

function is_https_request(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
}

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => is_https_request(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function send_security_headers(): void
{
    if (headers_sent()) {
        return;
    }

    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
}

function escape_html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    start_secure_session();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return (string) $_SESSION['csrf_token'];
}

function require_valid_csrf(): void
{
    start_secure_session();
    $submitted = $_POST['csrf_token'] ?? '';
    $expected = $_SESSION['csrf_token'] ?? '';

    if (!is_string($submitted) || !is_string($expected) || $expected === '' || !hash_equals($expected, $submitted)) {
        http_response_code(419);
        exit('Sua sessão expirou. Volte à página anterior e tente novamente.');
    }
}

function require_post_request(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        header('Allow: POST');
        http_response_code(405);
        exit('Método não permitido.');
    }
}

function post_string(string $key, int $maxLength, int $minLength = 1): string
{
    $value = trim((string) ($_POST[$key] ?? ''));
    $length = strlen($value);

    if ($length < $minLength || $length > $maxLength) {
        throw new InvalidArgumentException('Campo inválido: ' . $key);
    }

    return $value;
}

function post_email(string $key = 'email'): string
{
    $value = strtolower(trim((string) ($_POST[$key] ?? '')));

    if (strlen($value) > 254 || filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
        throw new InvalidArgumentException('Email inválido.');
    }

    return $value;
}

function post_password(int $minimumLength = 1, string $key = 'password'): string
{
    $value = (string) ($_POST[$key] ?? '');
    $length = strlen($value);

    if ($length < $minimumLength || $length > 255 || trim($value) === '') {
        throw new InvalidArgumentException('Senha inválida.');
    }

    return $value;
}

function post_integer(string $key, int $minimum, int $maximum): int
{
    $value = filter_var($_POST[$key] ?? null, FILTER_VALIDATE_INT);

    if ($value === false || $value < $minimum || $value > $maximum) {
        throw new InvalidArgumentException('Campo numérico inválido: ' . $key);
    }

    return $value;
}

function post_iso_date(string $key): string
{
    $value = post_string($key, 10, 10);
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
    $errors = DateTimeImmutable::getLastErrors();

    if ($date === false || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
        throw new InvalidArgumentException('Data inválida.');
    }

    return $date->format('Y-m-d');
}

function flash_set(string $type, string $message): void
{
    start_secure_session();
    $_SESSION['flash_message'] = ['type' => $type, 'message' => $message];
}

function flash_take(): ?array
{
    start_secure_session();
    $flash = $_SESSION['flash_message'] ?? null;
    unset($_SESSION['flash_message']);

    return is_array($flash) ? $flash : null;
}

function render_flash_message(?array $flash): string
{
    if ($flash === null) {
        return '';
    }

    $type = ($flash['type'] ?? '') === 'success' ? 'success' : 'error';
    $message = escape_html((string) ($flash['message'] ?? ''));
    return '<p class="form-message form-message--' . $type . '" role="status">' . $message . '</p>';
}

function redirect_with_flash(string $location, string $type, string $message): never
{
    flash_set($type, $message);
    header('Location: ' . $location, true, 303);
    exit;
}

function require_authenticated_user(string $loginLocation = 'login.php'): int
{
    start_secure_session();
    $userId = filter_var($_SESSION['id_cadastro'] ?? null, FILTER_VALIDATE_INT);

    if ($userId === false || $userId < 1) {
        redirect_with_flash($loginLocation, 'error', 'Faça login para acessar esta área.');
    }

    return $userId;
}

function logout_user(): void
{
    start_secure_session();
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', (bool) $params['secure'], (bool) $params['httponly']);
    }

    session_destroy();
}

function is_unique_violation(PDOException $exception): bool
{
    $sqlState = (string) $exception->getCode();
    $driverCode = (string) ($exception->errorInfo[1] ?? '');
    return in_array($sqlState, ['23000', '23505'], true) || $driverCode === '1062';
}
