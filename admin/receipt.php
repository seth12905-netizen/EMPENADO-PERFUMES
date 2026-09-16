<?php
require_once __DIR__ . '/includes/auth.php';

$orderId = (int) ($_GET['id'] ?? 0);

if ($orderId <= 0) {
    header('Location: orders.php');
    exit;
}

$orderStmt = $pdo->prepare(
    "SELECT o.id, o.total, o.payment_method, o.payment_reference, o.status, o.created_at,
            u.username, u.email, u.phone
     FROM orders o
     JOIN users u ON u.id = o.user_id
     WHERE o.id = :id"
);
$orderStmt->bindValue(':id', $orderId, PDO::PARAM_INT);
$orderStmt->execute();
$order = $orderStmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: orders.php?status=error&message=' . urlencode('That order could not be found.'));
    exit;
}

$itemStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = :id ORDER BY id ASC");
$itemStmt->bindValue(':id', $orderId, PDO::PARAM_INT);
$itemStmt->execute();
$items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

$paymentMethodLabels = [
    'cod'   => 'Cash on Delivery',
    'gcash' => 'GCash',
    'card'  => 'Credit / Debit Card',
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt — Order #<?= (int) $order['id'] ?> — EMPENADO Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="receipt-body">

    <main class="receipt-page">
        <div class="receipt-toolbar no-print">
            <a href="orders.php" class="receipt-toolbar-back">Back to Orders</a>
            <div class="receipt-toolbar-actions">
                <button type="button" class="btn btn-primary" onclick="window.print()">&#128438; Print Receipt</button>
            </div>
        </div>

        <div class="receipt-paper">

            <div class="receipt-head">
                <div class="receipt-brand">
                    <img src="../images/logo.png" alt="EMPENADO logo" class="receipt-logo">
                    <div class="receipt-brand-text">
                        <span class="receipt-brand-name">EMPEN<span>A</span>DO</span>
                        <span class="receipt-brand-sub">PERFUMES</span>
                    </div>
                </div>
                <div class="receipt-head-meta">
                    <h1>Official Receipt</h1>
                    <span class="status-pill status-<?= htmlspecialchars($order['status']) ?>"><?= htmlspecialchars(ucfirst($order['status'])) ?></span>
                    <span class="receipt-admin-tag">Admin Copy</span>
                </div>
            </div>

            <div class="receipt-info-grid">
                <div>
                    <span class="receipt-label">Order No.</span>
                    <span class="receipt-value">#<?= (int) $order['id'] ?></span>
                </div>
                <div>
                    <span class="receipt-label">Date Issued</span>
                    <span class="receipt-value"><?= htmlspecialchars(date('M j, Y g:i A', strtotime($order['created_at']))) ?></span>
                </div>
                <div>
                    <span class="receipt-label">Payment Method</span>
                    <span class="receipt-value"><?= htmlspecialchars($paymentMethodLabels[$order['payment_method']] ?? ucfirst($order['payment_method'])) ?></span>
                </div>
                <div>
                    <span class="receipt-label">Payment Reference</span>
                    <span class="receipt-value"><?= !empty($order['payment_reference']) ? htmlspecialchars($order['payment_reference']) : '—' ?></span>
                </div>
            </div>

            <div class="receipt-divider"></div>

            <div class="receipt-info-grid">
                <div>
                    <span class="receipt-label">Customer</span>
                    <span class="receipt-value"><?= htmlspecialchars($order['username']) ?></span>
                </div>
                <div>
                    <span class="receipt-label">Email</span>
                    <span class="receipt-value"><?= htmlspecialchars($order['email']) ?></span>
                </div>
                <div>
                    <span class="receipt-label">Phone</span>
                    <span class="receipt-value"><?= htmlspecialchars($order['phone']) ?></span>
                </div>
            </div>

            <table class="receipt-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th class="receipt-num">Qty</th>
                        <th class="receipt-num">Price</th>
                        <th class="receipt-num">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td class="receipt-num"><?= (int) $item['quantity'] ?></td>
                            <td class="receipt-num">₱<?= number_format((float) $item['price'], 2) ?></td>
                            <td class="receipt-num">₱<?= number_format((float) $item['subtotal'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="receipt-total-row">
                <span>Total Paid</span>
                <span>₱<?= number_format((float) $order['total'], 2) ?></span>
            </div>

            <?php if ($order['payment_method'] === 'gcash' && !empty($order['payment_reference'])): ?>
                <p class="receipt-note">GCash reference <strong><?= htmlspecialchars($order['payment_reference']) ?></strong> — verify this against the GCash payment received before marking the order as processing/shipped.</p>
            <?php endif; ?>

            <div class="receipt-footer">
                <p>Issued by <?= htmlspecialchars($currentAdminName) ?> — EMPENADO Perfumes Admin</p>
                <p class="receipt-footer-small">This receipt was generated on <?= htmlspecialchars(date('M j, Y g:i A')) ?> and reflects the order details on file.</p>
            </div>

        </div>
    </main>

</body>

</html>
