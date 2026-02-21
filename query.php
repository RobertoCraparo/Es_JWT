<?php

require_once "database.php";

$pdo = Database::getInstance()->getConnection();
//query inserimento utente

function insert(string $username, string $password){

    global $pdo;

    $stmt = $pdo->prepare("insert into utenti(username, password) values (?,?)");
    $stmt -> bindParam(1,$username, PDO::PARAM_STR);
    $stmt -> bindParam(4,$password, PDO::PARAM_STR);
    $stmt -> execute();
}

?>