<?php
require_once 'jwt.php';

function require_auth(): array {
    $headers = getallheaders();

    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Token mancante']);
        exit;
    }

    if (!preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
        http_response_code(401);
        echo json_encode(['error' => 'Formato token non valido']);
        exit;
    }

    try {
        return jwt_decode($matches[1]);
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}
