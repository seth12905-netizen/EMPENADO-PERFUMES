<?php
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Users';
$activeNav = 'users';

$users = $pdo->query(
    "SELECT u.id, u.username, u.email, u.phone, u.role, u.created_at,
            COUNT(o.id) AS order_count
     FROM users u
     LEFT JOIN orders o ON o.user_id = u.id
     GROUP BY u.id
     ORDER BY u.created_at DESC"
)->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/includes/header.php';
?>

<section class="admin-panel">

    <div class="admin-panel-head">
        <h2>All Users (<?= count($users) ?>)</h2>
    </div>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Orders</th>
                    <th>Joined</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['phone']) ?></td>
                        <td><span class="status-pill status-<?= $u['role'] === 'admin' ? 'completed' : 'pending' ?>"><?= htmlspecialchars(ucfirst($u['role'])) ?></span></td>
                        <td><?= (int) $u['order_count'] ?></td>
                        <td><?= htmlspecialchars(date('M j, Y', strtotime($u['created_at']))) ?></td>
                        <td class="admin-row-actions">
                            <?php if ((int) $u['id'] !== (int) $_SESSION['user_id']): ?>
                                <form method="POST" action="user-role.php" class="admin-inline-form">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                    <input type="hidden" name="role" value="<?= $u['role'] === 'admin' ? 'customer' : 'admin' ?>">
                                    <button type="submit" class="admin-link">
                                        <?= $u['role'] === 'admin' ? 'Demote to Customer' : 'Promote to Admin' ?>
                                    </button>
                                </form>
                                <form method="POST" action="user-delete.php" onsubmit="return confirm('Delete this user? This cannot be undone.');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                    <button type="submit" class="admin-link admin-link-danger">Delete</button>
                                </form>
                            <?php else: ?>
                                <span class="admin-muted">This is you</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
