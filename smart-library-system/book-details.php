<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$book_id = (int)($_GET["id"] ?? 0);

$stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();

if (!$book) {
    die("Book not found.");
}

$message = "";
$message_type = "";

if (isset($_GET["borrowed"])) {
    $message = "Book borrowed successfully.";
    $message_type = "success";
}
if (isset($_GET["error"])) {
    $message = $_GET["error"];
    $message_type = "error";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($book["title"]) ?> - Smart Library</title>
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
        <a class="profile-link" href="profile.php"><span>◉</span> Profile</a>
        <button class="theme-toggle" type="button" data-theme-toggle aria-label="Switch to dark mode" aria-pressed="false"><span data-theme-icon>☾</span><span data-theme-label>Dark mode</span></button>
        <a class="logout-link" href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <div class="card">
        <?php if ($message): ?>
            <div class="alert <?= $message_type ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <div class="book-layout">
            <aside class="book-cover">
                <span class="cover-category"><?= htmlspecialchars($book["category"] ?: "Library book") ?></span>
                <strong><?= htmlspecialchars($book["title"]) ?></strong>
                <small><?= htmlspecialchars($book["author"]) ?></small>
            </aside>
            <div class="book-details">
                <p class="eyebrow">Book details</p>
                <h1><?= htmlspecialchars($book["title"]) ?></h1>
                <p class="muted">By <?= htmlspecialchars($book["author"]) ?></p>
                <p class="book-description"><?= nl2br(htmlspecialchars($book["description"] ?: "No description has been added for this title yet.")) ?></p>
                <div class="detail-grid">
                    <div><span>Category</span><strong><?= htmlspecialchars($book["category"] ?: "Uncategorised") ?></strong></div>
                    <div><span>ISBN</span><strong><?= htmlspecialchars($book["isbn"] ?: "Not listed") ?></strong></div>
                    <div><span>Availability</span><strong><?= (int)$book["available_quantity"] ?> of <?= (int)$book["quantity"] ?> copies</strong></div>
                    <div><span>Status</span><strong><?= $book["available_quantity"] > 0 ? "Ready to borrow" : "Currently unavailable" ?></strong></div>
                </div>
                <?php if ($book["available_quantity"] > 0): ?>
                    <form action="borrow.php" method="POST">
                        <input type="hidden" name="book_id" value="<?= (int)$book["book_id"] ?>">
                        <button type="submit">Borrow this book</button>
                    </form>
                <?php else: ?>
                    <button disabled>Currently unavailable</button>
                <?php endif; ?>
                <a class="back-link" href="search.php">← Back to the collection</a>
            </div>
        </div>
    </div>
</div>
<script src="js/theme.js"></script>
</body>
</html>
