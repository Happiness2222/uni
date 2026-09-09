<?php
session_start();
include "../db.php";
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin-login.php");
    exit();
}
$admin_name = $_SESSION["admin_name"];
$message = "";
$message_type = "";
/* GET FACULTIES */
$faculty_query = $conn->query(
    "SELECT id, faculty_name
     FROM faculties_info
     ORDER BY faculty_name"
);
/* GET DEPARTMENTS FROM COURSES */
$department_query = $conn->query(
    "SELECT DISTINCT department
     FROM courses
     WHERE department IS NOT NULL
     AND department != ''
     ORDER BY department"
);
/* ADD COURSE */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $faculty = trim($_POST["faculty"]);
    $department = trim($_POST["department"]);
    $level = trim($_POST["level"]);
    $semester = trim($_POST["semester"]);
    $course_code = trim($_POST["course_code"]);
    $course_title = trim($_POST["course_title"]);
    $unit = trim($_POST["unit"]);
    $status = trim($_POST["status"]);
    if (
        empty($faculty) ||
        empty($department) ||
        empty($level) ||
        empty($semester) ||
        empty($course_code) ||
        empty($course_title) ||
        empty($unit) ||
        empty($status)
    ) {
        $message = "Please fill in all fields.";
        $message_type = "error";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO courses
            (faculty, department, level, semester, course_code, course_title, unit, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "ssssssis",
            $faculty,
            $department,
            $level,
            $semester,
            $course_code,
            $course_title,
            $unit,
            $status
        );
        if ($stmt->execute()) {
            $message = "Course added successfully!";
            $message_type = "success";
            $_POST = [];
        } else {
            $message = "Failed to add course: " . $conn->error;
            $message_type = "error";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Course - Admin Panel</title>
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
    .sidebar ul li a:hover,
    .sidebar ul li a.active {
        background: #452066;
    }
    .sidebar ul li a i {
        width: 20px;
        text-align: center;
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
    /* MAIN */
    .main {
        margin-left: 250px;
        padding: 30px;
    }
    /* TOPBAR */
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
    /* FORM */
    .form-container {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        max-width: 1000px;
    }
    .form-container h2 {
        color: #5b2c83;
        margin-bottom: 25px;
    }
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    .form-group {
        display: flex;
        flex-direction: column;
    }
    .form-group.full {
        grid-column: span 2;
    }
    .form-group label {
        margin-bottom: 8px;
        font-weight: bold;
        font-size: 14px;
    }
    .form-group input,
    .form-group select {
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 7px;
        outline: none;
        font-size: 14px;
    }
    .form-group input:focus,
    .form-group select:focus {
        border-color: #5b2c83;
    }
    /* BUTTONS */
    .buttons {
        margin-top: 25px;
        display: flex;
        gap: 12px;
    }
    .save-btn,
    .cancel-btn {
        padding: 12px 20px;
        border-radius: 7px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }
    .save-btn {
        background: #5b2c83;
        color: white;
    }
    .save-btn:hover {
        background: #452066;
    }
    .cancel-btn {
        background: #ddd;
        color: #333;
    }
    /* MESSAGE */
    .message {
        padding: 12px 15px;
        border-radius: 7px;
        margin-bottom: 20px;
        font-size: 14px;
    }
    .success {
        background: #e8f4ea;
        color: #287a3d;
    }
    .error {
        background: #fbe9e7;
        color: #c0392b;
    }
    @media (max-width: 700px) {
        .sidebar {
            width: 210px;
        }
        .main {
            margin-left: 210px;
            padding: 15px;
        }
        .form-grid {
            grid-template-columns: 1fr;
        }
        .form-group.full {
            grid-column: span 1;
        }
        .topbar {
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
        <h1>Add Course</h1>
        <div class="admin-info">
            <i class="fa-solid fa-circle-user"></i>
            <span>
                Welcome,
                <span class="admin-name">
                    <?php echo htmlspecialchars($admin_name); ?>
                </span>
            </span>
        </div>
    </div>
    <div class="form-container">
        <h2>
            <i class="fa-solid fa-book"></i>
            Course Information
        </h2>
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-grid">
                <!-- FACULTY -->
                <div class="form-group">
                    <label>Faculty</label>
                    <select name="faculty" required>
                        <option value="">
                            Select Faculty
                        </option>
                        <?php while ($faculty = $faculty_query->fetch_assoc()): ?>
                            <option
                                value="<?php echo htmlspecialchars($faculty["faculty_name"]); ?>"
                            >
                                <?php echo htmlspecialchars($faculty["faculty_name"]); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <!-- DEPARTMENT -->
                <div class="form-group">
                    <label>Department</label>
                    <select name="department" required>
                        <option value="">
                            Select Department
                        </option>
                        <?php while ($department = $department_query->fetch_assoc()): ?>
                            <option
                                value="<?php echo htmlspecialchars($department["department"]); ?>"
                            >
                                <?php echo htmlspecialchars($department["department"]); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <!-- LEVEL -->
                <div class="form-group">
                    <label>Level</label>
                    <select name="level" required>
                        <option value="">
                            Select Level
                        </option>
                        <option value="100">100 Level</option>
                        <option value="200">200 Level</option>
                        <option value="300">300 Level</option>
                        <option value="400">400 Level</option>
                    </select>
                </div>
                <!-- SEMESTER -->
                <div class="form-group">
                    <label>Semester</label>
                    <select name="semester" required>
                        <option value="">
                            Select Semester
                        </option>
                        <option value="First">First Semester</option>
                        <option value="Second">Second Semester</option>
                    </select>
                </div>
                <!-- COURSE CODE -->
                <div class="form-group">
                    <label>Course Code</label>
                    <input
                        type="text"
                        name="course_code"
                        placeholder="e.g. CSC101"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label>Course Title</label>
                    <input
                        type="text"
                        name="course_title"
                        placeholder="e.g. Introduction to Computer Science"
                        required>
                </div>
                
                <div class="form-group">
                    <label>Course Unit</label>
                    <input
                        type="number"
                        name="unit"
                        min="1"
                        max="6"
                        placeholder="e.g. 3"
                        required>
                </div>


                <div class="form-group">
                    <label>Course Type</label>
                    <select name="status" required>
                        <option value="">
                            Select Course Type
                        </option>
                        <option value="Core">
                            Core
                        </option>
                        <option value="Elective">
                            Elective
                        </option>
                    </select>
                </div>
            </div>

            <div class="buttons">
                <button type="submit" class="save-btn">
                    <i class="fa-solid fa-plus"></i>
                    Add Course
                </button>
                <a href="courses.php" class="cancel-btn">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

</body>
</html>