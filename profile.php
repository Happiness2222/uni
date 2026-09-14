<?php
session_start();
include "db.php";

if (!isset($_SESSION["student_id"])) {
    header("Location: login.html");
    exit();
}

$student_id = $_SESSION["student_id"];

/* Generate a CSRF token if one doesn't exist yet */
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

/* Allowed values for select fields (must match the <select> options below) */
$ALLOWED_GENDERS = ["Male", "Female"];
$ALLOWED_LEVELS  = ["100 Level", "200 Level", "300 Level", "400 Level"];

/* Allowed image types for passport upload */
$ALLOWED_MIME_TO_EXT = [
    "image/jpeg" => "jpg",
    "image/png"  => "png",
    "image/webp" => "webp",
];
$MAX_UPLOAD_BYTES = 2 * 1024 * 1024; // 2MB

$errors = [];

/* UPDATE PROFILE */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /* CSRF check */
    if (
        !isset($_POST["csrf_token"]) ||
        !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
    ) {
        die("Invalid or expired form submission. Please refresh the page and try again.");
    }

    $first_name = trim($_POST["first_name"] ?? "");
    $last_name  = trim($_POST["last_name"] ?? "");
    $phone      = trim($_POST["phone"] ?? "");
    $gender     = trim($_POST["gender"] ?? "");
    $faculty    = trim($_POST["faculty"] ?? "");
    $department = trim($_POST["department"] ?? "");
    $level      = trim($_POST["level"] ?? "");
    $passport   = $_FILES["passport"] ?? null;

    if (
        empty($first_name) ||
        empty($last_name) ||
        empty($phone) ||
        empty($gender) ||
        empty($faculty) ||
        empty($department) ||
        empty($level)
    ) {
        $errors[] = "Please fill in all editable fields.";
    }

    /* Whitelist check on constrained fields, since these are POSTed and
       could be sent directly (not just through the <select>) */
    if (!empty($gender) && !in_array($gender, $ALLOWED_GENDERS, true)) {
        $errors[] = "Invalid gender selection.";
    }

    if (!empty($level) && !in_array($level, $ALLOWED_LEVELS, true)) {
        $errors[] = "Invalid level selection.";
    }

    /* Basic sanity limits so free-text fields can't be absurdly long */
    foreach ([
        "first_name" => $first_name,
        "last_name"  => $last_name,
        "phone"      => $phone,
        "faculty"    => $faculty,
        "department" => $department,
    ] as $field => $value) {
        if (mb_strlen($value) > 100) {
            $errors[] = "The $field field is too long.";
        }
    }

    /*
     * Handle passport upload, if one was provided.
     * We never trust the client-supplied filename or MIME type.
     */
    $passportPath = null;
    $oldPassportPath = $student["passport"] ?? null;

    if ($passport && $passport["error"] !== UPLOAD_ERR_NO_FILE) {

        if ($passport["error"] !== UPLOAD_ERR_OK) {
            $errors[] = "There was a problem uploading the passport photograph.";
        } elseif ($passport["size"] > $MAX_UPLOAD_BYTES) {
            $errors[] = "Passport photograph must be smaller than 2MB.";
        } else {
            /* Verify the real file content type, not the client-sent one */
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $detectedMime = $finfo->file($passport["tmp_name"]);

            if (!isset($ALLOWED_MIME_TO_EXT[$detectedMime])) {
                $errors[] = "Passport photograph must be a JPG, PNG, or WEBP image.";
            } else {
                $ext = $ALLOWED_MIME_TO_EXT[$detectedMime];

                /* Fully server-generated filename — never derived from user input */
                $fileName = bin2hex(random_bytes(16)) . "." . $ext;
                $uploadDir = __DIR__ . "/uploads/";
                $uploadPath = $uploadDir . $fileName;

                if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
                    $errors[] = "Server error: could not prepare upload directory.";
                } elseif (!move_uploaded_file($passport["tmp_name"], $uploadPath)) {
                    $errors[] = "Failed to upload passport photograph.";
                } else {
                    $passportPath = "uploads/" . $fileName;
                }
            }
        }
    }

    if (!empty($errors)) {
        /* Clean up an uploaded file if validation failed elsewhere */
        if ($passportPath !== null && file_exists(__DIR__ . "/" . $passportPath)) {
            unlink(__DIR__ . "/" . $passportPath);
        }
        die(htmlspecialchars(implode(" ", $errors)));
    }

    /*
     * Update the record. If a new passport was uploaded, update that
     * column too; otherwise leave the existing passport untouched.
     */
    if ($passportPath !== null) {

        $update = $conn->prepare(
            "UPDATE students
             SET first_name = ?,
                 last_name = ?,
                 phone = ?,
                 gender = ?,
                 faculty = ?,
                 department = ?,
                 level = ?,
                 passport = ?
             WHERE id = ?"
        );

        if (!$update) {
            error_log("Prepare failed: " . $conn->error);
            die("A server error occurred. Please try again later.");
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

    } else {

        $update = $conn->prepare(
            "UPDATE students
             SET first_name = ?,
                 last_name = ?,
                 phone = ?,
                 gender = ?,
                 faculty = ?,
                 department = ?,
                 level = ?
             WHERE id = ?"
        );

        if (!$update) {
            error_log("Prepare failed: " . $conn->error);
            die("A server error occurred. Please try again later.");
        }

        $update->bind_param(
            "sssssssi",
            $first_name,
            $last_name,
            $phone,
            $gender,
            $faculty,
            $department,
            $level,
            $student_id
        );
    }

    if (!$update->execute()) {
        error_log("Update failed: " . $update->error);
        /* Roll back the newly uploaded file if the DB write failed */
        if ($passportPath !== null && file_exists(__DIR__ . "/" . $passportPath)) {
            unlink(__DIR__ . "/" . $passportPath);
        }
        die("Failed to update profile. Please try again later.");
    }

    $update->close();

    /* Only delete the old passport file after the new one is safely saved */
    if (
        $passportPath !== null &&
        !empty($oldPassportPath) &&
        file_exists(__DIR__ . "/" . $oldPassportPath)
    ) {
        unlink(__DIR__ . "/" . $oldPassportPath);
    }

    header("Location: profile.php?updated=success");
    exit();
}


