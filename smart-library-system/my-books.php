<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$user_id = (int)$_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT br.borrowing_id, br.borrow_date, br.due_date, br.return_date, br.status,
            b.title, b.author
     FROM borrowings br
     INNER JOIN books b ON br.book_id = b.book_id
     WHERE br.user_id = ?
     ORDER BY br.borrowing_id DESC"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$loans = $stmt->get_result();

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM borrowings WHERE user_id = ? AND status = 'Borrowed'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$active_loans = $stmt->get_result()->fetch_assoc()["total"];

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM borrowings WHERE user_id = ? AND status = 'Borrowed' AND due_date < CURDATE()");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$overdue_loans = $stmt->get_result()->fetch_assoc()["total"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Books - Smart Library</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="dashboard.php"><span class="brand-mark" aria-hidden="true"><span class="logo-pages"></span><span class="logo-star">✦</span></span><span>Smart Library</span></a>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="search.php">Search Books</a>
        <a class="active" href="my-books.php">My Books</a>
        <span class="nav-divider" aria-hidden="true"></span>
        <a class="profile-link" href="profile.php"><span>◉</span> Profile</a>
        <button class="theme-toggle" type="button" data-theme-toggle aria-label="Switch to dark mode" aria-pressed="false"><span data-theme-icon>☾</span><span data-theme-label>Dark mode</span></button>
        <a class="logout-link" href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <div class="section-heading">
        <div><p class="eyebrow">Your reading activity</p><h1>My books</h1><p class="muted">Track active loans, due dates, and your borrowing history.</p></div>
        <a class="button secondary" href="search.php">Find a book</a>
    </div>

    <div class="stats">
        <div class="stat card"><span class="stat-icon">▣</span><strong><?= $active_loans ?></strong><span>Active loans</span></div>
        <div class="stat card warning"><span class="stat-icon">◷</span><strong><?= $overdue_loans ?></strong><span>Overdue loans</span></div>
        <div class="stat card success"><span class="stat-icon">✓</span><strong><?= max(0, $loans->num_rows - $active_loans) ?></strong><span>Books returned</span></div>
        <div class="stat card teal"><span class="stat-icon">◎</span><strong><?= $loans->num_rows ?></strong><span>Total history</span></div>
    </div>

    <div class="card">
        <div class="section-heading"><div><h2>Borrowing history</h2><p class="muted">A complete record of your library activity.</p></div></div>

        <table>
            <tr>
                <th>Book</th>
                <th>Author</th>
                <th>Borrow Date</th>
                <th>Due Date</th>
                <th>Return Date</th>
                <th>Status</th>
            </tr>
            <?php if ($loans->num_rows === 0): ?>
                <tr><td colspan="6">You have not borrowed any books yet.</td></tr>
            <?php endif; ?>

            <?php while ($loan = $loans->fetch_assoc()): ?>
                <?php
                    $is_overdue = ($loan["status"] === "Borrowed" && $loan["due_date"] < date("Y-m-d"));
                    $display_status = $is_overdue ? "Overdue" : $loan["status"];
                ?>
                <tr>
                    <td><span class="table-title"><?= htmlspecialchars($loan["title"]) ?></span></td>
                    <td><?= htmlspecialchars($loan["author"]) ?></td>
                    <td><?= htmlspecialchars($loan["borrow_date"]) ?></td>
                    <td><?= htmlspecialchars($loan["due_date"]) ?></td>
                    <td><?= htmlspecialchars($loan["return_date"] ?? "-") ?></td>
                    <td>
                        <?php if ($is_overdue): ?>
                            <span class="badge overdue">Overdue</span>
                        <?php elseif ($loan["status"] === "Borrowed"): ?>
                            <span class="badge available">Borrowed</span>
                        <?php else: ?>
                            <span class="badge returned">Returned</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
<script src="js/theme.js"></script>
</body>
</html>
