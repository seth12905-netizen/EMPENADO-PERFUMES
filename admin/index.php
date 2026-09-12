<?php
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Dashboard';
$activeNav = 'dashboard';

$totalUsers    = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$totalOrders   = (int) $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$pendingOrders = (int) $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$totalRevenue  = (float) $pdo->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status != 'cancelled'")->fetchColumn();
$unreadMsgs    = (int) $pdo->query('SELECT COUNT(*) FROM messages WHERE is_read = 0')->fetchColumn();
$lowStockCount = (int) $pdo->query(
    '(SELECT id FROM products WHERE stock <= 5) UNION ALL (SELECT id FROM collections WHERE stock <= 5)'
)->rowCount();

$recentOrders = $pdo->query(
    "SELECT o.id, o.total, o.status, o.created_at, u.username
     FROM orders o
     JOIN users u ON u.id = o.user_id
     ORDER BY o.created_at DESC
     LIMIT 5"
)->fetchAll(PDO::FETCH_ASSOC);

$recentMessages = $pdo->query(
    'SELECT id, name, email, message, is_read, created_at
     FROM messages
     ORDER BY created_at DESC
     LIMIT 5'
)->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/includes/header.php';
?>

<div class="stat-grid">

    <div class="stat-card">
        <span class="stat-label">Total Users</span>
        <span class="stat-value"><?= number_format($totalUsers) ?></span>
    </div>

    <div class="stat-card">
        <span class="stat-label">Total Orders</span>
        <span class="stat-value"><?= number_format($totalOrders) ?></span>
    </div>

    <div class="stat-card">
        <span class="stat-label">Pending Orders</span>
        <span class="stat-value"><?= number_format($pendingOrders) ?></span>
    </div>

    <div class="stat-card">
        <span class="stat-label">Revenue</span>
        <span class="stat-value">₱<?= number_format($totalRevenue, 2) ?></span>
    </div>

    <div class="stat-card">
        <span class="stat-label">Unread Messages</span>
        <span class="stat-value"><?= number_format($unreadMsgs) ?></span>
    </div>

    <div class="stat-card <?= $lowStockCount > 0 ? 'stat-card-warning' : '' ?>">
        <span class="stat-label">Low Stock Items</span>
        <span class="stat-value"><?= number_format($lowStockCount) ?></span>
    </div>

</div>

<div class="admin-panel-grid">

    <section class="admin-panel">
        <div class="admin-panel-head">
            <h2>Recent Orders</h2>
            <a href="orders.php" class="admin-link">View all</a>
        </div>

        <?php if (empty($recentOrders)): ?>
            <p class="admin-empty">No orders yet.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td>#<?= (int) $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['username']) ?></td>
                            <td>₱<?= number_format((float) $order['total'], 2) ?></td>
                            <td><span class="status-pill status-<?= htmlspecialchars($order['status']) ?>"><?= htmlspecialchars(ucfirst($order['status'])) ?></span></td>
                            <td><?= htmlspecialchars(date('M j, Y', strtotime($order['created_at']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

    <section class="admin-panel">
        <div class="admin-panel-head">
            <h2>Recent Messages</h2>
            <a href="messages.php" class="admin-link">View all</a>
        </div>

        <?php if (empty($recentMessages)): ?>
            <p class="admin-empty">No messages yet.</p>
        <?php else: ?>
            <ul class="admin-message-list">
                <?php foreach ($recentMessages as $msg): ?>
                    <li class="<?= $msg['is_read'] ? '' : 'is-unread' ?>">
                        <div class="admin-message-meta">
                            <strong><?= htmlspecialchars($msg['name']) ?></strong>
                            <span><?= htmlspecialchars(date('M j, Y', strtotime($msg['created_at']))) ?></span>
                        </div>
                        <p><?= htmlspecialchars(mb_strimwidth($msg['message'], 0, 100, '…')) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
