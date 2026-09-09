<?php

session_start();

include "../db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin-login.php");
    exit();
}

$message = "";
$error = "";

$selected_student_id = isset($_GET["student_id"])
    ? (int) $_GET["student_id"]
    : 0;


/* =========================
   SAVE RESULTS
========================= */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_id = (int) $_POST["student_id"];
    $academic_session = $_POST["academic_session"];

    if (!isset($_POST["grades"]) || empty($_POST["grades"])) {

        $error = "Please enter at least one grade.";

    } else {

        foreach ($_POST["grades"] as $course_code => $grade) {

            if ($grade == "") {
                continue;
            }

            /*
             * Get the course information from the student's
             * registered courses.
             */

            $course_stmt = $conn->prepare(
                "SELECT
                    cr.course_code,
                    cr.semester,
                    c.course_title,
                    c.unit
                 FROM course_registrations cr
                 INNER JOIN courses c
                    ON c.course_code = cr.course_code
                    AND c.semester = cr.semester
                 WHERE cr.student_id = ?
                 AND cr.course_code = ?
                 LIMIT 1"
            );

            $course_stmt->bind_param(
                "is",
                $student_id,
                $course_code
            );

            $course_stmt->execute();

            $course_result = $course_stmt->get_result();

            if ($course_result->num_rows == 0) {
                $course_stmt->close();
                continue;
            }

            $course = $course_result->fetch_assoc();

            $course_stmt->close();


            /*
             * Check if this result already exists.
             */

            $check_stmt = $conn->prepare(
                "SELECT id
                 FROM results
                 WHERE student_id = ?
                 AND course_code = ?
                 AND semester = ?
                 AND academic_session = ?"
            );

            $check_stmt->bind_param(
                "isss",
                $student_id,
                $course["course_code"],
                $course["semester"],
                $academic_session
            );

            $check_stmt->execute();

            $check_result = $check_stmt->get_result();


            if ($check_result->num_rows > 0) {

                /*
                 * Update existing result
                 */

                $existing = $check_result->fetch_assoc();

                $update_stmt = $conn->prepare(
                    "UPDATE results
                     SET grade = ?,
                         course_title = ?,
                         units = ?
                     WHERE id = ?"
                );

                $update_stmt->bind_param(
                    "ssii",
                    $grade,
                    $course["course_title"],
                    $course["unit"],
                    $existing["id"]
                );

                $update_stmt->execute();

                $update_stmt->close();

            } else {

                /*
                 * Insert new result
                 */

                $insert_stmt = $conn->prepare(
                    "INSERT INTO results
                    (
                        student_id,
                        course_code,
                        course_title,
                        units,
                        grade,
                        semester,
                        academic_session
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?)"
                );

                $insert_stmt->bind_param(
                    "ississs",
                    $student_id,
                    $course["course_code"],
                    $course["course_title"],
                    $course["unit"],
                    $grade,
                    $course["semester"],
                    $academic_session
                );

                $insert_stmt->execute();

                $insert_stmt->close();
            }

            $check_stmt->close();
        }

        header("Location: results.php");
        exit();
    }
}


/* =========================
   GET STUDENTS
========================= */

$students = $conn->query(
    "SELECT
        id,
        first_name,
        last_name,
        matric_number,
        department,
        level
     FROM students
     ORDER BY first_name, last_name"
);


/* =========================
   GET SELECTED STUDENT
========================= */

$selected_student = null;
$registered_courses = null;

