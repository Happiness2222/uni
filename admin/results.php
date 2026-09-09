<?php

session_start();

include "../db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin-login.php");
    exit();
}


/* =========================
   GET RESULTS
========================= */

$sql = "SELECT
            results.id,
            results.student_id,
            results.course_code,
            results.course_title,
            results.units,
            results.grade,
            results.semester,
            results.academic_session,
            students.first_name,
            students.last_name
        FROM results
        INNER JOIN students
            ON results.student_id = students.id
        ORDER BY students.first_name, students.last_name, results.semester, results.course_code";

$result = $conn->query($sql);

if (!$result) {
    die("Failed to load results: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Results | Admin</title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
        }

        .main-content {
            padding: 30px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .page-header h1 {
            font-size: 28px;
            color: #222;
        }

        .page-header p {
            color: #777;
            margin-top: 5px;
        }

        .add-btn {
            background: #2563eb;
            color: white;
            padding: 12px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }

        .add-btn:hover {
            background: #1d4ed8;
        }

        .table-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #f8fafc;
            color: #444;
        }

        td {
            color: #555;
        }

        .grade {
            font-weight: bold;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .edit-btn,
        .delete-btn {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            text-decoration: none;
            color: white;
        }

        .edit-btn {
            background: #2563eb;
        }

        .delete-btn {
            background: #dc2626;
        }

        .edit-btn:hover {
            background: #1d4ed8;
        }

        .delete-btn:hover {
            background: #b91c1c;
        }

        .empty {
            text-align: center;
            padding: 30px;
            color: #888;
        }

    </style>

</head>

<body>


<div class="main-content">

    <div class="page-header">

        <div>
            <h1>Results</h1>
            <p>Manage student academic results</p>
        </div>

        <a href="add-result.php" class="add-btn">
            <i class="fa-solid fa-plus"></i>
            Add Result
        </a>

    </div>


    <div class="table-container">

        <table>

            <thead>

                <tr>

                    <th>#</th>
                    <th>Student</th>
                    <th>Course Code</th>
                    <th>Course Title</th>
                    <th>Units</th>
                    <th>Grade</th>
                    <th>Semester</th>
                    <th>Session</th>
                    <th>Actions</th>

                </tr>

            </thead>


            <tbody>

                <?php if ($result->num_rows > 0): ?>

                    <?php $number = 1; ?>

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php echo $number++; ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row["first_name"] . " " . $row["last_name"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row["course_code"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row["course_title"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row["units"]); ?>
                            </td>

                            <td class="grade">
                                <?php echo htmlspecialchars($row["grade"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row["semester"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row["academic_session"]); ?>
                            </td>

                            <td>

                                <div class="actions">

                                    <a
                                        href="edit-result.php?id=<?php echo $row["id"]; ?>"
                                        class="edit-btn"
                                        title="Edit Result"
                                    >
                                        <i class="fa-solid fa-pen"></i>
                                    </a>

                                    <a
                                        href="delete-result.php?id=<?php echo $row["id"]; ?>"
                                        class="delete-btn"
                                        title="Delete Result"
                                        onclick="return confirm('Are you sure you want to delete this result?');"
                                    >
                                        <i class="fa-solid fa-trash"></i>
                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="9" class="empty">
                            No results found.
                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>


</body>

</html>