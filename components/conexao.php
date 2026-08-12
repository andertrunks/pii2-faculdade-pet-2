<?php

declare(strict_types=1);

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/database.php';

start_secure_session();
send_security_headers();

try {
    $pdo = create_database_connection();
} catch (Throwable $exception) {
    error_log('Falha na conexão com o banco: ' . $exception->getMessage());
    http_response_code(500);
    exit('Não foi possível conectar ao banco de dados.');
}
