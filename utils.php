<?php

function dd($msg){
    var_dump($msg);
    die();
}

function getBasePath() {

    $site = 'api-jwt/';

    return $site;
}
