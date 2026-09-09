<?php

session_start();

include "../db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin-login.php");
    exit();
}


/* =========================
   GET FACULTIES
========================= */

$sql = "SELECT
            id,
            faculty_name,
            faculty_description,
            icon_class
        FROM faculties_info
        ORDER BY faculty_name";

$result = $conn->query($sql);

if (!$result) {
    die("Failed to load faculties: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Faculties | Admin</title>

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

        .main-content {
            padding: 30px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .page-header h1 {
            font-size: 28px;
            color: #222;
        }

        .page-header p {
            color: #777;
            margin-top: 5px;
        }

        .add-btn {
            background: #2563eb;
            color: white;
            padding: 12px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }

        .add-btn:hover {
            background: #1d4ed8;
        }

        .table-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #f8fafc;
            color: #444;
        }

        td {
            color: #555;
        }

        .faculty-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .edit-btn,
        .delete-btn {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            text-decoration: none;
            font-size: 18px;
        }

        .edit-btn {
            background: #2563eb;
            color: white;
        }

        .delete-btn {
            background: #dc2626;
            color: white;
        }

        .edit-btn:hover {
            background: #1d4ed8;
        }

        .delete-btn:hover {
            background: #b91c1c;
        }

        .empty {
            text-align: center;
            padding: 30px;
            color: #888;
        }

    </style>

</head>

<body>

<div class="main-content">

    <div class="page-header">

        <div>

            <h1>Faculties</h1>

            <p>Manage university faculties</p>

        </div>


        <a href="add-faculty.php" class="add-btn">

            <i class="fa-solid fa-plus"></i>

            Add Faculty

        </a>

    </div>


    <div class="table-container">

        <table>

            <thead>

                <tr>

                    <th>#</th>

                    <th>Icon</th>

                    <th>Faculty</th>

                    <th>Description</th>

                    <th>Actions</th>

                </tr>

            </thead>


            <tbody>

                <?php if ($result->num_rows > 0): ?>

                    <?php $number = 1; ?>

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php echo $number++; ?>
                            </td>


                            <td>

                                <div class="faculty-icon">

                                    <?php if (!empty($row["icon_class"])): ?>

                                        <i class="<?php echo htmlspecialchars($row["icon_class"]); ?>"></i>

                                    <?php else: ?>

                                        🏫

                                    <?php endif; ?>

                                </div>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $row["faculty_name"]
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $row["faculty_description"] ?? ""
                                );
                                ?>

                            </td>


                            <td>

                                <div class="actions">

                                    <a
                                        href="edit-faculty.php?id=<?php echo $row["id"]; ?>"
                                        class="edit-btn"
                                        title="Edit Faculty"
                                    >
                                        ✎
                                    </a>


                                    <a
                                        href="delete-faculty.php?id=<?php echo $row["id"]; ?>"
                                        class="delete-btn"
                                        title="Delete Faculty"
                                        onclick="return confirm('Are you sure you want to delete this faculty?');"
                                    >
                                        🗑
                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="5" class="empty">

                            No faculties found.

                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>

</html>