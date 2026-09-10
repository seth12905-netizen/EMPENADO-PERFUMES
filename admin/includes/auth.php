<?php

/**
 * Include this at the very top of every /admin/*.php page (before any
 * HTML is echoed). It makes sure the visitor is logged in AND has the
 * 'admin' role, otherwise it bounces them away.
 */

session_start();

require_once __DIR__ . '/../../database/config.php';
require_once __DIR__ . '/csrf.php';

// Not logged in at all -> send to the normal login page.
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php?status=error&message=' . urlencode('Please log in to continue.'));
    exit;
}

// Logged in, but role wasn't cached in session (e.g. older session
// from before this feature existed) -> look it up once and cache it.
if (!isset($_SESSION['role'])) {
    $pdo  = getConnection();
    $stmt = $pdo->prepare('SELECT role FROM users WHERE id = :id');
    $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['role'] = $row['role'] ?? 'customer';
}

// Logged in, but not an admin -> send back to the shop, not the panel.
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../index.php?status=error&message=' . urlencode('You do not have access to the admin dashboard.'));
    exit;
}

$pdo = getConnection();
$currentAdminName = $_SESSION['username'] ?? 'Admin';
