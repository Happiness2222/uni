<?php
session_start();
include "db.php";

// echo "REGISTER PHP LOADED";
// exit();

header("Content-Type: text/plain");

/* ==============================
   CHECK LOGIN
============================== */

if (!isset($_SESSION["student_id"])) {
    echo "Please login first.";
    exit();
}

$student_id = (int) $_SESSION["student_id"];


/* ==============================
   GET STUDENT INFORMATION
============================== */

$studentQuery = $conn->prepare(
    "SELECT faculty, department, level
     FROM students
     WHERE id = ?
     LIMIT 1"
);

if (!$studentQuery) {
    echo "Student query failed: " . $conn->error;
    exit();
}

$studentQuery->bind_param("i", $student_id);
$studentQuery->execute();

$studentResult = $studentQuery->get_result();

if ($studentResult->num_rows !== 1) {
    echo "Student not found.";
    $studentQuery->close();
    exit();
}

$student = $studentResult->fetch_assoc();

$faculty = trim($student["faculty"]);
$department = trim($student["department"]);
$level = trim($student["level"]);

$studentQuery->close();

$rawData = file_get_contents("php://input");

$data = json_decode($rawData, true);


if (!is_array($data)) {
    echo "Invalid registration data.";
    exit();
}


$firstSemester = $data["firstSemester"] ?? [];
$secondSemester = $data["secondSemester"] ?? [];

if (!is_array($firstSemester)) {
    $firstSemester = [];
}

if (!is_array($secondSemester)) {
    $secondSemester = [];
}

$allCourses = array_merge(
    $firstSemester,
    $secondSemester
);


if (count($allCourses) === 0) {
    echo "No courses selected.";
    exit();
}

$firstSemester = array_values(
    array_unique($firstSemester)
);

$secondSemester = array_values(
    array_unique($secondSemester)
);

$check = $conn->prepare(
    "SELECT id
     FROM courses
     WHERE REPLACE(TRIM(faculty), 'Faculty of ', '') =
           REPLACE(TRIM(?), 'Faculty of ', '')
     AND TRIM(department) = TRIM(?)
     AND TRIM(level) = TRIM(?)
     AND TRIM(semester) = TRIM(?)
     AND TRIM(course_code) = TRIM(?)
     LIMIT 1"
);

if (!$check) {
    echo "Course validation failed: " . $conn->error;
    exit();
}

$semester = "First";

foreach ($firstSemester as $course) {

    $course = trim($course);

    $check->bind_param(
        "sssss",
        $faculty,
        $department,
        $level,
        $semester,
        $course
    );

    $check->execute();

    $result = $check->get_result();

    if ($result->num_rows === 0) {

        echo "FIRST SEMESTER FAILED: " . $course;
        $check->close();
        exit();
    }
}

$semester = "Second";

foreach ($secondSemester as $course) {

    $course = trim($course);

    $check->bind_param(
        "sssss",
        $faculty,
        $department,
        $level,
        $semester,
        $course
    );

    $check->execute();

    $result = $check->get_result();

    if ($result->num_rows === 0) {

        echo "SECOND SEMESTER FAILED: " . $course;
        $check->close();
        exit();
    }
}

$check->close();

$conn->begin_transaction();


try {
    $delete = $conn->prepare(
        "DELETE FROM course_registrations
         WHERE student_id = ?"
    );

    if (!$delete) {
        throw new Exception(
            "Could not prepare delete query: " . $conn->error
        );
    }

    $delete->bind_param(
        "i",
        $student_id
    );

    if (!$delete->execute()) {
        throw new Exception(
            "Could not remove previous registration: " .
            $delete->error
        );
    }

    $delete->close();

    $insert = $conn->prepare(
        "INSERT INTO course_registrations
        (student_id, course_code, semester)
        VALUES (?, ?, ?)"
    );

    if (!$insert) {
        throw new Exception(
            "Could not prepare insert query: " . $conn->error
        );
    }

    $semester = "First";
    foreach ($firstSemester as $course) {
        $course = trim($course);

        $insert->bind_param(
            "iss",
            $student_id,
            $course,
            $semester
        );

        if (!$insert->execute()) {
            throw new Exception(
                "Could not register First Semester course " .
                $course . ": " .
                $insert->error
            );
        }
    }

    $semester = "Second";
    foreach ($secondSemester as $course) {
        $course = trim($course);

        $insert->bind_param(
            "iss",
            $student_id,
            $course,
            $semester
        );

        if (!$insert->execute()) {
            throw new Exception(
                "Could not register Second Semester course " .
                $course . ": " .
                $insert->error
            );
        }
    }

    $insert->close();

   $conn->commit();

$countQuery = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM course_registrations
     WHERE student_id = ?"
);

$countQuery->bind_param("i", $student_id);
$countQuery->execute();

$countResult = $countQuery->get_result();
$countRow = $countResult->fetch_assoc();

echo "success - saved " . $countRow["total"] . " courses";

$countQuery->close();

} catch (Exception $e) {
    /* Undo everything if something fails */
    $conn->rollback();
    echo "Registration failed: " . $e->getMessage();
}

$conn->close();
?>