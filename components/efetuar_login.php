<?php

declare(strict_types=1);

require_once __DIR__ . '/conexao.php';

require_post_request();
require_valid_csrf();

try {
    if (filter_var($_POST['hidden'] ?? null, FILTER_VALIDATE_INT) !== 1) {
        throw new InvalidArgumentException('Operação inválida.');
    }

    $name = post_string('name', 120, 2);
    $email = post_email();
    $password = post_password(8);
    $cep = preg_replace('/\D+/', '', post_string('cep', 12, 8));
    $rua = post_string('rua', 160, 2);
    $cidade = post_string('cidade', 120, 2);
    $uf = strtoupper(post_string('uf', 2, 2));

    if (strlen($cep) !== 8 || !preg_match('/^[A-Z]{2}$/', $uf)) {
        throw new InvalidArgumentException('Dados de cadastro inválidos.');
    }

    $statement = $pdo->prepare(
        'INSERT INTO cadastro (name, email, password, cep, rua, cidade, uf)
         VALUES (:name, :email, :password, :cep, :rua, :cidade, :uf)'
    );
    $statement->execute([
        'name' => $name,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'cep' => $cep,
        'rua' => $rua,
        'cidade' => $cidade,
        'uf' => $uf,
    ]);

    redirect_with_flash('login.php', 'success', 'Cadastro concluído. Entre com seu email e senha.');
} catch (InvalidArgumentException) {
    redirect_with_flash('login.php', 'error', 'Revise os campos. Use um email válido e uma senha com pelo menos 8 caracteres.');
} catch (PDOException $exception) {
    if (is_unique_violation($exception)) {
        redirect_with_flash('login.php', 'error', 'Este email já está cadastrado.');
    }

    error_log('Erro no cadastro: ' . $exception->getMessage());
    redirect_with_flash('login.php', 'error', 'Não foi possível concluir o cadastro agora.');
}
