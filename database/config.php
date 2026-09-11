<?php

function getConnection(): PDO
{
    $host = 'localhost';
    $db   = 'empenado_perfumes'; // must match the database name you create
    $user = 'root';
    $pass = 'password123';

    
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $pdo;
}
