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

    $input = json_decode(file_get_contents('php://input'), true);

    $stmt = $pdo->prepare("insert into utenti(username, password) values (?,?)");
    $stmt->bindParam(1, $input['username']);
    $stmt->bindParam(2, $input['password']);
    $stmt->execute();

    echo json_encode(['success' => true]);
    exit;
}
/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/
if ($path === 'login' && $method === 'POST') {

    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['username']) && isset($input['password'])) {
        $stmt = $pdo->prepare("select * from utenti where username = ? and password = ?");
        $stmt->execute([$input['username'], $input['password']]);
        $user = $stmt->fetch();

        if ($user) {
            echo json_encode([
                'token' => jwt_encode(['user_id' => $user['id'], 'role' => $user['role']])
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Credenziali non valide']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Username e password richiesti']);
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
