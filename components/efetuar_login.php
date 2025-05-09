<?php
include('conexao.php');

$hidden = $_POST['hidden'];

if($hidden == 1) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cep = $_POST['cep'];
    $rua = $_POST['rua'];
    $city = $_POST['cidade'];
    $uf = $_POST['uf'];

   try {
    $sql = "INSERT INTO cadastro (name, email, password, cep, rua, cidade, uf) 
            VALUES ('$name', '$email', '$password', '$cep', '$rua', '$cidad', '$uf')";
    $pdo->exec($sql); // Executa a inserção

    header("Location: inicio.php");

   } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Código de erro para violação de índice único
            echo "Erro no cadastro: Email já cadastrado.";
        } else {
            echo "Erro no cadastro: " . $e->getMessage(); // Outros erros
        }
    }

} else {
    $email = $_POST['email'];
    $password = $_POST['senha'];

    $sql = "SELECT * FROM cadastro";

    $query = mysqli_query($conexao, $sql);

    if(mysqli_num_rows($query) > 0) {
        while(($cadastro = mysqli_fetch_assoc($query)) != NULL) {
            if((($email == $cadastro['email']) && ($email == "admin@admin.com")) && (($password == $cadastro['password']) && ($password == "123456789"))){
                setcookie('ADM', 1, time()+600);
                header("Location: area_admin.php");
            } else if(($email == $cadastro['email']) && ($password == $cadastro['password'])){
                setcookie('USER', $cadastro['id_cadastro'], time()+600);
                header("Location: inicio.php");
            }
        }
    }
}


?>