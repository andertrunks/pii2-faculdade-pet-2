<?php

declare(strict_types=1);

require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/Usuario.class.php';

require_post_request();
require_valid_csrf();

$lockedUntil = (int) ($_SESSION['login_locked_until'] ?? 0);
if ($lockedUntil > time()) {
    redirect_with_flash('login.php', 'error', 'Muitas tentativas. Aguarde alguns minutos antes de tentar novamente.');
}

try {
    $email = post_email();
    $password = post_password();
    $usuario = new Usuario($pdo);

    if ($usuario->login($email, $password)) {
        unset($_SESSION['login_failed_attempts'], $_SESSION['login_locked_until']);
        header('Location: inicio.php', true, 303);
        exit;
    }
} catch (InvalidArgumentException) {
    // A mesma mensagem genérica é usada abaixo para não revelar detalhes da conta.
} catch (PDOException $exception) {
    error_log('Erro no login: ' . $exception->getMessage());
    redirect_with_flash('login.php', 'error', 'Não foi possível entrar agora. Tente novamente em instantes.');
}

$attempts = (int) ($_SESSION['login_failed_attempts'] ?? 0) + 1;
$_SESSION['login_failed_attempts'] = $attempts;

if ($attempts >= 5) {
    $_SESSION['login_locked_until'] = time() + 300;
    $_SESSION['login_failed_attempts'] = 0;
}

redirect_with_flash('login.php', 'error', 'Email ou senha inválidos.');
