<?php
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método não permitido.');
}

$hidden = filter_input(INPUT_POST, 'hidden', FILTER_VALIDATE_INT);
if ($hidden !== 1) {
    http_response_code(400);
    exit('Operação inválida.');
}

$name = trim($_POST['name'] ?? '');
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';
$cep = trim($_POST['cep'] ?? '');
$rua = trim($_POST['rua'] ?? '');
$cidade = trim($_POST['cidade'] ?? '');
$uf = strtoupper(trim($_POST['uf'] ?? ''));

if ($name === '' || $email === false || strlen($password) < 8) {
    http_response_code(422);
    exit('Preencha os dados corretamente. A senha deve ter pelo menos 8 caracteres.');
}

try {
    $sql = $pdo->prepare(
        'INSERT INTO cadastro (name, email, password, cep, rua, cidade, uf)
         VALUES (:name, :email, :password, :cep, :rua, :cidade, :uf)'
    );
    $sql->execute([
        'name' => $name,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'cep' => $cep,
        'rua' => $rua,
        'cidade' => $cidade,
        'uf' => $uf,
    ]);
    header('Location: inicio.php');
    exit;
} catch (PDOException $e) {
    if ((string) $e->getCode() === '23000') {
        http_response_code(409);
        exit('Erro no cadastro: email já cadastrado.');
    }
    error_log('Erro no cadastro: ' . $e->getMessage());
    http_response_code(500);
    exit('Não foi possível concluir o cadastro.');
}
