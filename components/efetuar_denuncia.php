<?php
include('conexao.php');

$hidden = $_POST['hidden'];

if($hidden == 1) {
    $titulo = $_POST['titulo'];
    $data_denuncia = $_POST['data_denuncia'];
    $descricao = $_POST['descricao'];

   try {
    $sql = "INSERT INTO denuncia (titulo, data_denuncia, descricao) 
            VALUES ('$titulo', '$data_denuncia', '$descricao')";
    $pdo->exec($sql); // Executa a inserção

    header("Location: certo2.html");

   } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Código de erro para violação de índice único
            echo "Erro no cadastro: Tem algo de errado com sua denuncia.";
        } else {
            echo "Erro no cadastro: " . $e->getMessage(); // Outros erros
        }
    }

}

?>