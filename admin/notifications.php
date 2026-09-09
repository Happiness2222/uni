<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

$message = "";
$message_type = "";


/* =========================
   SAVE NOTIFICATION SETTINGS
   ========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Checkbox = 1 if ON, 0 if OFF
    $system_notifications = isset($_POST['system_notifications']) ? 1 : 0;
    $course_notifications = isset($_POST['course_notifications']) ? 1 : 0;
    $student_notifications = isset($_POST['student_notifications']) ? 1 : 0;
    $announcement_notifications = isset($_POST['announcement_notifications']) ? 1 : 0;


    // Check if settings already exist for this admin
    $check_sql = "SELECT id FROM admin_notification_settings WHERE admin_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $admin_id);
    $check_stmt->execute();

    $check_result = $check_stmt->get_result();


    if ($check_result->num_rows > 0) {

        // Update existing settings
        $update_sql = "
            UPDATE admin_notification_settings
            SET
                system_notifications = ?,
                course_notifications = ?,
                student_notifications = ?,
                announcement_notifications = ?
            WHERE admin_id = ?
        ";

        $update_stmt = $conn->prepare($update_sql);

        $update_stmt->bind_param(
            "iiiii",
            $system_notifications,
            $course_notifications,
            $student_notifications,
            $announcement_notifications,
            $admin_id
        );

        if ($update_stmt->execute()) {
            $message = "Notification preferences saved successfully!";
            $message_type = "success";
        }

        $update_stmt->close();

    } else {

        // Create settings for this admin
        $insert_sql = "
            INSERT INTO admin_notification_settings
            (
                admin_id,
                system_notifications,
                course_notifications,
                student_notifications,
                announcement_notifications
            )
            VALUES (?, ?, ?, ?, ?)
        ";

        $insert_stmt = $conn->prepare($insert_sql);

        $insert_stmt->bind_param(
            "iiiii",
            $admin_id,
            $system_notifications,
            $course_notifications,
            $student_notifications,
            $announcement_notifications
        );

        if ($insert_stmt->execute()) {
            $message = "Notification preferences saved successfully!";
            $message_type = "success";
        }

        $insert_stmt->close();
    }

    $check_stmt->close();
}


/* =========================
   GET SAVED SETTINGS
   ========================= */

$sql = "
    SELECT
        system_notifications,
        course_notifications,
        student_notifications,
        announcement_notifications
    FROM admin_notification_settings
    WHERE admin_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $settings = $result->fetch_assoc();

} else {

    // Default settings
    $settings = [
        'system_notifications' => 1,
        'course_notifications' => 1,
        'student_notifications' => 1,
        'announcement_notifications' => 1
    ];
}

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Notifications</title>

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

        .notification-card {
            background: white;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
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

        .setting {
            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 18px 0;

            border-bottom: 1px solid #eee;
        }

        .setting:last-of-type {
            border-bottom: none;
        }

        .setting-text h3 {
            margin-bottom: 5px;
            font-size: 17px;
        }

        .setting-text p {
            color: #777;
            font-size: 14px;
            line-height: 1.5;
        }


        /* Toggle switch */

        .switch {
            position: relative;
            display: inline-block;

            width: 50px;
            height: 28px;

            flex-shrink: 0;
            margin-left: 15px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;

            cursor: pointer;

            top: 0;
            left: 0;
            right: 0;
            bottom: 0;

            background: #ccc;

            transition: 0.3s;

            border-radius: 30px;
        }

        .slider:before {
            content: "";

            position: absolute;

            height: 22px;
            width: 22px;

            left: 3px;
            bottom: 3px;

            background: white;

            transition: 0.3s;

            border-radius: 50%;
        }

        input:checked + .slider {
            background: #2563eb;
        }

        input:checked + .slider:before {
            transform: translateX(22px);
        }


        .save-btn {
            width: 100%;

            padding: 14px;

            border: none;
            border-radius: 8px;

            background: #2563eb;
            color: white;

            font-size: 16px;

            cursor: pointer;

            margin-top: 25px;
        }

        .save-btn:hover {
            background: #1d4ed8;
        }

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

    <div class="notification-card">

        <div class="header">

            <div class="icon">
                🔔
            </div>

            <h1>Notifications</h1>

            <p>
                Manage your administrator notification preferences.
            </p>

        </div>


        <?php if (!empty($message)): ?>

            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>


        <form method="POST" action="">


            <!-- System Notifications -->

            <div class="setting">

                <div class="setting-text">

                    <h3>
                        System Notifications
                    </h3>

                    <p>
                        Receive important updates about the student portal.
                    </p>

                </div>

                <label class="switch">

                    <input
                        type="checkbox"
                        name="system_notifications"
                        <?php echo $settings['system_notifications'] ? 'checked' : ''; ?>
                    >

                    <span class="slider"></span>

                </label>

            </div>


            <!-- Course Registration -->

            <div class="setting">

                <div class="setting-text">

                    <h3>
                        Course Registration
                    </h3>

                    <p>
                        Receive notifications about student course registration.
                    </p>

                </div>

                <label class="switch">

                    <input
                        type="checkbox"
                        name="course_notifications"
                        <?php echo $settings['course_notifications'] ? 'checked' : ''; ?>
                    >

                    <span class="slider"></span>

                </label>

            </div>


            <!-- Student Activity -->

            <div class="setting">

                <div class="setting-text">

                    <h3>
                        Student Activity
                    </h3>

                    <p>
                        Receive updates about important student activities.
                    </p>

                </div>

                <label class="switch">

                    <input
                        type="checkbox"
                        name="student_notifications"
                        <?php echo $settings['student_notifications'] ? 'checked' : ''; ?>
                    >

                    <span class="slider"></span>

                </label>

            </div>


            <!-- Announcements -->

            <div class="setting">

                <div class="setting-text">

                    <h3>
                        Announcements
                    </h3>

                    <p>
                        Receive important announcements from the portal.
                    </p>

                </div>

                <label class="switch">

                    <input
                        type="checkbox"
                        name="announcement_notifications"
                        <?php echo $settings['announcement_notifications'] ? 'checked' : ''; ?>
                    >

                    <span class="slider"></span>

                </label>

            </div>


            <button type="submit" class="save-btn">
                Save Preferences
            </button>

        </form>


        <a href="settings.php" class="back-btn">
            ← Back to Settings
        </a>

    </div>

</div>

</body>

</html>