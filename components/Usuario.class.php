<?php

class Usuario
{
    public function login($email, $password)
    {
        global $pdo;

        $sql = $pdo->prepare('SELECT id_cadastro, password FROM cadastro WHERE email = :email LIMIT 1');
        $sql->execute(['email' => $email]);
        $dado = $sql->fetch(PDO::FETCH_ASSOC);

        if (!$dado) {
            return false;
        }

        $hash = (string) $dado['password'];
        $senhaValida = password_verify($password, $hash);

        // Migra de forma transparente os cadastros antigos que usavam texto puro.
        if (!$senhaValida && hash_equals($hash, (string) $password)) {
            $senhaValida = true;
            $update = $pdo->prepare('UPDATE cadastro SET password = :password WHERE id_cadastro = :id');
            $update->execute([
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'id' => $dado['id_cadastro'],
            ]);
        }

        if (!$senhaValida) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['id_cadastro'] = $dado['id_cadastro'];
        return true;
    }

    public function logado($cod)
    {
        global $pdo;

        $sql = $pdo->prepare('SELECT name FROM cadastro WHERE id_cadastro = :id_cadastro');
        $sql->execute(['id_cadastro' => $cod]);
        return $sql->fetch(PDO::FETCH_ASSOC) ?: [];
    }
}
