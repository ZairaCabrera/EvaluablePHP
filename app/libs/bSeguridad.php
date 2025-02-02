<?php

function encriptar($password, $cost=10) {
    $password = trim($password);
    return password_hash($password, PASSWORD_DEFAULT, ['cost' => $cost]);
}

function comprobarhash($pass, $passBD) {
    $pass = trim($pass);
    return password_verify($pass, $passBD) ;
}
?>
