<?php
session_start();

include "../db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin-login.php");
    exit();
}

$sql = "SELECT
             departments.id,
             departments.department_name,
             departments.department_description,
             faculties_info.faculty_name
        FROM departments
        INNER JOIN faculties_info
            ON departments.faculty_id = faculties_info.id
        ORDER BY faculties_info.faculty_name, departments.department_name";

$result = $conn->query($sql);

if (!$result) {
    die("Failed to load departments: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font_awesome/6.5.2/css/all.min.css">

    <style>

        *{
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body{
            font-family: Arial, sans-serif;
            background: #f5f7fb;
        }

        .main-content{
            padding: 30px;
        }

        .page-header{
            display: flex;
            justify-content: space-between;
            aliign-items: center;
            margin-botton: 25px;
        }

        .page-header h1{
            font-size: 28px;
            color: #222;
        }

        .page-header p{
            color: #777;
            margin-top: 5px;
        }

        add-btn{
            background: #2563eb;
            color: white;
            padding: 12px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }

        .add-btn:hover{
            background: #1d4ed8;
        }

        .table-container{
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
            overflow-x: auto;
        }

        table{
            width: 100%
            border-collape: collape;
        }

        th,
        td{
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th{
            background: #f8fafc;
            color: #444;
        }

        td{
            color: #555;
        }

         .actions{
            display: flex;
            gap: 8px;
        }

         .edit-btn,
         delete-btn{
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px
            text-decoration: none;
            color: white;
        }

        .edit-btn{
            background: #2563eb;
        }

        .delete-btn{
            background: #dc2626;
        }

        .edit-btn:hover{
            background: #1d4ed8;
        }

        .delete-btn:hover{
            background: #b91c1c;
        }

        .empty{
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

            <h1>Department</h1>
            <p>Manage university department</p>
        </div>

        <a href="add-department.php" class="add-btn">
            <i class="fa-solid fa-plus"></i>
            Add Department
        </a>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Department</th>
                    <th>Faculty</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            <thead>

            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php $number = 1; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php echo $number++; ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars(
                                    $row["department_name"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars(
                                    $row["faculty_name"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars(
                                    $row["department_description"] ?? ""
                                );
                                ?>
                            </td>

                            <td>

                                <div class="actions">

                                    <a
                                        href="edit-department.php?id=<?php echo $row["id"]; ?>"
                                        class="edit-btn"
                                        title="Edit Department">

                                        <i class="fa-solid fa-pen"></i>
                                    </a>

                                    <a
                                        href="delete-department.php?id=<?php echo $row["id"]; ?>"
                                        class="delete_btn"
                                        title="Delete Department"
                                        onclick="return confirm('Are you sure you want to delete this department?');">

                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>

                    <?php endwhile; ?>
                <?php else: ?>

                    <tr>

                        <td colspan="5" class="empty">
                            No departments found.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>
        </table>
    </div>
</body>
</html>