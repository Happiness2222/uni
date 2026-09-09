<?php
session_start();
include "../db.php";
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin-login.php");
    exit();
}
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: courses.php");
    exit();
}
$course_id = (int) $_GET["id"];
$stmt = $conn->prepare(
    "DELETE FROM courses WHERE id = ?"
);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$stmt->close();
header("Location: courses.php");
exit();
?>