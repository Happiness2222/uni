<?php
session_start();
include "db.php";

if (!isset($_SESSION["student_id"])) {
    echo "Please login first.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "Invaild request.";
    exit();
}

$student_id = $_SESSION["student_id"];

$currentPassword = $_POST["currentPassword"] ?? "";
$newPassword = $_POST["newPassword"] ?? "";

if (empty($currentPassword) || empty($newPassword)) {
    echo "Please fill in all fields.";
    exit();
}

$sql = "SELECT password FROM students WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "Student not found.";
    exit();
}

$student = $result->fetch_assoc();

if (!password_verify($currentPassword, $student["password"])) {
    echo "Current password is incorrect.";
    exit();
}

$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

$update = $conn->prepare(
    "UPDATE students SET password = ? WHERE id = ?"
);

$update->bind_param(
    "si",
    $hashedPassword,
    $student_id
);

if ($update->execute()) {
    echo "success";
} else {
    echo "Failed to change password.";
}

$update->close();
$stmt->close();
$conn->close();
?>