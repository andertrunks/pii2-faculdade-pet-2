<?php

declare(strict_types=1);

require_once __DIR__ . '/verifica.php';

require_post_request();
require_valid_csrf();

try {
    $titulo = post_string('titulo', 160, 5);
    $dataDenuncia = post_iso_date('data_denuncia');
    $descricao = post_string('descricao', 4000, 20);

    $statement = $pdo->prepare(
        'INSERT INTO denuncia (titulo, data_denuncia, descricao, user_id)
         VALUES (:titulo, :data_denuncia, :descricao, :user_id)'
    );
    $statement->execute([
        'titulo' => $titulo,
        'data_denuncia' => $dataDenuncia,
        'descricao' => $descricao,
        'user_id' => $userId,
    ]);

    header('Location: certo2.html', true, 303);
    exit;
} catch (InvalidArgumentException) {
    redirect_with_flash('denuncia.php', 'error', 'Informe um título, uma data válida e uma descrição detalhada.');
} catch (PDOException $exception) {
    error_log('Erro ao registrar denúncia: ' . $exception->getMessage());
    redirect_with_flash('denuncia.php', 'error', 'Não foi possível enviar a denúncia agora.');
}
