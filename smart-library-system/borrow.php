<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: search.php");
    exit;
}

$user_id = (int)$_SESSION["user_id"];
$book_id = (int)($_POST["book_id"] ?? 0);

$conn->begin_transaction();

try {
    // Lock the selected book row so two users cannot borrow the last copy simultaneously.
    $stmt = $conn->prepare("SELECT available_quantity FROM books WHERE book_id = ? FOR UPDATE");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $book = $stmt->get_result()->fetch_assoc();

    if (!$book) {
        throw new Exception("Book not found.");
    }

    if ((int)$book["available_quantity"] <= 0) {
        throw new Exception("This book is currently unavailable.");
    }

    // Prevent the same student from having the same book borrowed twice at once.
    $stmt = $conn->prepare("SELECT borrowing_id FROM borrowings WHERE user_id = ? AND book_id = ? AND status = 'Borrowed' LIMIT 1");
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        throw new Exception("You already have this book borrowed.");
    }

    $borrow_date = date("Y-m-d");
    $due_date = date("Y-m-d", strtotime("+14 days"));

    $stmt = $conn->prepare("INSERT INTO borrowings (user_id, book_id, borrow_date, due_date, status) VALUES (?, ?, ?, ?, 'Borrowed')");
    $stmt->bind_param("iiss", $user_id, $book_id, $borrow_date, $due_date);

    if (!$stmt->execute()) {
        throw new Exception("Could not create borrowing record.");
    }

    $stmt = $conn->prepare("UPDATE books SET available_quantity = available_quantity - 1 WHERE book_id = ?");
    $stmt->bind_param("i", $book_id);

    if (!$stmt->execute()) {
        throw new Exception("Could not update book availability.");
    }

    $conn->commit();
    header("Location: book-details.php?id=" . $book_id . "&borrowed=1");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    header("Location: book-details.php?id=" . $book_id . "&error=" . urlencode($e->getMessage()));
    exit;
}
?>
