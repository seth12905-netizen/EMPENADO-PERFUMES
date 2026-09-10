<?php

session_start();

require 'database/config.php';
require 'validation.php';

if (!isset($_POST['login'])) {
    header('Location: login.php');
    exit;
}

$result = validateLoginInput($_POST);
$errors = $result['errors'];

if (!empty($errors)) {
    $message = implode(' ', $errors);
    header('Location: login.php?status=error&message=' . urlencode($message));
    exit;
}

try {
    $pdo = getConnection();

    $sql = "SELECT id, username, email, password, role FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':email', $result['data']['email']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($result['data']['password'], $user['password'])) {
        header('Location: login.php?status=error&message=' . urlencode('Incorrect email or password.'));
        exit;
    }

    // Regenerate the session id on privilege change (login) to prevent
    // session fixation attacks.
    session_regenerate_id(true);

    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role']     = $user['role'];

    header('Location: ' . ($user['role'] === 'admin' ? 'admin/index.php' : 'account.php'));
    exit;
} catch (PDOException $e) {
    header('Location: login.php?status=error&message=' . urlencode('Something went wrong. Please try again.'));
    exit;
}
