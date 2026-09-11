<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require 'database/config.php';

try {
    $pdo = getConnection();

    $orderStmt = $pdo->prepare(
        "SELECT id, total, status, created_at
         FROM orders
         WHERE user_id = :user_id
         ORDER BY created_at DESC"
    );
    $orderStmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $orderStmt->execute();
    $orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);

    $itemsByOrder = [];
    if (!empty($orders)) {
        $orderIds = array_column($orders, 'id');
        $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
        $itemStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id IN ($placeholders)");
        $itemStmt->execute($orderIds);
        foreach ($itemStmt->fetchAll(PDO::FETCH_ASSOC) as $item) {
            $itemsByOrder[$item['order_id']][] = $item;
        }
    }
} catch (PDOException $e) {
    header('Location: account.php?status=error&message=' . urlencode('Something went wrong loading your orders. Please try again.'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders — EMPENADO</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="auth-body">

    <main class="auth-page orders-page">
        <div class="auth-card orders-card">

            <a href="index.php" class="auth-logo">
                <img src="images/logo.png" alt="EMPENADO logo" class="logo-mark">
                    <span class="auth-logo-text-col">
                    <span class="logo-text">EMPEN<span>A</span>DO</span>
                    <span class="logo-subtext">PERFUMES</span>
                </span>
            </a>

            <h1 class="auth-title">My Orders</h1>
            <p class="auth-subtitle">Everything you've ordered from us, most recent first.</p>

            <?php if (empty($orders)): ?>
                <p class="orders-empty">You haven't placed any orders yet.</p>
                <a href="index.php" class="btn btn-primary auth-submit" style="text-align:center;">Start Shopping</a>
            <?php else: ?>

                <div class="orders-list">
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <div class="order-card-head">
                                <div>
                                    <strong>Order #<?= (int) $order['id'] ?></strong>
                                    <span class="order-meta"><?= htmlspecialchars(date('M j, Y g:i A', strtotime($order['created_at']))) ?></span>
                                </div>
                                <span class="status-pill status-<?= htmlspecialchars($order['status']) ?>"><?= htmlspecialchars(ucfirst($order['status'])) ?></span>
                            </div>

                            <ul class="order-card-items">
                                <?php foreach ($itemsByOrder[$order['id']] ?? [] as $item): ?>
                                    <li>
                                        <span><?= (int) $item['quantity'] ?>× <?= htmlspecialchars($item['product_name']) ?></span>
                                        <span>₱<?= number_format((float) $item['subtotal'], 2) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <div class="order-card-total">
                                <span>Total</span>
                                <span>₱<?= number_format((float) $order['total'], 2) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php endif; ?>

            <a href="account.php" class="btn btn-outline auth-submit" style="text-align:center;">Back to Account</a>

        </div>
    </main>

</body>

</html>
