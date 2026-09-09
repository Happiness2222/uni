<?php
session_start();
include "db.php";

if (!isset($_SESSION["student_id"])) {
    header("Location: login.html");
    exit();
}

$student_id = (int) $_SESSION["student_id"];

$sql = "SELECT id, first_name, last_name, email, faculty, department, level
        FROM students
        WHERE id = $student_id
        LIMIT 1";

$result = $conn->query($sql);

if ($result === false) {
    die("Student query failed: " . $conn->error);
}

if ($result->num_rows !== 1) {
    session_destroy();
    header("Location: login.html");
    exit();
}

$student = $result->fetch_assoc();

$faculty = $student["faculty"];
$department = $student["department"];
$level = $student["level"];

$courseSql = "SELECT
                id,
                faculty,
                department,
                level,
                semester,
                course_code,
                course_title,
                unit,
                status
              FROM courses
              WHERE faculty = ?
              AND department = ?
              AND level = ?
              ORDER BY semester, course_code";

$stmt = $conn->prepare($courseSql);

if (!$stmt) {
    die("Course query failed: " . $conn->error);
}

$stmt->bind_param(
    "sss",
    $faculty,
    $department,
    $level
);

$stmt->execute();

$courseResult = $stmt->get_result();

$firstSemesterCourses = [];
$secondSemesterCourses = [];

while ($course = $courseResult->fetch_assoc()) {
    if (strtolower($course["semester"]) === "first") {
        $firstSemesterCourses[] = $course;
    } elseif (strtolower($course["semester"]) === "second") {
        $secondSemesterCourses[] = $course;
    }
}

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Registration | Evergreen State University</title>
    <link rel="stylesheet" href="course-registration.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
<div class="container">
    <div class="course-box">
        <h1>Evergreen State University</h1>
        <h2>Course Registration</h2>
        <div class="student-info">
            <div>
                <strong>Name</strong>
                <p>
                    <?php
                    echo htmlspecialchars(
                        $student["first_name"] . " " . $student["last_name"]
                    );
                    ?>
                </p>
            </div>

            <div>
                <strong>Faculty</strong>
                <p>
                    <?php echo htmlspecialchars($faculty); ?>
                </p>
            </div>

            <div>
                <strong>Department</strong>
                <p>
                    <?php echo htmlspecialchars($department); ?>
                </p>
            </div>

            <div>
                <strong>Level</strong>
                <p>
                    <?php echo htmlspecialchars($level); ?>
                </p>
            </div>
        </div>

        <form id="courseForm">
            <h3>First Semester</h3>
            <table id="firstSemesterTable">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Course Code</th>
                        <th>Course Title</th>
                        <th>Units</th>
                        <th>Status</th>
                    </tr>

                </thead>

                <tbody>
                    <?php foreach ($firstSemesterCourses as $course): ?>
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    value="<?php echo htmlspecialchars($course["course_code"]); ?>"
                                    data-unit="<?php echo htmlspecialchars($course["unit"]); ?>"
                                    data-status="<?php echo htmlspecialchars($course["status"]); ?>"
                                    <?php
                                    if (
                                        strtolower($course["status"]) === "core"
                                    ) {
                                        echo "checked disabled";
                                    }
                                    ?>
                                >
                            </td>

                            <td>
                                <?php echo htmlspecialchars($course["course_code"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($course["course_title"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($course["unit"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($course["status"]); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>


            <div class="unit-summary">
                First Semester Units:
                <strong id="firstUnits">0</strong>
            </div>

            <h3>Second Semester</h3>
            <table id="secondSemesterTable">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Course Code</th>
                        <th>Course Title</th>
                        <th>Units</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($secondSemesterCourses as $course): ?>
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    value="<?php echo htmlspecialchars($course["course_code"]); ?>"
                                    data-unit="<?php echo htmlspecialchars($course["unit"]); ?>"
                                    data-status="<?php echo htmlspecialchars($course["status"]); ?>"
                                    <?php
                                    if (
                                        strtolower($course["status"]) === "core"
                                    ) {
                                        echo "checked disabled";
                                    }
                                    ?>
                                >
                            </td>

                            <td>
                                <?php echo htmlspecialchars($course["course_code"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($course["course_title"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($course["unit"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($course["status"]); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>


            <div class="unit-summary">
                Second Semester Units:
                <strong id="secondUnits">0</strong>
            </div>

            <div class="grand-total">
                Total Registered Units:
                <strong id="grandUnits">0</strong>
            </div>

            <p id="registrationStatus"></p>
            <div class="buttons">
                <button type="submit">
                    Register Courses
                    </button>
                    
                <button type="button" id="printBtn">
                    Print
                    </button>
                    
                <button type="button" id="dashboardBtn">
                    Dashboard
                    </button>    
        </form>
    </div>
</div>


<script src="course-registration.js"></script>
</body>
</html>

<?php
$conn->close();
?>