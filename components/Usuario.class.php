<?php

class Usuario {

  public function login($email, $password){
    global $pdo;

    $sql = "SELECT * FROM cadastro WHERE email = :email AND password = :password";
    $sql = $pdo->prepare($sql);
    $sql->bindValue("email", $email);
    $sql->bindValue("password", $password); 
    $sql->execute();

    if($sql->rowCount() > 0){
        $dado = $sql->fetch();

        $_SESSION['id_cadastro'] = $dado['id_cadastro'];

        return true;
    }else{
        return false;
    }
  }

  public function logado($cod){
      global $pdo;

      $array = array();

      $sql = "SELECT name FROM cadastro WHERE id_cadastro = :id_cadastro";
      $sql = $pdo->prepare($sql);
      $sql->bindValue("id_cadastro", $cod);
      $sql->execute();

      if($sql->rowCount() > 0){
          $array = $sql->fetch();
      }

      return $array;
  }
}

?>