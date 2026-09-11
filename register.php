<?php
$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — EMPENADO</title>
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

            <h1 class="auth-title">Create Account</h1>
            <p class="auth-subtitle">Join us for early access to new scents and collections.</p>

            <?php if ($status === 'error'): ?>
                <p class="form-alert form-alert-error"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <form method="POST" action="register-function.php" class="auth-form">

                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="janedoe" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="jane@example.com" required>

                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" placeholder="09171234567" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="At least 8 characters" required>

                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required>

                <button type="submit" name="register" class="btn btn-primary auth-submit">Register</button>

            </form>

            <p class="auth-switch">Already have an account? <a href="login.php">Log in</a></p>

        </div>
    </main>

</body>

</html>
