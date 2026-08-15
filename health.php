<?php

declare(strict_types=1);

require_once __DIR__ . '/components/database.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

try {
    $pdo = create_database_connection();
    $pdo->query('SELECT 1');
    $revision = trim((string) (getenv('RENDER_GIT_COMMIT') ?: getenv('APP_REVISION') ?: 'unknown'));
    echo json_encode([
        'status' => 'ok',
        'revision' => $revision,
    ], JSON_THROW_ON_ERROR);
} catch (Throwable $exception) {
    error_log('Falha no health check: ' . $exception->getMessage());
    http_response_code(503);
    echo json_encode(['status' => 'unavailable']);
}