/* GET STUDENT INFORMATION */
$sql = "SELECT * FROM students WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    die("A server error occurred. Please try again later.");
}

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

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css"
    >

</head>

<body>

<div class="container">

    <!-- SIDEBAR -->
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


    <!-- MAIN CONTENT -->
    <main class="main-content">

        <div class="profile-card">

            <h1>Student Profile</h1>

            <form
                id="profileForm"
                method="POST"
                action="profile.php"
                enctype="multipart/form-data"
            >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?php echo htmlspecialchars($_SESSION["csrf_token"]); ?>"
                >

                <!-- PASSPORT -->
                <div class="input-group passport-group">

                    <label>Passport Photograph</label>

                    <?php if (!empty($student["passport"])): ?>

                        <div class="passport-preview">

                            <img
                                src="<?php echo htmlspecialchars($student["passport"]); ?>"
                                alt="Passport Photograph"
                            >

                        </div>

                    <?php endif; ?>

                    <input
                        type="file"
                        id="passport"
                        name="passport"
                        accept="image/jpeg,image/png,image/webp"
                    >

                    <small>JPG, PNG, or WEBP. Max 2MB.</small>

                </div>


                <!-- FIRST NAME -->
                <div class="input-group">

                    <label>First Name</label>

                    <input
                        type="text"
                        id="firstname"
                        name="first_name"
                        value="<?php echo htmlspecialchars($student["first_name"]); ?>"
                        maxlength="100"
                        required
                    >

                </div>


                <!-- LAST NAME -->
                <div class="input-group">

                    <label>Last Name</label>

                    <input
                        type="text"
                        id="lastname"
                        name="last_name"
                        value="<?php echo htmlspecialchars($student["last_name"]); ?>"
                        maxlength="100"
                        required
                    >

                </div>


                <!-- EMAIL -->
                <div class="input-group">

                    <label>Email Address</label>

                    <input
                        type="email"
                        id="email"
                        value="<?php echo htmlspecialchars($student["email"]); ?>"
                        readonly
                    >

                </div>


                <!-- PHONE -->
                <div class="input-group">

                    <label>Phone Number</label>

                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        value="<?php echo htmlspecialchars($student["phone"]); ?>"
                        maxlength="100"
                        required
                    >

                </div>


                <!-- GENDER -->
                <div class="input-group">

                    <label>Gender</label>

                    <select
                        id="gender"
                        name="gender"
                        required
                    >

                        <option
                            value="Male"
                            <?php if ($student["gender"] == "Male") echo "selected"; ?>
                        >
                            Male
                        </option>

                        <option
                            value="Female"
                            <?php if ($student["gender"] == "Female") echo "selected"; ?>
                        >
                            Female
                        </option>

                    </select>

                </div>


                <!-- FACULTY -->
                <div class="input-group">

                    <label>Faculty</label>

                    <input
                        type="text"
                        id="faculty"
                        name="faculty"
                        value="<?php echo htmlspecialchars($student["faculty"]); ?>"
                        maxlength="100"
                        required
                    >

                </div>


                <!-- DEPARTMENT -->
                <div class="input-group">

                    <label>Department</label>

                    <input
                        type="text"
                        id="department"
                        name="department"
                        value="<?php echo htmlspecialchars($student["department"]); ?>"
                        maxlength="100"
                        required
                    >

                </div>


                <!-- LEVEL -->
                <div class="input-group">

                    <label>Level</label>

                    <select
                        id="level"
                        name="level"
                        required
                    >

                        <option
                            value="100 Level"
                            <?php if ($student["level"] == "100 Level") echo "selected"; ?>
                        >
                            100 Level
                        </option>

                        <option
                            value="200 Level"
                            <?php if ($student["level"] == "200 Level") echo "selected"; ?>
                        >
                            200 Level
                        </option>

                        <option
                            value="300 Level"
                            <?php if ($student["level"] == "300 Level") echo "selected"; ?>
                        >
                            300 Level
                        </option>

                        <option
                            value="400 Level"
                            <?php if ($student["level"] == "400 Level") echo "selected"; ?>
                        >
                            400 Level
                        </option>

                    </select>

                </div>


                <!-- REGISTRATION NUMBER -->
                <div class="input-group">

                    <label>Registration Number</label>

                    <input
                        type="text"
                        id="regno"
                        value="<?php echo htmlspecialchars($student["registration_number"]); ?>"
                        readonly
                    >

                </div>


                <!-- MATRIC NUMBER -->
                <div class="input-group">

                    <label>Matric Number</label>

                    <input
                        type="text"
                        id="matric"
                        value="<?php echo htmlspecialchars($student["matric_number"]); ?>"
                        readonly
                    >

                </div>


                <!-- SAVE -->
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