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

// Handle password change
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check that all fields are filled
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {

        $message = "Please fill in all fields.";
        $message_type = "error";

    } elseif ($new_password !== $confirm_password) {

        $message = "New passwords do not match.";
        $message_type = "error";

    } else {

        // Get current password from database
        $sql = "SELECT password FROM admins WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Database error: " . $conn->error);
        }

        $stmt->bind_param("i", $admin_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if (!$admin) {

            $message = "Admin account not found.";
            $message_type = "error";

        } elseif (!password_verify($current_password, $admin['password'])) {

            $message = "Current password is incorrect.";
            $message_type = "error";

        } else {

            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update password
            $update_sql = "UPDATE admins SET password = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);

            if (!$update_stmt) {
                die("Database error: " . $conn->error);
            }

            $update_stmt->bind_param("si", $hashed_password, $admin_id);

            if ($update_stmt->execute()) {

                $message = "Password changed successfully!";
                $message_type = "success";

            } else {

                $message = "Failed to change password.";
                $message_type = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Change Password</title>

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
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }

        .password-card {
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
            font-size: 45px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 27px;
            margin-bottom: 8px;
        }

        .header p {
            color: #777;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 13px;

            border: 1px solid #ddd;
            border-radius: 8px;

            font-size: 15px;
            outline: none;
        }

        .form-group input:focus {
            border-color: #2563eb;
        }

        .update-btn {
            width: 100%;
            padding: 14px;

            border: none;
            border-radius: 8px;

            background: #2563eb;
            color: white;

            font-size: 16px;
            cursor: pointer;
        }

        .update-btn:hover {
            background: #1d4ed8;
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

        .error {
            background: #fee2e2;
            color: #991b1b;
        }

        .back-btn {
            display: block;
            text-align: center;
            margin-top: 20px;

            color: #2563eb;
            text-decoration: none;
        }

        .back-btn:hover {
            text-decoration: underline;
        }

    </style>

</head>

<body>

    <div class="container">

        <div class="password-card">

            <div class="header">

                <div class="icon">
                    🔐
                </div>

                <h1>Change Password</h1>

                <p>Update your administrator account password.</p>

            </div>


            <?php if (!empty($message)): ?>

                <div class="message <?php echo $message_type; ?>">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php endif; ?>


            <form method="POST" action="">

                <div class="form-group">

                    <label for="current_password">
                        Current Password
                    </label>

                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        placeholder="Enter your current password"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="new_password">
                        New Password
                    </label>

                    <input
                        type="password"
                        id="new_password"
                        name="new_password"
                        placeholder="Enter your new password"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="confirm_password">
                        Confirm New Password
                    </label>

                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="Confirm your new password"
                        required
                    >

                </div>


                <button type="submit" class="update-btn">
                    Update Password
                </button>

            </form>


            <a href="settings.php" class="back-btn">
                ← Back to Settings
            </a>

        </div>

    </div>

</body>

</html>