<?php

function getConnection(): PDO
{
    $host = 'localhost';
    $db   = 'empenado_perfumes'; // must match the database name you create
    $user = 'root';
    $pass = 'password123';

    // Note: no try/catch here on purpose. Pages that call getConnection()
    // (login-function.php, get-products.php, etc.) already wrap their own
    // logic in try/catch (PDOException $e) and turn a failure into a
    // proper HTML message or JSON error response. If we caught the
    // exception here and called die(), it would print plain text instead
    // and break every JSON endpoint (get-products.php, get-collections.php,
    // checkout-function.php), which is exactly what caused the
    // "Couldn't load products" message you saw.
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $pdo;
}
