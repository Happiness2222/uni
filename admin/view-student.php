<?php

session_start();

include "../db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin-login.php");
    exit();
}

// Get student ID from URL
$student_id = intval($_GET["id"] ?? 0);

if ($student_id <= 0) {
    die("Invalid student ID.");
}

// Get student details
$sql = "SELECT
            id,
            first_name,
            last_name,
            email,
            registration_number,
            matric_number,
            faculty,
            department,
            phone,
            gender,
            level,
            passport
        FROM students
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Student not found.");
}

$student = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            color: #333;
        }

        .container {
            width: 100%;
            padding: 30px;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .top-bar h1 {
            font-size: 28px;
            color: #222;
        }

        .back-btn {
            text-decoration: none;
            background: #2563eb;
            color: white;
            padding: 11px 17px;
            border-radius: 8px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-btn:hover {
            background: #1d4ed8;
        }

        .student-card {
            background: white;
            border-radius: 14px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .student-header {
            display: flex;
            align-items: center;
            gap: 25px;
            padding-bottom: 25px;
            margin-bottom: 25px;
            border-bottom: 1px solid #eee;
        }

        .passport {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #eee;
        }

        .no-passport {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #777;
            font-size: 35px;
        }

        .student-header h2 {
            font-size: 24px;
            margin-bottom: 8px;
        }

        .student-header p {
            color: #777;
        }

        .details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .detail-box {
            background: #f8fafc;
            padding: 16px;
            border-radius: 8px;
        }

        .detail-box i {
            margin-right: 8px;
            color: #2563eb;
        }

        .detail-label {
            font-size: 12px;
            color: #777;
            margin-bottom: 6px;
        }

        .detail-value {
            font-size: 15px;
            font-weight: bold;
            color: #333;
        }

        @media (max-width: 700px) {

            .container {
                padding: 20px;
            }

            .top-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .details {
                grid-template-columns: 1fr;
            }

            .student-header {
                flex-direction: column;
                align-items: flex-start;
            }

        }

        .top-buttons {
    display: flex;
    align-items: center;
    gap: 10px;
}

.dashboard-btn {
    text-decoration: none;
    background: #16a34a;
    color: white;
    padding: 11px 17px;
    border-radius: 8px;
    font-weight: bold;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.dashboard-btn:hover {
    background: #15803d;
}
   </style>

</head>
<body>
<div class="container">
    <div class="top-bar">
    <h1>
        <i class="fa-solid fa-user"></i>
        Student Details
    </h1>

    <div class="top-buttons">
        <a href="students.php" class="back-btn">
            <i class="fa-solid fa-arrow-left"></i>
            Back to Students
        </a>

        <a href="admin-dashboard.php" class="dashboard-btn">
            <i class="fa-solid fa-house"></i>
            Dashboard
        </a>
    </div>
</div>

    <div class="student-card">

        <div class="student-header">

            <?php if (!empty($student["passport"])): ?>

                <img
                    src="../uploads/<?php echo htmlspecialchars($student["passport"]); ?>"
                    class="passport"
                    alt="Student Passport"
                >

            <?php else: ?>

                <div class="no-passport">
                    <i class="fa-solid fa-user"></i>
                </div>

            <?php endif; ?>


            <div>

                <h2>
                    <?php
                    echo htmlspecialchars(
                        $student["first_name"] . " " . $student["last_name"]
                    );
                    ?>
                </h2>

                <p>
                    <?php echo htmlspecialchars($student["matric_number"]); ?>
                </p>

            </div>

        </div>


        <div class="details">

            <div class="detail-box">
                <div class="detail-label">
                    <i class="fa-solid fa-envelope"></i>
                    Email
                </div>

                <div class="detail-value">
                    <?php echo htmlspecialchars($student["email"]); ?>
                </div>
            </div>


            <div class="detail-box">
                <div class="detail-label">
                    <i class="fa-solid fa-id-card"></i>
                    Registration Number
                </div>

                <div class="detail-value">
                    <?php echo htmlspecialchars($student["registration_number"]); ?>
                </div>
            </div>


            <div class="detail-box">
                <div class="detail-label">
                    <i class="fa-solid fa-id-badge"></i>
                    Matric Number
                </div>

                <div class="detail-value">
                    <?php echo htmlspecialchars($student["matric_number"]); ?>
                </div>
            </div>


            <div class="detail-box">
                <div class="detail-label">
                    <i class="fa-solid fa-building-columns"></i>
                    Faculty
                </div>

                <div class="detail-value">
                    <?php echo htmlspecialchars($student["faculty"]); ?>
                </div>
            </div>


            <div class="detail-box">
                <div class="detail-label">
                    <i class="fa-solid fa-building"></i>
                    Department
                </div>

                <div class="detail-value">
                    <?php echo htmlspecialchars($student["department"]); ?>
                </div>
            </div>


            <div class="detail-box">
                <div class="detail-label">
                    <i class="fa-solid fa-phone"></i>
                    Phone
                </div>

                <div class="detail-value">
                    <?php echo htmlspecialchars($student["phone"]); ?>
                </div>
            </div>


            <div class="detail-box">
                <div class="detail-label">
                    <i class="fa-solid fa-venus-mars"></i>
                    Gender
                </div>

                <div class="detail-value">
                    <?php echo htmlspecialchars($student["gender"]); ?>
                </div>
            </div>


            <div class="detail-box">
                <div class="detail-label">
                    <i class="fa-solid fa-layer-group"></i>
                    Level
                </div>

                <div class="detail-value">
                    <?php echo htmlspecialchars($student["level"]); ?>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>