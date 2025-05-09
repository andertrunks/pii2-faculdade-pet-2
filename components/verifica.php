<?php

require "conexao.php";

if(isset($_SESSION['id_cadastro']) && !empty($_SESSION['id_cadastro'])){

    require_once 'Usuario.class.php';
    $u = new Usuario();

    $listLogado = $u->logado($_SESSION['id_cadastro']);
    $nomeUser = $listLogado['name'];

}else{
    header("Location: login.php");
}

?>