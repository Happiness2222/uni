<?php
session_start();
include "db.php";

if (!isset($_SESSION["student_id"])) {
    header("Location: login.html");
    exit();
}

$student_id = $_SESSION["student_id"];

/* UPDATE PROFILE */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $first_name = trim($_POST["first_name"] ?? "");
    $last_name = trim($_POST["last_name"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $gender = trim($_POST["gender"] ?? "");
    $faculty = trim($_POST["faculty"] ?? "");
    $department = trim($_POST["department"] ?? "");
    $level = trim($_POST["level"] ?? "");
    $passport = $_FILES["passport"] ?? null;

    $passportPath = null;

    if($passport && $passport["error"] === UPLOAD_ERR_OK) {
        $fileName = time() . "_" . basename($passport["name"]);
        $uploadPath = "uploads/" . $fileName;

        if (move_uploaded_file($passport["tmp_name"], $uploadPath)) {
            $passportPath = $uploadPath;
        }
    }

    if (
        empty($first_name) ||
        empty($last_name) ||
        empty($phone) ||
        empty($gender) ||
        empty($faculty) ||
        empty($department) ||
        empty($level)
    ) {
        die("Please fill in all editable fields.");
    }

    $update = $conn->prepare(
        "UPDATE students 
         SET first_name = ?,
             last_name = ?,
             phone = ?,
             gender = ?,
             faculty = ?,
             department = ?,
             level = ?,
             `passport` = ?
         WHERE id = ?"
    );

    if (!$update) {
        die("Update error: " . $conn->error);
    }

    $update->bind_param(
        "ssssssssi",
        $first_name,
        $last_name,
        $phone,
        $gender,
        $faculty,
        $department,
        $level,
        $passportPath,
        $student_id
    );

    if (!$update->execute()) {
        die("Failed to update profile.");
    }

    $update->close();

    /* Refresh the page with the new information */
    header("Location: profile.php?updated=success");
    exit();
}


/* GET STUDENT INFORMATION */
$sql = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Student not found.");
}

$student = $result->fetch_assoc();

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Evergreen State University</title>
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">
</head>
<body>

<div class="container">
<aside class="sidebar">
    <div class="logo">
        <i class="fa-solid fa-graduation-cap"></i>
        <h2>Evergreen State University</h2>
    </div>

    <ul>
        <li>
            <a href="dashboard.php">
                <i class="fa-solid fa-house"></i>
                Dashboard
            </a>
        </li>

        <li class="active">
            <a href="profile.php">
                <i class="fa-solid fa-user"></i>
                Profile
            </a>
        </li>

        <li>
            <a href="e-matriculation.php">
                <i class="fa-solid fa-file-signature"></i>
                E-Matriculation
            </a>
        </li>

        <li>
            <a href="course-registration.php">
                <i class="fa-solid fa-book-open"></i>
                Course Registration
            </a>
        </li>

        <li>
            <a href="results.php">
                <i class="fa-solid fa-square-poll-vertical"></i>
                Check Results
            </a>
        </li>

        <li>
            <a href="password.php">
                <i class="fa-solid fa-lock"></i>
                Change Password
            </a>
        </li>

        <li id="logoutBtn">
            <i class="fa-solid fa-right-from-bracket"></i>
            Logout
        </li>
    </ul>
</aside>


<main class="main-content">

    <div class="profile-card">

        <h1>Student Profile</h1>

        <form id="profileForm" method="POST" action="profile.php" enctype="multipart/form-data">

        <div class="input-group">
            <label>Passport Photograph</label>
            <input type="file" id="passport" name="passport" accept="image/*">
        </div>

            <div class="input-group">
                <label>First Name</label>
                <input type="text" id="firstname" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
            </div>

            <div class="input-group">
                <label>Last Name</label>
                <input type="text" id="lastname" name="last_name" value="<?php echo htmlspecialchars($student["last_name"]); ?>" required>
            </div>

            <div class="input-group">
                <label>Email Address</label>
                <input type="email" id="email" value="<?php echo htmlspecialchars($student["email"]); ?>" readonly>
            </div>

            <div class="input-group">
                <label>Phone Number</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($student["phone"]); ?>" required>
            </div>

            <div class="input-group">
                <label>Gender</label>
                <select id="gender" name="gender" required>
                    <option value="Male" <?php if ($student["gender"] == "Male") echo "selected"; ?>>
                        Male
                    </option>

                    <option value="Female" <?php if ($student["gender"] == "Female") echo "selected"; ?>>
                        Female
                    </option>
                </select>
            </div>

            <div class="input-group">
                <label>Faculty</label>
                <input type="text" id="faculty" name="faculty" value="<?php echo htmlspecialchars($student["faculty"]); ?>" required>
            </div>

            <div class="input-group">
                <label>Department</label>
                <input type="text" id="department" name="department" value="<?php echo htmlspecialchars($student["department"]); ?>" required>
            </div>

            <div class="input-group">
                <label>Level</label>
                <select id="level" name="level" required>

                    <option value="100 Level"
                        <?php if ($student["level"] == "100 Level") echo "selected"; ?>>
                        100 Level
                    </option>

                    <option value="200 Level"
                        <?php if ($student["level"] == "200 Level") echo "selected"; ?>>
                        200 Level
                    </option>

                    <option value="300 Level"
                        <?php if ($student["level"] == "300 Level") echo "selected"; ?>>
                        300 Level
                    </option>

                    <option value="400 Level"
                        <?php if ($student["level"] == "400 Level") echo "selected"; ?>>
                        400 Level
                    </option>

                </select>
            </div>

            <div class="input-group">
                <label>Registration Number</label>
                <input type="text" id="regno" value="<?php echo htmlspecialchars($student["registration_number"]); ?>" readonly>
            </div>

            <div class="input-group">
                <label>Matric Number</label>
                <input type="text" id="matric" value="<?php echo htmlspecialchars($student["matric_number"]); ?>" readonly>
            </div>

            <button type="submit">
                Save Changes
            </button>
        </form>
    </div>
</main>
</div>

<script src="profile.js"></script>

</body>
</html>