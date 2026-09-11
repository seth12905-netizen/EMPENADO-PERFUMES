<?php
require_once __DIR__ . '/includes/auth.php';

$validStatuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];
$filter = $_POST['filter'] ?? 'all';
$backTo = ($filter !== 'all' && in_array($filter, $validStatuses, true))
    ? 'orders.php?filter=' . urlencode($filter)
    : 'orders.php';

// Use '?' to start the query string if $backTo has none yet, otherwise '&'
$sep = (strpos($backTo, '?') === false) ? '?' : '&';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify()) {
    header('Location: ' . $backTo . $sep . 'status=error&message=' . urlencode('Invalid request. Please try again.'));
    exit;
}

$id     = (int) ($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';

if ($id <= 0 || !in_array($status, $validStatuses, true)) {
    header('Location: ' . $backTo . $sep . 'status=error&message=' . urlencode('Invalid order or status.'));
    exit;
}

$stmt = $pdo->prepare('UPDATE orders SET status = :status WHERE id = :id');
$stmt->bindValue(':status', $status);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();

header('Location: ' . $backTo . $sep . 'status=success&message=' . urlencode("Order #{$id} marked as " . $status . "."));
exit;