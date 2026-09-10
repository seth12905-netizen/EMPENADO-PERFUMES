<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require 'database/config.php';

try {
    $pdo  = getConnection();
    $sql  = "SELECT id, username, email, phone, role, created_at FROM users WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    header('Location: login.php?status=error&message=' . urlencode('Something went wrong loading your account. Please try again.'));
    exit;
}

if (!$user) {
    header('Location: login.php');
    exit;
}

$_SESSION['role'] = $user['role'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account — EMPENADO</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="auth-body">

    <main class="auth-page">
        <div class="auth-card">

            <a href="index.php" class="auth-logo">
                <span class="logo-text">EMPEN<span>A</span>DO</span>
                <span class="logo-subtext">PERFUMES</span>
            </a>

            <h1 class="auth-title">Welcome, <?= htmlspecialchars($user['username']) ?></h1>
            <p class="auth-subtitle">You're logged in.</p>

            <table class="account-table">
                <tr>
                    <th>Username</th>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td><?= htmlspecialchars($user['phone']) ?></td>
                </tr>
                <tr>
                    <th>Member since</th>
                    <td><?= htmlspecialchars($user['created_at']) ?></td>
                </tr>
            </table>

            <?php if ($user['role'] === 'admin'): ?>
                <a href="admin/index.php" class="btn btn-outline auth-submit" style="text-align:center;">Admin Dashboard</a>
            <?php endif; ?>

            <a href="orders.php" class="btn btn-outline auth-submit" style="text-align:center;">My Orders</a>
            <a href="index.php" class="btn btn-outline auth-submit" style="text-align:center;">Back to Shop</a>
            <a href="logout.php" class="btn btn-primary auth-submit" style="text-align:center;">Log Out</a>

        </div>
    </main>

</body>

</html>
