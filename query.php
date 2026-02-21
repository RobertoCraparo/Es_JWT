<?php

require_once "database.php";

$pdo = Database::getInstance()->getConnection();



//query inserimento untente

function insert(string $nome, string $cognome, string $email, string $password){

    global $pdo;

    $stmt = $pdo->prepare("insert into utenti(id, username, password) values (null, ?,?)");
    $stmt -> bindParam(1,$username, PDO::PARAM_STR);
    $stmt -> bindParam(4,$password, PDO::PARAM_STR);
    $stmt -> execute();
}

function recupero_task(string $username){
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM task t join utenti u on t.id_utente=u.id
                                group by u.id having u.username=?");
    $stmt -> bindParam(1,$username, PDO::PARAM_STR);
    $stmt -> execute();
    $result = $stmt->fetchall();
}

?>