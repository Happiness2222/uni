<?php

session_start();

include "../db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin-login.php");
    exit();
}


if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: faculties.php");
    exit();
}

$id = (int) $_GET["id"];


$stmt = $conn->prepare(
    "DELETE FROM faculties_info WHERE id = ?"
);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    die("Delete failed: " . $stmt->error);
}

if ($stmt->affected_rows === 0) {
    die("No faculty was deleted. Faculty ID: " . $id);
}

$stmt->close();

header("Location: faculties.php");
exit();

?>