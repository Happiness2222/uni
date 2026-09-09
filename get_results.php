<?php
session_start();
include "db.php";

header("Content-Type: application/json");

if (!isset($_SESSION["student_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Please login first."
    ]);
    exit();
}

$student_id = (int) $_SESSION["student_id"];

$sql = "SELECT
            r.course_code,
            r.course_title,
            r.units,
            r.grade,
            r.semester,
            r.academic_session
        FROM results r
        INNER JOIN course_registrations cr
        ON r.student_id = cr.student_id
        AND r.course_code = cr.course_code
        AND r.semester = cr.semester
        WHERE r.student_id = ?
        ORDER BY r.semester, r.course_code";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $conn->error
    ]);
    exit();
}

$stmt->bind_param("i", $student_id);
$stmt->execute();

$result = $stmt->get_result();

$results = [];

while ($row = $result->fetch_assoc()) {
    $results[] = $row;
}

echo json_encode([
    "success" => true,
    "results" => $results
]);

$stmt->close();
$conn->close();
?>