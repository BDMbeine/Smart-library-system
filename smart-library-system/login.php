<?php
session_start();
require_once "config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$student_id = trim($_POST["student_id"] ?? "");
$password = $_POST["password"] ?? "";

$stmt = $conn->prepare("SELECT user_id, name, student_id, password, role FROM users WHERE student_id = ? LIMIT 1");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user["password"])) {
    $_SESSION["user_id"] = $user["user_id"];
    $_SESSION["name"] = $user["name"];
    $_SESSION["student_id"] = $user["student_id"];
    $_SESSION["role"] = $user["role"];

    header("Location: dashboard.php");
    exit;
}

header("Location: index.php?error=" . urlencode("Invalid Student ID or password."));
exit;
?>
