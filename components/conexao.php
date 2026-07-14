<?php
session_start();

/*  ↓↓↓  pegue os dados do ambiente; se não existirem, use valores padrão só para testes locais */
$user     = getenv('DB_USER')     ?: 'root';
$pass     = getenv('DB_PASS')     ?: '';
$database = getenv('DB_NAME')     ?: 'adota-pet';
$localhost= getenv('DB_HOST')     ?: 'localhost';

global $pdo;

try {
    $pdo = new PDO("mysql:dbname={$database};host={$localhost}", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log('Falha na conexão com o banco: ' . $e->getMessage());
    http_response_code(500);
    exit('Não foi possível conectar ao banco de dados.');
}
