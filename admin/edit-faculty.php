<?php
session_start();
include "../db.php";

if(!isset($_SESSION["admin_id"])) {
    header("Location: admin-login.php");
    exit();
}

if(!isset($_GET["id"])) {
    header("Location: faculties.php");
    exit();
}

$id = (int) $_GET["id"];

$error = "";

$stmt = $conn->prepare(
    "SELECT
        id,
        faculty_name,
        faculty_description,
        icon_class
    FROM faculties_info
    WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows === 0) {
    header("Location: faculties.php");
    exit();
}

$faculty = $result->fetch_assoc();

$stmt->close();

if($_SERVER["REQUEST_METHOD"] == "POST") {

$faculty_name = trim($_POST["faculty_name"]);
$faculty_description = trim($_POST["faculty_description"]);
$icon_class = trim($_POST["icon_class"]);

if (empty($faculty_name)) {

$error = "Please enter a faculty name.";
}else {
    $check = $conn->prepare(
        "SELECT id
        FROM faculties_info
        WHERE faculty_name = ?
        AND id != ?"
    );
    $check->bind_param(
        "si",
        $faculty_name,
        $id
    );
    $check->execute();
    $check_result = $check->get_result();

    if($check_result->num_rows > 0) {
        $error = "This faculty already exits.";
    }else {
        $update = $conn->prepare(
            "UPDATE faculties_info
            SET faculty_name = ?,
                faculty_description = ?,
                icon_class = ?
            WHERE id = ?"
        );

        $update->bind_param(
            "sssi",
            $faculty_name,
            $faculty_description,
            $icon_class,
            $id
        );

        if ($update->execute()) {
            header("Location: faculties.php");
            exit();
        } else {
            $error = "Failed to update faculty: " . $conn->error;
        }
        $update->close();
    }
    $check->close();
}
} 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Faculty | Admin</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            color: #333;
        }

        .container {
            max-width: 700px;
            margin: 50px auto;
            padding: 20px;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }

        h1 {
            margin-bottom: 8px;
        }

        .subtitle {
            color: #777;
            margin-bottom: 25px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        input,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 15px;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .submit-btn {
            background: #2563eb;
            color: white;
            border: none;
            padding: 13px 22px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }

        .submit-btn:hover {
            background: #1d4ed8;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>

</head>
<body>

<div class="container">
    <a href="faculties.php" class="back-btn">
        ← Back to Faculties
    </a>

    <div class="card">
        <h1>Edit Faculty</h1>
        <p class="subtitle">
            Update faculty information.
        </p>

        <?php if ($error != ""): ?>
            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="faculty_name">
                Faculty Name
            </label>

            <input
                type="text"
                name="faculty_name"
                id="faculty_name"
                value="<?php echo htmlspecialchars($faculty["faculty_name"]); ?>"
                required
            >

            <label for="faculty_description">
                Faculty Description
            </label>

            <textarea
                name="faculty_description"
                id="faculty_description"
            ><?php echo htmlspecialchars($faculty["faculty_description"] ?? ""); ?></textarea>

            <label for="icon_class">
                Icon Class
            </label>

            <input
                type="text"
                name="icon_class"
                id="icon_class"
                value="<?php echo htmlspecialchars($faculty["icon_class"] ?? ""); ?>"
                placeholder="e.g. fa-solid fa-flask"
            >

            <button type="submit" class="submit-btn">
                Save Changes
            </button>
        </form>
    </div>
</div>

</body>
</html>