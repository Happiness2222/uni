<?php

session_start();

include "../db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin-login.php");
    exit();
}

// Get all students
$sql = "SELECT 
            id,
            first_name,
            last_name,
            email,
            registration_number,
            matric_number,
            faculty,
            department,
            phone,
            gender,
            level,
            passport
        FROM students
        ORDER BY first_name, last_name";

$result = $conn->query($sql);

if (!$result) {
    die("Failed to load students: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students | Admin Dashboard</title>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            color: #333;
        }

        .container {
            width: 100%;
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 28px;
            color: #222;
        }

        .header p {
            margin-top: 6px;
            color: #777;
        }

        .student-count {
            background: #2563eb;
            color: white;
            padding: 12px 18px;
            border-radius: 8px;
            font-weight: bold;
        }

        .table-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            overflow-x: auto;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1100px;
        }

        th,
        td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #f8fafc;
            color: #444;
            font-size: 14px;
        }

        td {
            font-size: 14px;
        }

        tr:hover {
            background: #f9fbff;
        }

        .passport {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #eee;
        }

        .no-passport {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #777;
        }

        .empty {
            text-align: center;
            padding: 40px;
            color: #777;
        }

        .header-right{
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .dashboard-btn{
            text-decoration: none;
            background: #2563eb;
            color: white;
            padding: 12px 18px;
            border-radius: 8px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dashboard-btn:hover{
            background: #1e40af;
        }

        .search-container{
            background: white;
            padding: 12px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 300px;
        }

        .search-container i{
            color: #777;
            font-size: 16px;
        }

        .search-container input{
            width: 100%;
            border: none;
            outline: none;
            font-size: 15px;
            background: transparent;
        }

        .view-btn{
            text-decoration: none;
            background: #2563eb;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .view-btn:hover{
            background: #1d4ed8;
        }
    </style>

</head>
<body>

<div class="header">
    <div>
        <h1>
            <i class="fa-solid fa-users"></i>
            Students
        </h1>

        <p>Manage registered students</p>
    </div>

    <div class="header-right">
        <a href="admin-dashboard.php" class="dashboard-btn">
            <i class="fa-solid fa-house"></i>
            Dashboard
        </a>

        <div class="student-count">
            Total Students: <?php echo $result->num_rows; ?>
        </div>
    </div>
</div>

    <div class="table-container">

    <div class="search-container">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input
            type="text"
            id="studentSearch"
            placeholder="Search students by name, matric number, department...">
    </div>

        <table>
            <thead>
                <tr>
                    <th>Passport</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Registration No.</th>
                    <th>Matric No.</th>
                    <th>Faculty</th>
                    <th>Department</th>
                    <th>Phone</th>
                    <th>Gender</th>
                    <th>Level</th>
                    <th>Action</th>
                </tr>

            </thead>

            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($student = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php if (!empty($student["passport"])): ?>
                                    <img
                                        src="../uploads/<?php echo htmlspecialchars($student["passport"]); ?>"
                                        class="passport"
                                        alt="Passport"
                                    >
                                <?php else: ?>
                                    <div class="no-passport">
                                        <i class="fa-solid fa-user"></i>
                                    </div>

                                <?php endif; ?>

                            </td>


                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $student["first_name"] . " " . $student["last_name"]
                                );
                                ?>
                            </td>


                            <td>
                                <?php echo htmlspecialchars($student["email"]); ?>
                            </td>


                            <td>
                                <?php echo htmlspecialchars($student["registration_number"]); ?>
                            </td>


                            <td>
                                <?php echo htmlspecialchars($student["matric_number"]); ?>
                            </td>


                            <td>
                                <?php echo htmlspecialchars($student["faculty"]); ?>
                            </td>


                            <td>
                                <?php echo htmlspecialchars($student["department"]); ?>
                            </td>


                            <td>
                                <?php echo htmlspecialchars($student["phone"]); ?>
                            </td>


                            <td>
                                <?php echo htmlspecialchars($student["gender"]); ?>
                            </td>


                            <td>
                                <?php echo htmlspecialchars($student["level"]); ?>
                            </td>

                            <td>
                                <a href="view-student.php?id=<?php echo $student["id"]; ?>" class="view-btn">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="10" class="empty">
                            <i class="fa-solid fa-users"></i>
                            <br><br>
                            No students found.
                        </td>
                    </tr>

                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<script>

    const searchInput = document.getElementById("studentSearch");

    searchInput.addEventListener("keyup", function () {

        const searchValue = this.value.toLowerCase();

        const rows = document.querySelectorAll("tbody tr");

        rows.forEach(function (row) {
            const rowText = row.textContent.toLowerCase();

            if (rowText.includes(searchValue)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
                    }          
        });

   });
   
   </script>

</body>
</html>