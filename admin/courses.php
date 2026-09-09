<?php
session_start();
include "../db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin-login.php");
    exit();
}

$admin_name = $_SESSION["admin_name"];

$course_query = $conn->query(
    "SELECT
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
     ORDER BY level, semester, course_code"
);

if (!$course_query) {
    die("Failed to load courses: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Courses - Admin Panel</title>
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

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .page-header h2 {
        color: #5b2c83;
        font-size: 22px;
    }

    .add-course-btn {
        background: #5b2c83;
        color: white;
        text-decoration: none;
        padding: 12px 18px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: 0.3s;
    }

    .add-course-btn:hover {
        background: #452066;
    }
    
    .table-container {
        background: white;
        border-radius: 12px;
        padding: 20px;
        overflow-x: auto;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1000px;
    }

    table th {
        background: #eee5f7;
        color: #5b2c83;
        padding: 14px 12px;
        text-align: left;
        font-size: 14px;
    }

    table td {
        padding: 14px 12px;
        border-bottom: 1px solid #eee;
        font-size: 14px;
    }

    table tr:hover {
        background: #faf8fd;
    }
    
    .status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }
    .core {
        background: #eee5f7;
        color: #5b2c83;
    }
    .elective {
        background: #e8f4ea;
        color: #287a3d;
    }
    
    .action-buttons {
        display: flex;
        gap: 8px;
    }
    .edit-btn,
    .delete-btn {
        border: none;
        color: white;
        width: 34px;
        height: 34px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .edit-btn {
        background: #5b2c83;
    }
    .delete-btn {
        background: #d9534f;
    }
    .edit-btn:hover {
        background: #452066;
    }
    .delete-btn:hover {
        background: #c9302c;
    }
    .no-courses {
        text-align: center;
        padding: 30px;
        color: #777;
    }

    @media (max-width: 700px) {
        .sidebar {
            width: 210px;
        }
        .main {
            margin-left: 210px;
            padding: 15px;
        }
        .topbar {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
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
            <a href="admin-dashboard.php">
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
            <a href="courses.php" class="active">
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
            <a href="department.php">
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
        <h1>Courses</h1>
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


    <div class="page-header">
        <h2>Course Management</h2>
        <a href="add-course.php" class="add-course-btn">
            <i class="fa-solid fa-plus"></i>
            Add Course
        </a>
    </div>


    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Course Code</th>
                    <th>Course Title</th>
                    <th>Faculty</th>
                    <th>Department</th>
                    <th>Level</th>
                    <th>Semester</th>
                    <th>Unit</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($course_query->num_rows > 0): ?>
                    <?php $number = 1; ?>
                    <?php while ($course = $course_query->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php echo $number++; ?>
                            </td>
                            
                            <td>
                                <strong>
                                    <?php echo htmlspecialchars($course["course_code"]); ?>
                                </strong>
                            </td>
                            
                            <td>
                                <?php echo htmlspecialchars($course["course_title"]); ?>
                            </td>
                            
                            <td>
                                <?php echo htmlspecialchars($course["faculty"]); ?>
                            </td>
                            
                            <td>
                                <?php echo htmlspecialchars($course["department"]); ?>
                            </td>
                            
                            <td>
                                <?php echo htmlspecialchars($course["level"]); ?>
                            </td>
                            
                            <td>
                                <?php echo htmlspecialchars($course["semester"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($course["unit"]); ?>
                            </td>

                            <td>
                                <?php if (strtolower($course["status"]) === "core"): ?>
                                    <span class="status core">
                                        Core
                                    </span>
                                <?php else: ?>
                                    <span class="status elective">
                                        Elective
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <div class="action-buttons">
                                    <a
                                        href="edit-course.php?id=<?php echo $course["id"]; ?>"
                                        class="edit-btn"
                                        title="Edit Course">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>

                                    <a
                                        href="delete-course.php?id=<?php echo $course["id"]; ?>"
                                        class="delete-btn"
                                        title="Delete Course"
                                        onclick="return confirm('Are you sure you want to delete this course?');">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>

                    <tr>
                        <td colspan="10" class="no-courses">
                            No courses found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
</div>

</body>
</html>