<?php

session_start();

include "../db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admin-login.php");
    exit();
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if (empty($email) || empty($password)) {
    die("Please enter your email and password.");
}

$sql = "SELECT id, full_name, email, password 
        FROM admins 
        WHERE email = ? 
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {

    $admin = $result->fetch_assoc();

    if (password_verify($password, $admin["password"])) {

        $_SESSION["admin_id"] = $admin["id"];
        $_SESSION["admin_name"] = $admin["full_name"];
        $_SESSION["admin_email"] = $admin["email"];

        header("Location: admin-dashboard.php");
        exit();

    } else {
        die("Invalid email or password.");
    }

} else {
    die("Invalid email or password.");
}

$stmt->close();
$conn->close();

?>