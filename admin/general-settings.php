<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

$message = "";
$message_type = "";


/* =========================
   SAVE GENERAL SETTINGS
   ========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $university_name = trim($_POST['university_name'] ?? '');
    $academic_session = trim($_POST['academic_session'] ?? '');
    $semester = trim($_POST['semester'] ?? '');
    $registration_status = $_POST['registration_status'] ?? 'Open';


    // Validate fields

    if (
        empty($university_name) ||
        empty($academic_session) ||
        empty($semester)
    ) {

        $message = "Please fill in all fields.";
        $message_type = "error";

    } elseif (
        $registration_status !== 'Open' &&
        $registration_status !== 'Closed'
    ) {

        $message = "Invalid registration status.";
        $message_type = "error";

    } else {

        // Check if settings already exist

        $check_sql = "SELECT id FROM general_settings LIMIT 1";
        $check_result = $conn->query($check_sql);

        if ($check_result && $check_result->num_rows > 0) {

            // Update existing settings

            $row = $check_result->fetch_assoc();
            $settings_id = $row['id'];

            $update_sql = "
                UPDATE general_settings
                SET
                    university_name = ?,
                    academic_session = ?,
                    semester = ?,
                    registration_status = ?
                WHERE id = ?
            ";

            $update_stmt = $conn->prepare($update_sql);

            if ($update_stmt) {

                $update_stmt->bind_param(
                    "ssssi",
                    $university_name,
                    $academic_session,
                    $semester,
                    $registration_status,
                    $settings_id
                );

                if ($update_stmt->execute()) {

                    $message = "General settings saved successfully!";
                    $message_type = "success";

                } else {

                    $message = "Failed to save settings.";
                    $message_type = "error";
                }

                $update_stmt->close();

            } else {

                $message = "Database error: " . $conn->error;
                $message_type = "error";
            }

        } else {

            // Insert settings for the first time

            $insert_sql = "
                INSERT INTO general_settings
                (
                    university_name,
                    academic_session,
                    semester,
                    registration_status
                )
                VALUES (?, ?, ?, ?)
            ";

            $insert_stmt = $conn->prepare($insert_sql);

            if ($insert_stmt) {

                $insert_stmt->bind_param(
                    "ssss",
                    $university_name,
                    $academic_session,
                    $semester,
                    $registration_status
                );

                if ($insert_stmt->execute()) {

                    $message = "General settings saved successfully!";
                    $message_type = "success";

                } else {

                    $message = "Failed to save settings.";
                    $message_type = "error";
                }

                $insert_stmt->close();

            } else {

                $message = "Database error: " . $conn->error;
                $message_type = "error";
            }
        }
    }
}


/* =========================
   LOAD SAVED SETTINGS
   ========================= */

$sql = "
    SELECT
        university_name,
        academic_session,
        semester,
        registration_status
    FROM general_settings
    LIMIT 1
";

$result = $conn->query($sql);


