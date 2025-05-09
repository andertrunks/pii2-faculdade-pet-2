<?php
include('conexao.php');

$hidden = $_POST['hidden'];

if($hidden == 1) {
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $idade = $_POST['idade'];
    $profissao = $_POST['profissao'];
    $residencia = $_POST['residencia'];
    $espaco = $_POST['espaco'];
    $acordo = $_POST['acordo'];
    $animais = $_POST['animais'];
    $pq_animais = $_POST['pq_animais'];
    $tempo = $_POST['tempo'];
    $deseja_adotar = $_POST['deseja_adotar'];
    $ciente = $_POST['ciente'];

   try {
    $sql = "INSERT INTO adocao (nome, telefone, idade, profissao, residencia, espaco, acordo, animais, pq_animais, tempo, deseja_adotar, ciente) 
            VALUES ('$nome', '$telefone', '$idade', '$profissao', '$residencia', '$espaco', '$acordo', '$animais', '$pq_animais','$tempo', '$deseja_adotar', '$ciente')";
    $pdo->exec($sql); // Executa a inserção

    header("Location: certo3.html");

   } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Código de erro para violação de índice único
            echo "Erro no cadastro: Houve algo de errado com seu formulario.";
        } else {
            echo "Erro no cadastro: " . $e->getMessage(); // Outros erros
        }
    }

}

?>