if ($selected_student_id > 0) {

    $student_stmt = $conn->prepare(
        "SELECT
            id,
            first_name,
            last_name,
            matric_number,
            faculty,
            department,
            level
         FROM students
         WHERE id = ?"
    );

    $student_stmt->bind_param(
        "i",
        $selected_student_id
    );

    $student_stmt->execute();

    $student_result = $student_stmt->get_result();

    if ($student_result->num_rows > 0) {

        $selected_student = $student_result->fetch_assoc();

        /*
         * Automatically get the courses registered
         * by this student.
         */

        $course_stmt = $conn->prepare(
            "SELECT
                cr.course_code,
                cr.semester,
                c.course_title,
                c.unit
             FROM course_registrations cr
             INNER JOIN courses c
                ON c.course_code = cr.course_code
                AND c.semester = cr.semester
             WHERE cr.student_id = ?
             ORDER BY
                cr.semester,
                cr.course_code"
        );

        $course_stmt->bind_param(
            "i",
            $selected_student_id
        );

        $course_stmt->execute();

        $registered_courses = $course_stmt->get_result();

        $course_stmt->close();
    }

    $student_stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Result | Admin</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            color: #333;
        }

        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
        }

        .header {
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 6px;
        }

        .header p {
            color: #777;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            margin-bottom: 20px;
        }

        .student-info {
            background: #f8fafc;
            padding: 18px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .student-info h2 {
            margin-bottom: 10px;
        }

        .student-info p {
            margin: 5px 0;
            color: #555;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 14px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #f8fafc;
        }

        td select {
            margin: 0;
            width: 130px;
        }

        .save-btn {
            background: #2563eb;
            color: white;
            border: none;
            padding: 13px 22px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: bold;
            margin-top: 20px;
        }

        .save-btn:hover {
            background: #1d4ed8;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
        }

        .message {
            background: #fee2e2;
            color: #b91c1c;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .empty {
            text-align: center;
            padding: 25px;
            color: #777;
        }

    </style>

</head>

<body>

<div class="container">

    <a href="results.php" class="back-btn">
        ← Back to Results
    </a>

    <div class="header">

        <h1>Add Student Result</h1>

        <p>
            Select a student and enter results for their registered courses.
        </p>

    </div>


    <?php if ($error != ""): ?>

        <div class="message">
            <?php echo htmlspecialchars($error); ?>
        </div>

    <?php endif; ?>


    <!-- SELECT STUDENT -->

    <div class="card">

        <form method="GET" action="add-result.php">

            <label for="student_id">
                Select Student
            </label>

            <select
                name="student_id"
                id="student_id"
                onchange="this.form.submit()"
                required
            >

                <option value="">
                    -- Select Student --
                </option>

                <?php while ($student = $students->fetch_assoc()): ?>

                    <option
                        value="<?php echo $student["id"]; ?>"
                        <?php
                        if ($selected_student_id == $student["id"]) {
                            echo "selected";
                        }
                        ?>
                    >

                        <?php
                        echo htmlspecialchars(
                            $student["first_name"]
                            . " "
                            . $student["last_name"]
                            . " - "
                            . $student["matric_number"]
                        );
                        ?>

                    </option>

                <?php endwhile; ?>

            </select>

        </form>

    </div>


    <?php if ($selected_student): ?>

        <!-- STUDENT INFORMATION -->

        <div class="card">

            <div class="student-info">

                <h2>

                    <?php
                    echo htmlspecialchars(
                        $selected_student["first_name"]
                        . " "
                        . $selected_student["last_name"]
                    );
                    ?>

                </h2>

                <p>
                    <strong>Matric Number:</strong>
                    <?php echo htmlspecialchars($selected_student["matric_number"]); ?>
                </p>

                <p>
                    <strong>Faculty:</strong>
                    <?php echo htmlspecialchars($selected_student["faculty"]); ?>
                </p>

                <p>
                    <strong>Department:</strong>
                    <?php echo htmlspecialchars($selected_student["department"]); ?>
                </p>

                <p>
                    <strong>Level:</strong>
                    <?php echo htmlspecialchars($selected_student["level"]); ?>
                </p>

            </div>


            <!-- RESULT FORM -->

            <form method="POST" action="add-result.php">

                <input
                    type="hidden"
                    name="student_id"
                    value="<?php echo $selected_student["id"]; ?>"
                >


                <label for="academic_session">
                    Academic Session
                </label>

                <select
                    name="academic_session"
                    id="academic_session"
                    required
                >

                    <option value="2026/2027">
                        2026/2027
                    </option>

                    <option value="2025/2026">
                        2025/2026
                    </option>

                    <option value="2027/2028">
                        2027/2028
                    </option>

                </select>


                <?php if ($registered_courses && $registered_courses->num_rows > 0): ?>

                    <div class="table-container">

                        <table>

                            <thead>

                                <tr>

                                    <th>#</th>
                                    <th>Course Code</th>
                                    <th>Course Title</th>
                                    <th>Unit</th>
                                    <th>Semester</th>
                                    <th>Grade</th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php $number = 1; ?>

                                <?php while ($course = $registered_courses->fetch_assoc()): ?>

                                    <tr>

                                        <td>
                                            <?php echo $number++; ?>
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
                                            <?php echo htmlspecialchars($course["semester"]); ?>
                                        </td>

                                        <td>

                                            <select
                                                name="grades[<?php echo htmlspecialchars($course["course_code"]); ?>]"
                                            >

                                                <option value="">
                                                    Select Grade
                                                </option>

                                                <option value="A">A</option>
                                                <option value="B">B</option>
                                                <option value="C">C</option>
                                                <option value="D">D</option>
                                                <option value="E">E</option>
                                                <option value="F">F</option>

                                            </select>

                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                            </tbody>

                        </table>

                    </div>


                    <button type="submit" class="save-btn">
                        Save Results
                    </button>

                <?php else: ?>

                    <div class="empty">
                        This student has no registered courses yet.
                    </div>

                <?php endif; ?>

            </form>

        </div>

    <?php endif; ?>

</div>

</body>

</html>