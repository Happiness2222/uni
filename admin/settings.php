<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

$sql = "SELECT id, full_name, email FROM admins WHERE id = ?";
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Settings</title>

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

        .settings-container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
        }

        .settings-header {
            margin-bottom: 30px;
        }

        .settings-header h1 {
            font-size: 30px;
            margin-bottom: 8px;
        }

        .settings-header p {
            color: #777;
        }

        .settings-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .setting-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            color: #333;
            display: block;
        }

        .setting-card:hover {
            transform: translateY(-3px);
        }

        .setting-icon {
            font-size: 30px;
            margin-bottom: 15px;
        }

        .setting-card h2 {
            font-size: 20px;
            margin-bottom: 8px;
        }

        .setting-card p {
            color: #777;
            font-size: 14px;
            line-height: 1.5;
        }

        @media (max-width: 700px) {
            .settings-grid {
                grid-template-columns: 1fr;
            }
        }

        
    </style>
</head>

<body>
    <div class="settings-container">

        <div class="settings-header">
            <h1>⚙️ Settings</h1>
            <p>Manage your admin account and portal preferences.</p>
        </div>

        <div class="settings-grid">

            <!-- Admin Profile -->
            <a href="admin-profile.php" class="setting-card">
                <div class="setting-icon">👤</div>

                <h2>Admin Profile</h2>

                <p>
                    View and update your admin name and email address.
                </p>
            </a>


            <!-- Change Password -->
            <a href="change-password.php" class="setting-card">
                <div class="setting-icon">🔐</div>

                <h2>Change Password</h2>

                <p>
                    Update your admin account password.
                </p>
            </a>


            <!-- Notifications -->
            <a href="notifications.php" class="setting-card">
                <div class="setting-icon">🔔</div>

                <h2>Notifications</h2>

                <p>
                    Manage your system notification preferences.
                </p>
            </a>


            <!-- Appearance
            <a href="appearance.php" class="setting-card">
                <div class="setting-icon">🎨</div>

                <h2>Appearance</h2>

                <p>
                    Customize the portal appearance, including light and dark mode.
                </p>
            </a> -->


            <!-- General Settings -->
            <a href="general-settings.php" class="setting-card">
                <div class="setting-icon">⚙️</div>

                <h2>General Settings</h2>

                <p>
                    Manage general portal information and academic settings.
                </p>
            </a>

        </div>

    </div>

</body>

</html>