<?php

header('Content-Type: application/json');

require_once 'jwt.php';
require_once 'auth.php';
require_once 'users.php';
require_once 'database.php';
require_once 'utils.php';


$pdo = Database::getInstance()->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

$path = str_replace(getBasePath(), '', $path);
$parts = explode('/', $path);

/*
|--------------------------------------------------------------------------
| REGISTER
|--------------------------------------------------------------------------
*/
if ($path === 'register' && $method === 'POST') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        create_user($input);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Errore nel database durante la registrazione: ' . $e->getMessage()]);
    }
    exit;
}
/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/
if ($path === 'login' && $method === 'POST') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['username']) || empty($input['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Username e password richiesti']);
            exit();
        }

        $stmt = $pdo->prepare("SELECT * FROM utenti WHERE username = ?");
        $stmt->execute([$input['username']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Use password_verify to check the hashed password
        if ($user && password_verify($input['password'], $user['password'])) {
            echo json_encode([
                'token' => jwt_encode(['user_id' => $user['id'], 'role' => 'admin']) // Assuming a default role
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Credenziali non valide']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Errore nel database durante il login: ' . $e->getMessage()]);
    }
    exit();
}

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES
|--------------------------------------------------------------------------
*/

$user = require_auth();
/*
|--------------------------------------------------------------------------
| /users
|--------------------------------------------------------------------------
*/

if ($parts[0] === 'users') {

    // GET /users
    if ($method === 'GET' && count($parts) === 1) {
        echo json_encode(get_all_users());
        exit;
    }

    // POST /users
    if ($method === 'POST' && count($parts) === 1) {
        $data = json_decode(file_get_contents('php://input'), true);
        echo json_encode(create_user($data));
        exit;
    }

    // GET /users/{id}
    if ($method === 'GET' && count($parts) === 2) {
        $result = get_user((int)$parts[1]);
        if (!$result) {
            http_response_code(404);
            echo json_encode(['error' => 'Utente non trovato']);
        } else {
            echo json_encode($result);
        }
        exit;
    }

    // PUT /users/{id}
    if ($method === 'PUT' && count($parts) === 2) {
        $data = json_decode(file_get_contents('php://input'), true);
        $result = update_user((int)$parts[1], $data);
        if (!$result) {
            http_response_code(404);
            echo json_encode(['error' => 'Utente non trovato']);
        } else {
            echo json_encode($result);
        }
        exit;
    }

    // DELETE /users/{id}
    if ($method === 'DELETE' && count($parts) === 2) {
        if (delete_user((int)$parts[1])) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Utente non trovato']);
        }
        exit;
    }
}

/*
|--------------------------------------------------------------------------
| 404
|--------------------------------------------------------------------------
*/

http_response_code(404);
echo json_encode(['error' => 'Endpoint non valido']);
