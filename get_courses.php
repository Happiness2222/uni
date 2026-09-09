<?php
session_start();
include "db.php";
header("Content-Type: application/json");

if (!isset($_SESSION["student_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Student not logged in"
    ]);
    exit;
}
$student_id = $_SESSION["student_id"];

$sql = "SELECT faculty, department, level
        FROM students
        WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Student query failed: " . $conn->error
    ]);
    exit;
}
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode([
        "success" => false,
        "message" => "Student not found"
    ]);
    $stmt->close();
    exit;
}
$student = $result->fetch_assoc();
$faculty = $student["faculty"];
$department = $student["department"];
$level = $student["level"];
// IMPORTANT:
// Free the first result and close the first statement
// before running the next query.
$result->free();
$stmt->close();

$sql = "SELECT id,
               faculty,
               department,
               level,
               semester,
               course_code,
               course_title,
               unit,
               status
        FROM courses
        WHERE (faculty = ?
               OR faculty = REPLACE(?, 'Faculty of ', ''))
        AND department = ?
        AND level = ?
        ORDER BY semester, id";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Course query failed: " . $conn->error
    ]);
    exit;
}
$stmt->bind_param(
    "ssss",
    $faculty,
    $faculty,
    $department,
    $level
);
$stmt->execute();
$result = $stmt->get_result();

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}

echo json_encode([
    "success" => true,
    "faculty" => $faculty,
    "department" => $department,
    "level" => $level,
    "courses" => $courses
]);
$result->free();
$stmt->close();
$conn->close();
?>