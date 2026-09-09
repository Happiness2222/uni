<?php

session_start();

include "../db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin-login.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $faculty_name = trim($_POST["faculty_name"]);
    $faculty_description = trim($_POST["faculty_description"]);
    $icon_class = trim($_POST["icon_class"]);

    if (empty($faculty_name)) {

        $error = "Please enter a faculty name.";

    } else {

        // Check if faculty already exists
        $check = $conn->prepare(
            "SELECT id FROM faculties_info WHERE faculty_name = ?"
        );

        $check->bind_param("s", $faculty_name);
        $check->execute();

        $check_result = $check->get_result();

        if ($check_result->num_rows > 0) {

            $error = "This faculty already exists.";

        } else {

            $stmt = $conn->prepare(
                "INSERT INTO faculties_info
                (faculty_name, faculty_description, icon_class)
                VALUES (?, ?, ?)"
            );

            $stmt->bind_param(
                "sss",
                $faculty_name,
                $faculty_description,
                $icon_class
            );

            if ($stmt->execute()) {

                header("Location: faculties.php");
                exit();

            } else {

                $error = "Failed to add faculty: " . $conn->error;

            }

            $stmt->close();
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

    <title>Add Faculty | Admin</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
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

        .hint {
            display: block;
            color: #888;
            font-size: 13px;
            margin-top: -15px;
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

        <h1>Add Faculty</h1>

        <p class="subtitle">
            Add a new faculty to the university.
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
                placeholder="e.g. Faculty of Science"
                required
            >


            <label for="faculty_description">
                Faculty Description
            </label>

            <textarea
                name="faculty_description"
                id="faculty_description"
                placeholder="Enter a short description..."
            ></textarea>


            <label for="icon_class">
                Icon Class
            </label>

            <input
                type="text"
                name="icon_class"
                id="icon_class"
                placeholder="e.g. fa-solid fa-flask"
            >

            <small class="hint">
                Font Awesome icon class. You can leave this empty.
            </small>


            <button type="submit" class="submit-btn">
                + Add Faculty
            </button>

        </form>

    </div>

</div>

</body>

</html>