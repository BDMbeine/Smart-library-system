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
    <title>Student Registration</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
<button class="theme-toggle auth-theme" type="button" data-theme-toggle aria-label="Switch to dark mode" aria-pressed="false"><span data-theme-icon>☾</span><span data-theme-label>Dark mode</span></button>
<div class="container small">
    <div class="card">
        <div class="auth-brand"><span class="brand-mark" aria-hidden="true"><span class="logo-pages"></span><span class="logo-star">✦</span></span><span>Smart Library</span></div>
        <h1>Create your account</h1>
        <p class="muted">Join the library and keep every borrowed book in one place.</p>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <form action="register_process.php" method="POST">
            <label>Full Name</label>
            <input type="text" name="name" placeholder="Your full name" required>

            <label>Email</label>
            <input type="email" name="email" placeholder="student@example.com" required>

            <label>Student ID</label>
            <input type="text" name="student_id" placeholder="e.g. ST002" required>

            <label>Password</label>
            <input type="password" name="password" minlength="6" required>

            <label>Confirm Password</label>
            <input type="password" name="confirm_password" minlength="6" required>

            <button type="submit">Create my account</button>
        </form>

        <p class="center">Already registered?
            <a href="index.php">Back to login</a>
        </p>
    </div>
</div>
<script src="js/theme.js"></script>
</body>
</html>
