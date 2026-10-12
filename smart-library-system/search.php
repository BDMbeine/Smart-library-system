<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$q = trim($_GET["q"] ?? "");

if ($q !== "") {
    $like = "%" . $q . "%";
    $stmt = $conn->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR isbn LIKE ? OR category LIKE ? ORDER BY title");
    $stmt->bind_param("ssss", $like, $like, $like, $like);
    $stmt->execute();
    $books = $stmt->get_result();
} else {
    $books = $conn->query("SELECT * FROM books ORDER BY title");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Books - Smart Library</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="dashboard.php"><span class="brand-mark" aria-hidden="true"><span class="logo-pages"></span><span class="logo-star">✦</span></span><span>Smart Library</span></a>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a class="active" href="search.php">Search Books</a>
        <a href="my-books.php">My Books</a>
        <span class="nav-divider" aria-hidden="true"></span>
        <a class="profile-link" href="profile.php"><span>◉</span> Profile</a>
        <button class="theme-toggle" type="button" data-theme-toggle aria-label="Switch to dark mode" aria-pressed="false"><span data-theme-icon>☾</span><span data-theme-label>Dark mode</span></button>
        <a class="logout-link" href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <div class="card search-card">
        <p class="eyebrow">Explore the shelves</p>
        <h1>Find your next read</h1>
        <p class="muted">Search across titles, authors, ISBNs, and categories.</p>
        <form class="search-form" method="GET">
            <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search by title, author, ISBN or category">
            <button type="submit">Search</button>
        </form>
    </div>

    <div class="card">
        <div class="section-heading">
            <div><h2>Books</h2><p class="result-count"><?= $books->num_rows ?> result<?= $books->num_rows === 1 ? "" : "s" ?><?= $q !== "" ? " for “" . htmlspecialchars($q) . "”" : " in the collection" ?></p></div>
        </div>
        <table>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Availability</th>
                <th>Action</th>
            </tr>
            <?php if ($books->num_rows === 0): ?>
                <tr><td colspan="5">No books found.</td></tr>
            <?php endif; ?>
            <?php while ($book = $books->fetch_assoc()): ?>
            <tr>
                <td><span class="table-title"><?= htmlspecialchars($book["title"]) ?></span><span class="table-subtitle">ISBN: <?= htmlspecialchars($book["isbn"] ?: "Not listed") ?></span></td>
                <td><?= htmlspecialchars($book["author"]) ?></td>
                <td><?= htmlspecialchars($book["category"]) ?></td>
                <td>
                    <?php if ($book["available_quantity"] > 0): ?>
                        <span class="badge available">Available (<?= (int)$book["available_quantity"] ?>)</span>
                    <?php else: ?>
                        <span class="badge unavailable">Unavailable</span>
                    <?php endif; ?>
                </td>
                <td><a href="book-details.php?id=<?= (int)$book["book_id"] ?>">View Details</a></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
<script src="js/theme.js"></script>
</body>
</html>