if ($result && $result->num_rows > 0) {

    $settings = $result->fetch_assoc();

} else {

    // Default values

    $settings = [
        'university_name' => '',
        'academic_session' => '2026/2027',
        'semester' => 'First Semester',
        'registration_status' => 'Open'
    ];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>General Settings</title>


    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f5f7fb;
            color: #333;
        }

        .container {
            max-width: 700px;
            margin: 50px auto;
            padding: 20px;
        }

        .settings-card {
            background: white;
            padding: 35px;

            border-radius: 15px;

            box-shadow:
                0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .icon {
            width: 80px;
            height: 80px;

            margin: 0 auto 15px;

            border-radius: 50%;

            background: #eeeeee;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 40px;
        }

        .header h1 {
            font-size: 26px;
            margin-bottom: 8px;
        }

        .header p {
            color: #777;
        }


        /* Messages */

        .message {
            padding: 12px;

            border-radius: 8px;

            margin-bottom: 20px;

            text-align: center;
        }

        .success {
            background: #dcfce7;
            color: #166534;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
        }


        /* Form */

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;

            font-weight: bold;

            margin-bottom: 8px;
        }

        .form-group input,
        .form-group select {

            width: 100%;

            padding: 14px;

            border: 1px solid #ddd;

            border-radius: 8px;

            font-size: 15px;

            outline: none;

            background: white;
        }

        .form-group input:focus,
        .form-group select:focus {

            border-color: #2563eb;
        }


        /* Save button */

        .save-btn {

            width: 100%;

            padding: 14px;

            border: none;

            border-radius: 8px;

            background: #2563eb;

            color: white;

            font-size: 16px;

            cursor: pointer;

            margin-top: 5px;
        }

        .save-btn:hover {

            background: #1d4ed8;
        }


        /* Back button */

        .back-btn {

            display: block;

            text-align: center;

            margin-top: 20px;

            text-decoration: none;

            color: #2563eb;
        }

        .back-btn:hover {

            text-decoration: underline;
        }

    </style>

</head>


<body>

<div class="container">

    <div class="settings-card">


        <!-- Header -->

        <div class="header">

            <div class="icon">
                ⚙️
            </div>

            <h1>
                General Settings
            </h1>

            <p>
                Manage general information and academic settings.
            </p>

        </div>


        <!-- Message -->

        <?php if (!empty($message)): ?>

            <div class="message <?php echo $message_type; ?>">

                <?php
                echo htmlspecialchars($message);
                ?>

            </div>

        <?php endif; ?>


        <!-- Form -->

        <form method="POST" action="">


            <!-- University Name -->

            <div class="form-group">

                <label for="university_name">
                    University Name
                </label>

                <input
                    type="text"
                    id="university_name"
                    name="university_name"
                    value="<?php
                        echo htmlspecialchars(
                            $settings['university_name']
                        );
                    ?>"
                    placeholder="Enter university name"
                    required
                >

            </div>


            <!-- Academic Session -->

            <div class="form-group">

                <label for="academic_session">
                    Academic Session
                </label>

                <input
                    type="text"
                    id="academic_session"
                    name="academic_session"
                    value="<?php
                        echo htmlspecialchars(
                            $settings['academic_session']
                        );
                    ?>"
                    placeholder="e.g. 2026/2027"
                    required
                >

            </div>


            <!-- Semester -->

            <div class="form-group">

                <label for="semester">
                    Current Semester
                </label>

                <select
                    id="semester"
                    name="semester"
                >

                    <option
                        value="First Semester"
                        <?php
                        echo (
                            $settings['semester']
                            === 'First Semester'
                        ) ? 'selected' : '';
                        ?>
                    >
                        First Semester
                    </option>

                    <option
                        value="Second Semester"
                        <?php
                        echo (
                            $settings['semester']
                            === 'Second Semester'
                        ) ? 'selected' : '';
                        ?>
                    >
                        Second Semester
                    </option>

                </select>

            </div>


            <!-- Registration Status -->

            <div class="form-group">

                <label for="registration_status">
                    Course Registration Status
                </label>

                <select
                    id="registration_status"
                    name="registration_status"
                >

                    <option
                        value="Open"
                        <?php
                        echo (
                            $settings['registration_status']
                            === 'Open'
                        ) ? 'selected' : '';
                        ?>
                    >
                        Open
                    </option>

                    <option
                        value="Closed"
                        <?php
                        echo (
                            $settings['registration_status']
                            === 'Closed'
                        ) ? 'selected' : '';
                        ?>
                    >
                        Closed
                    </option>

                </select>

            </div>


            <!-- Save -->

            <button
                type="submit"
                class="save-btn"
            >
                Save General Settings
            </button>

        </form>


        <!-- Back -->

        <a
            href="settings.php"
            class="back-btn"
        >
            ← Back to Settings
        </a>


    </div>

</div>

</body>

</html>