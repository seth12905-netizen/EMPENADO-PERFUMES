<?php
/**
 * Shared admin layout header. Expects $pageTitle to be set, and
 * optionally $activeNav (one of: dashboard, products, collections,
 * orders, messages, users) to highlight the current sidebar link.
 * auth.php must already have been included before this file.
 */
$activeNav = $activeNav ?? '';
$flashStatus  = $_GET['status'] ?? null;
$flashMessage = $_GET['message'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> — EMPENADO Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="assets/admin.css">
</head>

<body class="admin-body">

    <div class="admin-shell">

        <aside class="admin-sidebar">

            <a href="index.php" class="admin-logo">
                <span class="logo-text">EMPEN<span>A</span>DO</span>
                <span class="logo-subtext">PERFUMES</span>
            </a>

            <span class="admin-tag">Admin</span>

            <nav class="admin-nav">
                <a href="index.php" class="<?= $activeNav === 'dashboard' ? 'is-active' : '' ?>">Dashboard</a>
                <a href="products.php" class="<?= $activeNav === 'products' ? 'is-active' : '' ?>">Shop Products</a>
                <a href="collections.php" class="<?= $activeNav === 'collections' ? 'is-active' : '' ?>">Special Collections</a>
                <a href="orders.php" class="<?= $activeNav === 'orders' ? 'is-active' : '' ?>">Orders</a>
                <a href="messages.php" class="<?= $activeNav === 'messages' ? 'is-active' : '' ?>">Messages</a>
                <a href="users.php" class="<?= $activeNav === 'users' ? 'is-active' : '' ?>">Users</a>
            </nav>

            <div class="admin-sidebar-footer">
    <a href="../index.php" class="admin-link-muted">
        <svg class="admin-link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        <span>Back to site</span>
    </a>
    <a href="../logout.php" class="admin-link-muted admin-link-logout">
        <svg class="admin-link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
        </svg>
        <span>Log out</span>
    </a>
</div>

        </aside>

        <main class="admin-main">

            <header class="admin-topbar">
                <h1><?= htmlspecialchars($pageTitle ?? 'Admin') ?></h1>
            </header>

            <?php if ($flashStatus === 'success'): ?>
                <p class="form-alert form-alert-success"><?= htmlspecialchars($flashMessage) ?></p>
            <?php elseif ($flashStatus === 'error'): ?>
                <p class="form-alert form-alert-error"><?= htmlspecialchars($flashMessage) ?></p>
            <?php endif; ?>

            <div class="admin-content">
