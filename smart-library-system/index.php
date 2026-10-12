<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Library - Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
<button class="theme-toggle auth-theme" type="button" data-theme-toggle aria-label="Switch to dark mode" aria-pressed="false"><span data-theme-icon>☾</span><span data-theme-label>Dark mode</span></button>
<div class="container small">
    <div class="card">
        <div class="auth-brand"><span class="brand-mark" aria-hidden="true"><span class="logo-pages"></span><span class="logo-star">✦</span></span><span>Smart Library</span></div>
        <h1>Welcome back</h1>
        <p class="muted">Sign in to explore your library and manage your reading.</p>

        <?php if (isset($_GET['registered'])): ?>
            <div class="alert success">Registration successful. You can now log in.</div>
        <?php endif; ?>

        <?php if (isset($_GET['logout'])): ?>
            <div class="alert success">You have been logged out.</div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <label>Student ID</label>
            <input type="text" name="student_id" placeholder="e.g. ST001" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Enter password" required>

            <button type="submit">Sign in to your account</button>
        </form>

        <p class="center">Don't have an account?
            <a href="register.php">Register as a student</a>
        </p>
    </div>
</div>
<script src="js/theme.js"></script>
</body>
</html>
