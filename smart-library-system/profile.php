<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$user_id = (int)$_SESSION["user_id"];
$stmt = $conn->prepare("SELECT name, email, student_id, created_at FROM users WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Smart Library</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="dashboard.php"><span class="brand-mark" aria-hidden="true"><span class="logo-pages"></span><span class="logo-star">✦</span></span><span>Smart Library</span></a>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="search.php">Search Books</a>
        <a href="my-books.php">My Books</a>
        <span class="nav-divider" aria-hidden="true"></span>
        <a class="profile-link active" href="profile.php"><span>◉</span> Profile</a>
        <button class="theme-toggle" type="button" data-theme-toggle aria-label="Switch to dark mode" aria-pressed="false"><span data-theme-icon>☾</span><span data-theme-label>Dark mode</span></button>
        <a class="logout-link" href="logout.php">Logout</a>
    </nav>
</header>

<main class="container profile-page">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Account settings</p>
            <h1>Your profile</h1>
            <p class="muted">Your library details and appearance preferences in one place.</p>
        </div>
    </div>

    <div class="profile-grid">
        <section class="card profile-summary">
            <div class="profile-avatar"><?= htmlspecialchars(strtoupper(substr($user["name"], 0, 1))) ?></div>
            <h2><?= htmlspecialchars($user["name"]) ?></h2>
            <p class="muted">Library member</p>
            <div class="profile-meta"><span>Member since</span><strong><?= htmlspecialchars(date("F Y", strtotime($user["created_at"]))) ?></strong></div>
        </section>

        <section class="card">
            <p class="eyebrow">Personal details</p>
            <h2>Account information</h2>
            <div class="account-details">
                <div><span>Full name</span><strong><?= htmlspecialchars($user["name"]) ?></strong></div>
                <div><span>Student ID</span><strong><?= htmlspecialchars($user["student_id"]) ?></strong></div>
                <div class="full-row"><span>Email address</span><strong><?= htmlspecialchars($user["email"]) ?></strong></div>
            </div>
        </section>
    </div>

    <section class="card appearance-card">
        <div>
            <p class="eyebrow">Appearance</p>
            <h2>Theme settings</h2>
            <p class="muted">Choose the view that feels most comfortable. Your choice is remembered on this device.</p>
        </div>
        <button class="theme-toggle appearance-toggle" type="button" data-theme-toggle aria-label="Switch to dark mode" aria-pressed="false"><span data-theme-icon>☾</span><span data-theme-label>Dark mode</span></button>
    </section>
</main>
<script src="js/theme.js"></script>
</body>
</html>
