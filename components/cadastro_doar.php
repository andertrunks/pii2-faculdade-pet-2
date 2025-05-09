<?php
include('conexao.php');

$hidden = $_POST['hidden'];

if($hidden == 1) {
    $nome_pet = $_POST['nome_pet'];
    $idade_pet = $_POST['idade_pet'];
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $cep = $_POST['cep'];
    $cidade = $_POST['cidade'];
    $uf = $_POST['uf'];
    $sobre =$_POST['sobre'];

   try {
    $sql = "INSERT INTO doar (nome_pet, idade_pet, nome, telefone, email, cep, cidade, uf, sobre) 
            VALUES ('$nome_pet', '$idade_pet', '$nome', '$telefone', '$email', '$cep', '$cidade', '$uf', '$sobre')";
    $pdo->exec($sql); // Executa a inserção

    header("Location: certo.html");

   } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Código de erro para violação de índice único
            echo "Erro no cadastro: Animal já cadastrado.";
        } else {
            echo "Erro no cadastro: " . $e->getMessage(); // Outros erros
        }
    }

}

?>