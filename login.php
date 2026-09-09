<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($email) || empty($password)) {
        die("Please enter your email and password.");
    }

    $sql = "SELECT * FROM students WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Database error: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();
        if (password_verify($password, $student["password"])) {
            $_SESSION["student_id"] = $student["id"];
            header("Location: dashboard.php");
            exit();

        } else {
            echo "Invalid email or password.";
        }

    } else {
        echo "Invalid email or password.";
    }
    $stmt->close();
}

$conn->close();
?>