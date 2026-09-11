<?php
$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In — EMPENADO</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="auth-body">

    <main class="auth-page">
        <div class="auth-card">

            <a href="index.php" class="auth-logo">
                <img src="images/logo.png" alt="EMPENADO logo" class="logo-mark">
                    <span class="auth-logo-text-col">
                    <span class="logo-text">EMPEN<span>A</span>DO</span>
                     <span class="logo-subtext">PERFUMES</span>
                    </span>
            </a>

            <h1 class="auth-title">Welcome Back</h1>
            <p class="auth-subtitle">Log in to continue to your account.</p>

            <?php if ($status === 'success'): ?>
                <p class="form-alert form-alert-success"><?= htmlspecialchars($message) ?></p>
            <?php elseif ($status === 'error'): ?>
                <p class="form-alert form-alert-error"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <form method="POST" action="login-function.php" class="auth-form">

                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="jane@example.com" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Your password" required>

                <button type="submit" name="login" class="btn btn-primary auth-submit">Log In</button>

            </form>

            <p class="auth-switch">Don't have an account? <a href="register.php">Register</a></p>

        </div>
    </main>

</body>

</html>
