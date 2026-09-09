<?php

session_start();

include "../db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin-login.php");
    exit();
}

$error = "";


/* =========================
   ADD DEPARTMENT
========================= */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $faculty_id = (int) $_POST["faculty_id"];
    $department_name = trim($_POST["department_name"]);
    $department_description = trim($_POST["department_description"]);

    if ($faculty_id <= 0 || empty($department_name)) {

        $error = "Please select a faculty and enter a department name.";

    } else {

        $check_stmt = $conn->prepare(
            "SELECT id
             FROM departments
             WHERE faculty_id = ?
             AND department_name = ?"
        );

        $check_stmt->bind_param(
            "is",
            $faculty_id,
            $department_name
        );

        $check_stmt->execute();

        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {

            $error = "This department already exists under the selected faculty.";

        } else {

            $stmt = $conn->prepare(
                "INSERT INTO departments
                (faculty_id, department_name, department_description)
                VALUES (?, ?, ?)"
            );

            $stmt->bind_param(
                "iss",
                $faculty_id,
                $department_name,
                $department_description
            );

            if ($stmt->execute()) {

                header("Location: departments.php");
                exit();

            } else {

                $error = "Failed to add department.";

            }

            $stmt->close();
        }

        $check_stmt->close();
    }
}


/* =========================
   GET FACULTIES
========================= */

$faculty_query = $conn->query(
    "SELECT id, faculty_name
     FROM faculties_info
     ORDER BY faculty_name"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Department | Admin</title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

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
            margin-bottom: 25px;
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
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
            margin-bottom: 8px;
            font-weight: bold;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 15px;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .submit-btn {
            background: #2563eb;
            color: white;
            border: none;
            padding: 13px 22px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: bold;
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

    <a href="departments.php" class="back-btn">

        <i class="fa-solid fa-arrow-left"></i>

        Back to Departments

    </a>


    <div class="card">

        <h1>Add Department</h1>

        <p class="subtitle">
            Add a new department to a faculty.
        </p>


        <?php if ($error != ""): ?>

            <div class="error">

                <?php echo htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>


        <form method="POST">

            <label for="faculty_id">
                Select Faculty
            </label>

            <select
                name="faculty_id"
                id="faculty_id"
                required
            >

                <option value="">
                    -- Select Faculty --
                </option>

                <?php while ($faculty = $faculty_query->fetch_assoc()): ?>

                    <option value="<?php echo $faculty["id"]; ?>">

                        <?php
                        echo htmlspecialchars(
                            $faculty["faculty_name"]
                        );
                        ?>

                    </option>

                <?php endwhile; ?>

            </select>


            <label for="department_name">
                Department Name
            </label>

            <input
                type="text"
                name="department_name"
                id="department_name"
                placeholder="e.g. Computer Science"
                required
            >


            <label for="department_description">
                Department Description
            </label>

            <textarea
                name="department_description"
                id="department_description"
                placeholder="Enter a short description..."
            ></textarea>


            <button type="submit" class="submit-btn">

                <i class="fa-solid fa-plus"></i>

                Add Department

            </button>

        </form>

    </div>

</div>

</body>

</html>