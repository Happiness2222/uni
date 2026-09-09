<?php

session_start();

include "../db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin-login.php");
    exit();
}

$admin_name = $_SESSION["admin_name"];

$student_query = $conn->query(
    "SELECT COUNT(*) AS total FROM students"
);

$student_count = $student_query->fetch_assoc()["total"];

$course_query = $conn->query(
    "SELECT COUNT(*) AS total FROM courses"
);

$course_count = $course_query->fetch_assoc()["total"];

$faculty_query = $conn->query(
    "SELECT COUNT(*) AS total FROM faculties_info"
);

$faculty_count = $faculty_query->fetch_assoc()["total"];

$department_query = $conn->query(
    "SELECT COUNT(DISTINCT department) AS total
     FROM courses
     WHERE department IS NOT NULL
     AND department != ''"
);

$department_count = $department_query->fetch_assoc()["total"];

$application_query = $conn->query(
    "SELECT COUNT(*) AS total
     FROM admission_applications
     WHERE status = 'Pending'"
);

$application_count = $application_query->fetch_assoc()["total"];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }


        body {
            background: #f5f3fa;
            color: #333;
        }


        /* SIDEBAR */

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100vh;
            background: #5b2c83;
            color: white;
            padding: 25px 15px;
        }


        .sidebar .logo {
            text-align: center;
            margin-bottom: 35px;
        }


        .sidebar .logo i {
            font-size: 35px;
            margin-bottom: 10px;
        }


        .sidebar .logo h2 {
            font-size: 21px;
        }


        .sidebar ul {
            list-style: none;
        }


        .sidebar ul li {
            margin-bottom: 8px;
        }


        .sidebar ul li a {
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            color: white;
            padding: 14px 15px;
            border-radius: 8px;
            transition: 0.3s;
        }


        .sidebar ul li a i {
            width: 20px;
            text-align: center;
            font-size: 17px;
        }


        .sidebar ul li a:hover {
            background: #452066;
        }


        .sidebar ul li a.active {
            background: #452066;
        }


        .sidebar .logout {
            margin-top: 30px;
        }


        .sidebar .logout a {
            background: #d9534f;
        }


        .sidebar .logout a:hover {
            background: #c9302c;
        }

        .main {
            margin-left: 250px;
            padding: 30px;
        }


        /* TOP BAR */

        .topbar {
            background: white;
            padding: 20px 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }


        .topbar h1 {
            font-size: 26px;
            color: #333;
        }


        .topbar-right {
            display: flex;
            align-items: center;
            gap: 25px;
        }


        /* NOTIFICATION */

        .notification {
            position: relative;
            color: #5b2c83;
            font-size: 22px;
            text-decoration: none;
        }


        .notification:hover {
            color: #452066;
        }


        .notification-badge {
            position: absolute;
            top: -8px;
            right: -10px;
            background: #d9534f;
            color: white;
            font-size: 11px;
            font-weight: bold;
            min-width: 19px;
            height: 19px;
            padding: 2px 5px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #666;
        }


        .admin-info i {
            color: #5b2c83;
            font-size: 22px;
        }


        .admin-name {
            color: #5b2c83;
            font-weight: bold;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }


        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            transition: 0.3s;
        }


        .card:hover {
            transform: translateY(-3px);
        }


        .card-info h3 {
            color: #777;
            font-size: 14px;
            margin-bottom: 10px;
        }


        .card-info p {
            font-size: 30px;
            font-weight: bold;
            color: #5b2c83;
        }


        .card-icon {
            width: 55px;
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #eee5f7;
            color: #5b2c83;
            font-size: 22px;
        }

        .welcome {
            background: white;
            margin-top: 25px;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }


        .welcome h2 {
            color: #5b2c83;
            margin-bottom: 10px;
        }


        .welcome p {
            color: #666;
            line-height: 1.7;
        }


        /* RESPONSIVE */

        @media (max-width: 1100px) {

            .cards {
                grid-template-columns: repeat(2, 1fr);
            }

        }


        @media (max-width: 700px) {

            .sidebar {
                width: 210px;
            }


            .main {
                margin-left: 210px;
                padding: 15px;
            }


            .cards {
                grid-template-columns: 1fr;
            }


            .topbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }


            .topbar-right {
                width: 100%;
                justify-content: space-between;
            }

        }
    </style>

</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <i class="fa-solid fa-user-shield"></i>
            <h2>Admin Panel</h2>
        </div>

        <ul>
            <li>
                <a
                    href="admin-dashboard.php"
                    class="active"
                >
                    <i class="fa-solid fa-house"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li>
                <a href="students.php">

                    <i class="fa-solid fa-users"></i>

                    <span>Students</span>
                </a>
            </li>

            <li>
                <a href="courses.php">
                    <i class="fa-solid fa-book"></i>
                    <span>Courses</span>
                </a>
            </li>

            <li>
                <a href="results.php">
                    <i class="fa-solid fa-square-poll-vertical"></i>
                    <span>Results</span>
                </a>
            </li>

            <li>
                <a href="departments.php">
                    <i class="fa-solid fa-building"></i>
                    <span>Departments</span>
                </a>
            </li>

            <li>
                <a href="faculties.php">
                    <i class="fa-solid fa-building-columns"></i>
                    <span>Faculties</span>
                </a>

            </li>

            <li>
                <a href="settings.php">
                    <i class="fa-solid fa-gear"></i>
                    <span>Settings</span>
                </a>
            </li>


            <li class="logout">
                <a href="admin-logout.php">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="main">
        <div class="topbar">
            <h1>Dashboard</h1>
            <div class="topbar-right">
                <a
                    href="admission-applications.php"
                    class="notification"
                    title="Admission Applications"
                >

                    <i class="fa-solid fa-bell"></i>


                    <?php if ($application_count > 0): ?>

                        <span class="notification-badge">

                            <?php echo $application_count; ?>

                        </span>

                    <?php endif; ?>

                </a>

                <div class="admin-info">

                    <i class="fa-solid fa-circle-user"></i>

                    <span>

                        Welcome,

                        <span class="admin-name">

                            <?php
                            echo htmlspecialchars($admin_name);
                            ?>

                        </span>
                    </span>
                </div>
            </div>
        </div>

        <div class="cards">
            <div class="card">

                <div class="card-info">

                    <h3>Total Students</h3>

                    <p>
                        <?php echo $student_count; ?>
                    </p>

                </div>


                <div class="card-icon">
                    <i class="fa-solid fa-users"></i>
                </div>

            </div>

            <div class="card">
                <div class="card-info">
                    <h3>Total Courses</h3>
                    <p>
                        <?php echo $course_count; ?>
                    </p>

                </div>

                <div class="card-icon">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                    </div>

            <div class="card">

                <div class="card-info">

                    <h3>Total Departments</h3>

                    <p>
                        <?php echo $department_count; ?>
                    </p>

                </div>


                <div class="card-icon">

                    <i class="fa-solid fa-building"></i>

                </div>

            </div>

            <div class="card">
                <div class="card-info">
                    <h3>Total Faculties</h3>
                    <p>
                        <?php echo $faculty_count; ?>
                    </p>
                </div>


                <div class="card-icon">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
            </div>
        </div>

        <div class="welcome">
            <h2>
                Welcome to the Admin Dashboard
            </h2>

            <p>
                From this dashboard, you can manage students,
                courses, results, departments and faculties
                within the student portal.
            </p>
        </div>
    </div>

</body>
</html>