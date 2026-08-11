<?php

declare(strict_types=1);

final class Usuario
{
    public function __construct(private PDO $pdo)
    {
    }

    public function login(string $email, string $password): bool
    {
        $statement = $this->pdo->prepare(
            'SELECT id_cadastro, password FROM cadastro WHERE email = :email LIMIT 1'
        );
        $statement->execute(['email' => strtolower(trim($email))]);
        $user = $statement->fetch();

        if (!$user) {
            return false;
        }

        $storedPassword = (string) $user['password'];
        $passwordInfo = password_get_info($storedPassword);
        $isHash = ($passwordInfo['algoName'] ?? 'unknown') !== 'unknown';
        $isValid = $isHash
            ? password_verify($password, $storedPassword)
            : hash_equals($storedPassword, $password);

        if (!$isValid) {
            return false;
        }

        if (!$isHash || password_needs_rehash($storedPassword, PASSWORD_DEFAULT)) {
            $update = $this->pdo->prepare(
                'UPDATE cadastro SET password = :password WHERE id_cadastro = :id'
            );
            $update->execute([
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'id' => $user['id_cadastro'],
            ]);
        }

        session_regenerate_id(true);
        $_SESSION['id_cadastro'] = (int) $user['id_cadastro'];
        return true;
    }

    public function findNameById(int $userId): ?string
    {
        $statement = $this->pdo->prepare(
            'SELECT name FROM cadastro WHERE id_cadastro = :id_cadastro'
        );
        $statement->execute(['id_cadastro' => $userId]);
        $name = $statement->fetchColumn();

        return $name === false ? null : (string) $name;
    }
}
