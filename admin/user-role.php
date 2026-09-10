<?php
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify()) {
    header('Location: users.php?status=error&message=' . urlencode('Invalid request. Please try again.'));
    exit;
}

$id   = (int) ($_POST['id'] ?? 0);
$role = $_POST['role'] ?? '';

// Never let an admin change their own role through this form —
// that's handled by the "This is you" guard in the UI, but we
// double-check here too since this is a security boundary.
if ($id === (int) $_SESSION['user_id']) {
    header('Location: users.php?status=error&message=' . urlencode('You cannot change your own role.'));
    exit;
}

if ($id <= 0 || !in_array($role, ['admin', 'customer'], true)) {
    header('Location: users.php?status=error&message=' . urlencode('Invalid request.'));
    exit;
}

$stmt = $pdo->prepare('UPDATE users SET role = :role WHERE id = :id');
$stmt->bindValue(':role', $role);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();

header('Location: users.php?status=success&message=' . urlencode('User role updated.'));
exit;
