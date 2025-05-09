<?php

if(isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['password']) && !empty($_POST['password'])) {
    
    require 'conexao.php';
    require 'Usuario.class.php';

    $u = new Usuario();

    $email = addslashes($_POST['email']);
    $password = addslashes($_POST['password']);

    if($u->login($email, $password) ==  true) {
      header("Location: inicio.php");
    } else {
      header("Location: login.php");
    }
}

?>