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

// Check if edit mode is requested
$edit_mode = isset($_GET['edit']) && $_GET['edit'] === '1';

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);

    if (empty($full_name) || empty($email)) {

        $message = "Please fill in all fields.";
        $message_type = "error";
        $edit_mode = true;

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";
        $edit_mode = true;

    } else {

        $update_sql = "UPDATE admins SET full_name = ?, email = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);

        if (!$update_stmt) {

            $message = "Database error: " . $conn->error;
            $message_type = "error";
            $edit_mode = true;

        } else {

            $update_stmt->bind_param("ssi", $full_name, $email, $admin_id);

            if ($update_stmt->execute()) {

                $message = "Profile updated successfully!";
                $message_type = "success";
                $edit_mode = false;

            } else {

                $message = "Failed to update profile.";
                $message_type = "error";
                $edit_mode = true;
            }

            $update_stmt->close();
        }
    }
}

// Get admin information
$sql = "SELECT id, full_name, email, created_at FROM admins WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("i", $admin_id);
$stmt->execute();

$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if (!$admin) {
    die("Admin account not found.");
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Profile</title>

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

        .profile-card {
            background: white;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-icon {
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

        .profile-header h1 {
            font-size: 26px;
            margin-bottom: 5px;
        }

        .profile-header p {
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

        .error {
            background: #fee2e2;
            color: #991b1b;
        }

        .info-group {
            margin-bottom: 20px;
        }

        .info-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .info-box {
            background: #f5f7fb;
            padding: 14px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .edit-btn,
        .save-btn {
            display: block;
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            background: #2563eb;
            color: white;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            margin-top: 10px;
        }

        .edit-btn:hover,
        .save-btn:hover {
            background: #1d4ed8;
        }

        .cancel-btn {
            display: block;
            width: 100%;
            padding: 14px;
            margin-top: 10px;

            border: 1px solid #ddd;
            border-radius: 8px;

            background: white;
            color: #555;

            font-size: 16px;
            text-align: center;
            text-decoration: none;
        }

        .cancel-btn:hover {
            background: #f5f5f5;
        }

        .edit-form input {
            width: 100%;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
        }

        .edit-form input:focus {
            border-color: #2563eb;
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

        <div class="profile-card">

            <div class="profile-header">

                <div class="profile-icon">
                    👤
                </div>

                <h1>Admin Profile</h1>

                <p>
                    View and manage your administrator account.
                </p>

            </div>


            <?php if (!empty($message)): ?>

                <div class="message <?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>

            <?php endif; ?>


            <?php if ($edit_mode): ?>

                <!-- EDIT MODE -->

                <form method="POST" action="" class="edit-form">

                    <div class="info-group">

                        <label for="full_name">
                            Full Name
                        </label>

                        <input
                            type="text"
                            id="full_name"
                            name="full_name"
                            value="<?php echo htmlspecialchars($admin['full_name']); ?>"
                            required
                        >

                    </div>


                    <div class="info-group">

                        <label for="email">
                            Email Address
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?php echo htmlspecialchars($admin['email']); ?>"
                            required
                        >

                    </div>


                    <button type="submit" class="save-btn">
                        Save Changes
                    </button>


                    <a href="admin-profile.php" class="cancel-btn">
                        Cancel
                    </a>

                </form>


            <?php else: ?>

                <!-- VIEW MODE -->

                <div class="info-group">

                    <label>Full Name</label>

                    <div class="info-box">
                        <?php echo htmlspecialchars($admin['full_name']); ?>
                    </div>

                </div>


                <div class="info-group">

                    <label>Email Address</label>

                    <div class="info-box">
                        <?php echo htmlspecialchars($admin['email']); ?>
                    </div>

                </div>


                <div class="info-group">

                    <label>Account Created</label>

                    <div class="info-box">
                        <?php echo htmlspecialchars($admin['created_at']); ?>
                    </div>

                </div>


                <a href="admin-profile.php?edit=1" class="edit-btn">
                    Edit Profile
                </a>

            <?php endif; ?>


            <a href="settings.php" class="back-btn">
                ← Back to Settings
            </a>

        </div>

    </div>

</body>

</html>