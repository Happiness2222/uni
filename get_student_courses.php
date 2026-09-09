<?php
include "db.php";

header("Content-Type: application/json");

$student_id = intval($_POST["student_id"] ?? 0);

if ($student_id <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid student."
    ]);
    exit();
}

$sql = "SELECT 
            cr.course_code,
            cr.semester,
            c.course_title,
            c.unit
        FROM course_registrations cr
        INNER JOIN students s 
            ON s.id = cr.student_id
        INNER JOIN courses c 
            ON c.course_code = cr.course_code
            AND c.semester = cr.semester
            AND c.faculty = s.faculty
            AND c.department = s.department
            AND c.level = s.level
        WHERE cr.student_id = ?
        ORDER BY cr.semester, cr.course_code";

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

$courses = [];

while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}

echo json_encode([
    "success" => true,
    "courses" => $courses
]);

$stmt->close();
$conn->close();
?>