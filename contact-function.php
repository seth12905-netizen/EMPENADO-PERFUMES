<?php

require 'database/config.php';
require 'validation.php';

if (!isset($_POST['contact_submit'])) {
    header('Location: index.php#contact');
    exit;
}

$result = validateContactInput($_POST);
$errors = $result['errors'];

if (!empty($errors)) {
    $message = implode(' ', $errors);
    header('Location: index.php?status=error&message=' . urlencode($message) . '#contact');
    exit;
}

try {
    $pdo = getConnection();

    $sql = "INSERT INTO messages (name, email, message)
            VALUES (:name, :email, :message)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':name', $result['data']['name']);
    $stmt->bindValue(':email', $result['data']['email']);
    $stmt->bindValue(':message', $result['data']['message']);
    $stmt->execute();

    header('Location: index.php?status=success&message=' . urlencode("Thank you, your message has been sent. We'll reply within 1 business day.") . '#contact');
    exit;
} catch (PDOException $e) {
    $message = 'Something went wrong. Please try again.';
    header('Location: index.php?status=error&message=' . urlencode($message) . '#contact');
    exit;
}
