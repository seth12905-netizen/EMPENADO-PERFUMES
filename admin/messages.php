<?php
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Messages';
$activeNav = 'messages';

// Viewing the list marks everything currently shown as read.
$pdo->exec('UPDATE messages SET is_read = 1 WHERE is_read = 0');

$messages = $pdo->query('SELECT * FROM messages ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/includes/header.php';
?>

<section class="admin-panel">

    <div class="admin-panel-head">
        <h2>Contact Messages (<?= count($messages) ?>)</h2>
    </div>

    <?php if (empty($messages)): ?>
        <p class="admin-empty">No messages yet.</p>
    <?php else: ?>
        <ul class="admin-message-list admin-message-list-full">
            <?php foreach ($messages as $msg): ?>
                <li>
                    <div class="admin-message-meta">
                        <strong><?= htmlspecialchars($msg['name']) ?></strong>
                        <a href="mailto:<?= htmlspecialchars($msg['email']) ?>"><?= htmlspecialchars($msg['email']) ?></a>
                        <span><?= htmlspecialchars(date('M j, Y g:i A', strtotime($msg['created_at']))) ?></span>
                    </div>
                    <p><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                    <form method="POST" action="message-delete.php" onsubmit="return confirm('Delete this message?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $msg['id'] ?>">
                        <button type="submit" class="admin-link admin-link-danger">Delete</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
