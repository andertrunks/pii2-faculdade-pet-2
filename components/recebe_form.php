<?php

declare(strict_types=1);

require_once __DIR__ . '/verifica.php';

require_post_request();
require_valid_csrf();

try {
    $nome = post_string('nome', 120, 2);
    $telefone = preg_replace('/\D+/', '', post_string('telefone', 25, 8));
    $idade = post_integer('idade', 18, 120);
    $profissao = post_string('profissao', 120, 2);
    $residencia = post_string('residencia', 120, 2);
    $espaco = post_string('espaco', 500, 2);
    $acordo = post_string('acordo', 500, 2);
    $animais = post_string('animais', 500, 2);
    $pqAnimais = post_string('pq_animais', 1000, 2);
    $tempo = post_string('tempo', 500, 2);
    $desejaAdotar = post_string('deseja_adotar', 1000, 5);
    $ciente = post_string('ciente', 500, 2);

    if (strlen($telefone) < 10 || strlen($telefone) > 15) {
        throw new InvalidArgumentException('Telefone inválido.');
    }

    $statement = $pdo->prepare(
        'INSERT INTO adocao
         (nome, telefone, idade, profissao, residencia, espaco, acordo, animais, pq_animais, tempo, deseja_adotar, ciente)
         VALUES
         (:nome, :telefone, :idade, :profissao, :residencia, :espaco, :acordo, :animais, :pq_animais, :tempo, :deseja_adotar, :ciente)'
    );
    $statement->execute([
        'nome' => $nome,
        'telefone' => $telefone,
        'idade' => $idade,
        'profissao' => $profissao,
        'residencia' => $residencia,
        'espaco' => $espaco,
        'acordo' => $acordo,
        'animais' => $animais,
        'pq_animais' => $pqAnimais,
        'tempo' => $tempo,
        'deseja_adotar' => $desejaAdotar,
        'ciente' => $ciente,
    ]);

    header('Location: certo3.html', true, 303);
    exit;
} catch (InvalidArgumentException) {
    redirect_with_flash('formulario_adocao.php', 'error', 'Preencha todos os campos com informações válidas.');
} catch (PDOException $exception) {
    error_log('Erro ao registrar adoção: ' . $exception->getMessage());
    redirect_with_flash('formulario_adocao.php', 'error', 'Não foi possível enviar o formulário agora.');
}
