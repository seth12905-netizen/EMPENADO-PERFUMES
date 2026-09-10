<?php

require 'database/config.php';
require 'validation.php';

if (!isset($_POST['register'])) {
    header('Location: register.php');
    exit;
}

$result = validateRegisterInput($_POST);
$errors = $result['errors'];

if (!empty($errors)) {
    $message = implode(' ', $errors);
    header('Location: register.php?status=error&message=' . urlencode($message));
    exit;
}

try {
    $pdo = getConnection();

    $sql = "INSERT INTO users (username, email, phone, password)
            VALUES (:username, :email, :phone, :password)";

    $hashedPassword = password_hash($result['data']['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':username', $result['data']['username']);
    $stmt->bindValue(':email', $result['data']['email']);
    $stmt->bindValue(':phone', $result['data']['phone']);
    $stmt->bindValue(':password', $hashedPassword);
    $stmt->execute();

    header('Location: login.php?status=success&message=' . urlencode('Account created! Please log in.'));
    exit;
} catch (PDOException $e) {
    if ((int) $e->getCode() === 23000) {
        $message = 'That username or email is already registered.';
    } else {
        $message = 'Something went wrong. Please try again.';
    }
    header('Location: register.php?status=error&message=' . urlencode($message));
    exit;
}
