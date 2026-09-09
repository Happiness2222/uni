<?php
session_start();
include "db.php";

if(!isset($_SESSION["student_id"])) {
    header("Location: login.html");
    exit();
}

$student_id = (int) $_SESSION["student_id"];
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Results | Evergreen State University</title>

    <link rel="stylesheet" href="results.css">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

</head>

<body>

<div class="container">

    <div class="result-box">

        <h1>Evergreen State University</h1>

        <h2>Semester Results</h2>


        <div class="session-box">

            <div>

                <strong>Academic Session</strong><br>

                2026/2027

            </div>


            <div>

                <label>Select Semester</label>

                <select id="semester">

                    <option value="First">
                        First Semester
                    </option>

                    <option value="Second">
                        Second Semester
                    </option>

                </select>

            </div>

        </div>


        <table id="resultTable">

            <thead>

                <tr>

                    <th>Course Code</th>

                    <th>Course Title</th>

                    <th>Units</th>

                    <th>Grade</th>

                    <th>Status</th>

                </tr>

            </thead>


            <tbody>

                <!-- JavaScript loads results here -->

            </tbody>

        </table>


        <div class="summary">

            <div class="card">

                <h3>Total Units</h3>

                <p id="totalUnits">0</p>

            </div>


            <div class="card">

                <h3>Passed Units</h3>

                <p id="passedUnits">0</p>

            </div>


            <div class="card">

                <h3>GPA</h3>

                <p id="gpa">0.00</p>

            </div>


            <div class="card">

                <h3>CGPA</h3>

                <p id="cgpa">0.00</p>

            </div>

        </div>


        <!-- ACTION BUTTONS -->

        <div class="result-actions">

            <a href="dashboard.php" class="dashboard-btn">

                <i class="fa-solid fa-house"></i>

                Back to Dashboard

            </a>


            <button
                type="button"
                class="print-btn"
                onclick="window.print()"
            >

                <i class="fa-solid fa-print"></i>

                Print Result

            </button>

        </div>


    </div>

</div>


<script src="results.js?v=1"></script>

</body>

</html>