<?php

require_once "database.php";

$pdo = Database::getInstance()->getConnection();



//query inserimento untente

function insert(string $nome, string $cognome, string $email, string $password){

    global $pdo;

    $stmt = $pdo->prepare("insert into utenti(id, nome, cognome, email, password) values (null, ?,?,?,?)");
    $stmt -> bindParam(1,$nome, PDO::PARAM_STR);
    $stmt -> bindParam(2,$cognome, PDO::PARAM_STR);
    $stmt -> bindParam(3,$email, PDO::PARAM_STR);
    $stmt -> bindParam(4,$password, PDO::PARAM_STR);
    $stmt -> execute();
}

?>