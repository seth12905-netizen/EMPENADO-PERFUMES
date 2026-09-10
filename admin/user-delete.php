<?php
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify()) {
    header('Location: users.php?status=error&message=' . urlencode('Invalid request. Please try again.'));
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

if ($id === (int) $_SESSION['user_id']) {
    header('Location: users.php?status=error&message=' . urlencode('You cannot delete your own account.'));
    exit;
}

if ($id <= 0) {
    header('Location: users.php?status=error&message=' . urlencode('Invalid user.'));
    exit;
}

try {
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    header('Location: users.php?status=success&message=' . urlencode('User deleted.'));
    exit;
} catch (PDOException $e) {
    // The users -> orders foreign key has no ON DELETE CASCADE on
    // purpose, so order history isn't silently wiped out. Tell the
    // admin why the delete was blocked instead of failing silently.
    $message = ((int) $e->getCode() === 23000)
        ? 'This user has existing orders and cannot be deleted. Cancel or reassign their orders first.'
        : 'Something went wrong deleting this user.';
    header('Location: users.php?status=error&message=' . urlencode($message));
    exit;
}
