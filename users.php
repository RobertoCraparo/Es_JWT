<?php

function get_all_users() {
    try {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->query("SELECT id, username FROM utenti");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Errore nel database: ' . $e->getMessage()]);
        exit;
    }
}

function get_user($id) {
    try {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT id, username FROM utenti WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Errore nel database: ' . $e->getMessage()]);
        exit;
    }
}

function create_user($data) {
    try {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("INSERT INTO utenti (username, password) VALUES (?, ?)");
        $stmt->execute([$data['username'], password_hash($data['password'], PASSWORD_DEFAULT)]);
        $id = $pdo->lastInsertId();
        return get_user($id);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Errore nel database: ' . $e->getMessage()]);
        exit;
    }
}

function update_user($id, $data) {
    try {
        $pdo = Database::getInstance()->getConnection();
        $fields = [];
        $params = [];
        if (isset($data['username'])) {
            $fields[] = 'username = ?';
            $params[] = $data['username'];
        }
        if (isset($data['password'])) {
            $fields[] = 'password = ?';
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        if (empty($fields)) {
            return get_user($id);
        }
        $params[] = $id;
        $stmt = $pdo->prepare("UPDATE utenti SET " . implode(', ', $fields) . " WHERE id = ?");
        $stmt->execute($params);
        return get_user($id);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Errore nel database: ' . $e->getMessage()]);
        exit;
    }
}

function delete_user($id) {
    try {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("DELETE FROM utenti WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Errore nel database: ' . $e->getMessage()]);
        exit;
    }
}
