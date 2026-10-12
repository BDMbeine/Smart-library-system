<?php
require_once "config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: register.php");
    exit;
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$student_id = trim($_POST["student_id"] ?? "");
$password = $_POST["password"] ?? "";
$confirm_password = $_POST["confirm_password"] ?? "";

if ($name === "" || $email === "" || $student_id === "" || $password === "") {
    header("Location: register.php?error=" . urlencode("Please complete all fields."));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: register.php?error=" . urlencode("Please enter a valid email address."));
    exit;
}

if (strlen($password) < 6) {
    header("Location: register.php?error=" . urlencode("Password must be at least 6 characters."));
    exit;
}

if ($password !== $confirm_password) {
    header("Location: register.php?error=" . urlencode("Passwords do not match."));
    exit;
}

$stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? OR student_id = ? LIMIT 1");
$stmt->bind_param("ss", $email, $student_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    header("Location: register.php?error=" . urlencode("Email or Student ID is already registered."));
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$role = "student";

$stmt = $conn->prepare("INSERT INTO users (name, email, student_id, password, role) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $email, $student_id, $hash, $role);

if ($stmt->execute()) {
    header("Location: index.php?registered=1");
    exit;
}

header("Location: register.php?error=" . urlencode("Registration failed. Please try again."));
exit;
?>
