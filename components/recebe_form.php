<?php

declare(strict_types=1);

require_once __DIR__ . '/verifica.php';

require_post_request();
require_valid_csrf();

$animalId = filter_var($_POST['animal_id'] ?? null, FILTER_VALIDATE_INT);
$formLocation = $animalId !== false && $animalId > 0
    ? 'formulario_adocao.php?animal_id=' . $animalId
    : 'adote.php';

try {
    if ($animalId === false || $animalId < 1) {
        throw new InvalidArgumentException('Animal inválido.');
    }

    $animalStatement = $pdo->prepare("SELECT id_animal FROM animais WHERE id_animal = :animal_id AND status = 'disponivel'");
    $animalStatement->execute(['animal_id' => $animalId]);
    if ($animalStatement->fetchColumn() === false) {
        throw new InvalidArgumentException('Animal indisponível.');
    }

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
         (user_id, animal_id, nome, telefone, idade, profissao, residencia, espaco, acordo, animais, pq_animais, tempo, deseja_adotar, ciente)
         VALUES
         (:user_id, :animal_id, :nome, :telefone, :idade, :profissao, :residencia, :espaco, :acordo, :animais, :pq_animais, :tempo, :deseja_adotar, :ciente)'
    );
    $statement->execute([
        'user_id' => $userId,
        'animal_id' => $animalId,
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
    redirect_with_flash($formLocation, 'error', 'Selecione um animal disponível e preencha todos os campos com informações válidas.');
} catch (PDOException $exception) {
    if (is_unique_violation($exception)) {
        redirect_with_flash($formLocation, 'error', 'Você já enviou uma solicitação para este animal.');
    }

    error_log('Erro ao registrar adoção: ' . $exception->getMessage());
    redirect_with_flash($formLocation, 'error', 'Não foi possível enviar o formulário agora.');
}
