<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$books = $conn->query("SELECT book_id, title, author, category, available_quantity, quantity FROM books ORDER BY created_at DESC LIMIT 5");

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM borrowings WHERE user_id = ? AND status = 'Borrowed'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$current_loans = $stmt->get_result()->fetch_assoc()["total"];

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM borrowings WHERE user_id = ? AND status = 'Returned'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$returned_books = $stmt->get_result()->fetch_assoc()["total"];

$catalog = $conn->query("SELECT COUNT(*) AS titles, COALESCE(SUM(available_quantity), 0) AS copies_available FROM books")->fetch_assoc();

$stmt = $conn->prepare(
    "SELECT b.title, b.author, br.due_date
     FROM borrowings br
     INNER JOIN books b ON b.book_id = br.book_id
     WHERE br.user_id = ? AND br.status = 'Borrowed'
     ORDER BY br.due_date ASC
     LIMIT 3"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$upcoming_loans = $stmt->get_result();

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM borrowings WHERE user_id = ? AND status = 'Borrowed' AND due_date < CURDATE()");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$overdue_books = $stmt->get_result()->fetch_assoc()["total"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Smart Library</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="dashboard.php"><span class="brand-mark" aria-hidden="true"><span class="logo-pages"></span><span class="logo-star">✦</span></span><span>Smart Library</span></a>
    <nav>
        <a class="active" href="dashboard.php">Dashboard</a>
        <a href="search.php">Search Books</a>
        <a href="my-books.php">My Books</a>
        <span class="nav-divider" aria-hidden="true"></span>
        <a class="profile-link" href="profile.php"><span>◉</span> Profile</a>
        <button class="theme-toggle" type="button" data-theme-toggle aria-label="Switch to dark mode" aria-pressed="false"><span data-theme-icon>☾</span><span data-theme-label>Dark mode</span></button>
        <a class="logout-link" href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <section class="card dashboard-hero">
        <p class="eyebrow">Your reading space</p>
        <h1>Welcome back, <?= htmlspecialchars($_SESSION["name"]) ?>.</h1>
        <p class="muted">Discover your next great read, keep track of due dates, and make the most of the library collection.</p>
        <div class="hero-actions">
            <a class="button primary-light" href="search.php">Browse the collection <span>→</span></a>
            <a class="button ghost-light" href="my-books.php">View my books</a>
        </div>
    </section>

    <div class="stats">
        <div class="stat card">
            <span class="stat-icon">▣</span>
            <strong><?= $current_loans ?></strong>
            <span>Currently borrowed</span>
        </div>
        <div class="stat card success">
            <span class="stat-icon">✓</span>
            <strong><?= $returned_books ?></strong>
            <span>Books returned</span>
        </div>
        <div class="stat card teal">
            <span class="stat-icon">⌘</span>
            <strong><?= (int)$catalog["copies_available"] ?></strong>
            <span>Copies available</span>
        </div>
        <div class="stat card warning">
            <span class="stat-icon">◷</span>
            <strong><?= $overdue_books ?></strong>
            <span>Past due date</span>
        </div>
    </div>

    <div class="dashboard-grid">
        <section class="card">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Keep reading</p>
                    <h2>Due soon</h2>
                    <p class="muted">Your active loans at a glance.</p>
                </div>
                <a href="my-books.php">See all</a>
            </div>
            <div class="loan-list">
                <?php if ($upcoming_loans->num_rows === 0): ?>
                    <p class="empty-state">No books are currently borrowed. Explore the collection to find your next read.</p>
                <?php endif; ?>
                <?php while ($loan = $upcoming_loans->fetch_assoc()): ?>
                    <?php $initial = strtoupper(substr($loan["title"], 0, 1)); ?>
                    <div class="loan-item">
                        <div class="book-avatar"><?= htmlspecialchars($initial) ?></div>
                        <div>
                            <h3><?= htmlspecialchars($loan["title"]) ?></h3>
                            <p><?= htmlspecialchars($loan["author"]) ?></p>
                        </div>
                        <div class="loan-meta">Due<br><strong><?= htmlspecialchars(date("M j", strtotime($loan["due_date"]))) ?></strong></div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>

        <aside class="card">
            <p class="eyebrow">Explore</p>
            <h2>Library tools</h2>
            <div class="quick-links">
                <a class="quick-link" href="search.php"><span>Find a book</span><span>→</span></a>
                <a class="quick-link" href="my-books.php"><span>Manage my loans</span><span>→</span></a>
            </div>
            <div class="tip"><strong>Library tip</strong>Most books are due 14 days after you borrow them. Check your loan list to stay ahead of due dates.</div>
        </aside>
    </div>

    <section class="card">
        <div class="section-heading">
            <div>
                <p class="eyebrow">Discover something new</p>
                <h2>Recently added books</h2>
            </div>
            <a class="button secondary" href="search.php">View collection</a>
        </div>
        <table>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Availability</th>
                <th>Action</th>
            </tr>
            <?php while ($book = $books->fetch_assoc()): ?>
            <tr>
                <td><span class="table-title"><?= htmlspecialchars($book["title"]) ?></span></td>
                <td><?= htmlspecialchars($book["author"]) ?></td>
                <td><?= htmlspecialchars($book["category"]) ?></td>
                <td>
                    <span class="badge <?= $book["available_quantity"] > 0 ? "available" : "unavailable" ?>"><?= (int)$book["available_quantity"] ?> of <?= (int)$book["quantity"] ?> available</span>
                </td>
                <td><a href="book-details.php?id=<?= (int)$book["book_id"] ?>">View</a></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </section>
</div>
<script src="js/theme.js"></script>
</body>
</html>
