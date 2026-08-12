<?php

declare(strict_types=1);

require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/Usuario.class.php';

$userId = require_authenticated_user('login.php');
$usuario = new Usuario($pdo);
$nomeUser = $usuario->findNameById($userId);

if ($nomeUser === null) {
    logout_user();
    redirect_with_flash('login.php', 'error', 'Sua conta não está mais disponível.');
}
