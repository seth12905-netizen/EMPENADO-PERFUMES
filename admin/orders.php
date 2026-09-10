<?php
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Orders';
$activeNav = 'orders';

$statusFilter = $_GET['filter'] ?? 'all';
$validStatuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

$sql = "SELECT o.id, o.total, o.status, o.created_at, u.username, u.email
        FROM orders o
        JOIN users u ON u.id = o.user_id";

if (in_array($statusFilter, $validStatuses, true)) {
    $sql .= " WHERE o.status = :status";
}
$sql .= " ORDER BY o.created_at DESC";

$stmt = $pdo->prepare($sql);
if (in_array($statusFilter, $validStatuses, true)) {
    $stmt->bindValue(':status', $statusFilter);
}
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pull all items for the orders on this page in one query, then group in PHP.
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

require __DIR__ . '/includes/header.php';
?>

<section class="admin-panel">

    <div class="admin-panel-head">
        <h2>All Orders (<?= count($orders) ?>)</h2>
        <div class="admin-filter-tabs">
            <a href="orders.php" class="<?= $statusFilter === 'all' ? 'is-active' : '' ?>">All</a>
            <?php foreach ($validStatuses as $s): ?>
                <a href="orders.php?filter=<?= $s ?>" class="<?= $statusFilter === $s ? 'is-active' : '' ?>"><?= ucfirst($s) ?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (empty($orders)): ?>
        <p class="admin-empty">No orders match this filter.</p>
    <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?= (int) $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['username']) ?><br><small class="admin-muted"><?= htmlspecialchars($order['email']) ?></small></td>
                            <td>
                                <ul class="admin-order-items">
                                    <?php foreach ($itemsByOrder[$order['id']] ?? [] as $item): ?>
                                        <li><?= (int) $item['quantity'] ?>× <?= htmlspecialchars($item['product_name']) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                            <td>₱<?= number_format((float) $order['total'], 2) ?></td>
                            <td><?= htmlspecialchars(date('M j, Y g:i A', strtotime($order['created_at']))) ?></td>
                            <td>
                                <form method="POST" action="order-status.php" class="admin-inline-form">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $order['id'] ?>">
                                    <input type="hidden" name="filter" value="<?= htmlspecialchars($statusFilter) ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <?php foreach ($validStatuses as $s): ?>
                                            <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
