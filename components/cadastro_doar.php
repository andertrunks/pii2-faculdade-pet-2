<?php

declare(strict_types=1);

require_once __DIR__ . '/verifica.php';

require_post_request();
require_valid_csrf();

try {
    $nomePet = post_string('nome_pet', 120, 1);
    $idadePet = post_string('idade_pet', 40, 1);
    $nome = post_string('nome', 120, 2);
    $telefone = preg_replace('/\D+/', '', post_string('telefone', 25, 8));
    $email = post_email();
    $cep = preg_replace('/\D+/', '', post_string('cep', 12, 8));
    $cidade = post_string('cidade', 120, 2);
    $uf = strtoupper(post_string('uf', 2, 2));
    $sobre = post_string('sobre', 2000, 10);

    if (strlen($telefone) < 10 || strlen($telefone) > 15 || strlen($cep) !== 8 || !preg_match('/^[A-Z]{2}$/', $uf)) {
        throw new InvalidArgumentException('Dados de contato inválidos.');
    }

    $statement = $pdo->prepare(
        'INSERT INTO doar (nome_pet, idade_pet, nome, telefone, email, cep, cidade, uf, sobre)
         VALUES (:nome_pet, :idade_pet, :nome, :telefone, :email, :cep, :cidade, :uf, :sobre)'
    );
    $statement->execute([
        'nome_pet' => $nomePet,
        'idade_pet' => $idadePet,
        'nome' => $nome,
        'telefone' => $telefone,
        'email' => $email,
        'cep' => $cep,
        'cidade' => $cidade,
        'uf' => $uf,
        'sobre' => $sobre,
    ]);

    header('Location: certo.html', true, 303);
    exit;
} catch (InvalidArgumentException) {
    redirect_with_flash('doar.php', 'error', 'Revise os dados do animal e as informações de contato.');
} catch (PDOException $exception) {
    error_log('Erro ao cadastrar doação: ' . $exception->getMessage());
    redirect_with_flash('doar.php', 'error', 'Não foi possível enviar o cadastro agora.');
}
