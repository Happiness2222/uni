<?php

session_start();

include "../db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin-login.php");
    exit();
}


// Get all admission applications
$sql = "SELECT
            id,
            full_name,
            email,
            phone,
            gender,
            date_of_birth,
            state_of_origin,
            programme,
            jamb_number,
            olevel_result,
            address,
            application_date,
            status
        FROM admission_applications
        ORDER BY application_date DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Failed to load applications: " . $conn->error);
}


// Count pending applications
$pending_query = $conn->query(
    "SELECT COUNT(*) AS total
     FROM admission_applications
     WHERE status = 'Pending'"
);

$pending_count = $pending_query->fetch_assoc()["total"];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">
    <title>Admission Applications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }


        body {
            background: #f5f3fa;
            color: #333;
        }

        .main {
            padding: 30px;
        }

        .topbar {
            background: white;
            padding: 20px 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }


        .topbar h1 {
            color: #333;
            font-size: 26px;
        }

        .topbar h1 i {
            color: #5b2c83;
            margin-right: 10px;
        }

        .top-buttons {
            display: flex;
            gap: 10px;
        }

        .back-btn,
        .dashboard-btn {
            text-decoration: none;
            padding: 11px 17px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: bold;
            color: white;
        }

        .back-btn {
            background: #6b7280;
        }

        .back-btn:hover {
            background: #4b5563;
        }

        .dashboard-btn {
            background: #16a34a;
        }

        .dashboard-btn:hover {
            background: #15803d;
        }

        .summary {
            background: white;
            padding: 20px 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 15px;
        }


        .summary-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eee5f7;
            color: #5b2c83;
            border-radius: 10px;
            font-size: 21px;
        }

        .summary h3 {
            color: #777;
            font-size: 14px;
            margin-bottom: 5px;
        }


        .summary p {
            color: #5b2c83;
            font-size: 24px;
            font-weight: bold;
        }

        .table-container {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #5b2c83;
            color: white;
            padding: 14px;
            text-align: left;
            font-size: 14px;
        }


        td {
            padding: 14px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }


        tr:hover td {
            background: #faf8fc;
        }

        .status {
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }


        .pending {
            background: #fff3cd;
            color: #856404;
        }


        .approved {
            background: #d1fae5;
            color: #065f46;
        }


        .rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .view-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            background: #5b2c83;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
        }


        .view-btn:hover {
            background: #452066;
        }

        .empty {
            text-align: center;
            padding: 50px 20px;
            color: #777;
        }


        .empty i {
            font-size: 45px;
            color: #ccc;
            margin-bottom: 15px;
        }


        .empty h3 {
            margin-bottom: 8px;
            color: #555;
        }

        @media (max-width: 700px) {

            .main {
                padding: 15px;
            }


            .topbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }


            .top-buttons {
                width: 100%;
                flex-wrap: wrap;
            }

        }

    </style>

</head>
<body>
<div class="main">
    <div class="topbar">
        <h1>
            <i class="fa-solid fa-file-circle-check"></i>
            Admission Applications
        </h1>


        <div class="top-buttons">
            <a
                href="students.php"
                class="back-btn"
            >
                <i class="fa-solid fa-users"></i>
                Students
            </a>

            <a
                href="admin-dashboard.php"
                class="dashboard-btn"
            >

                <i class="fa-solid fa-house"></i>

                Dashboard

            </a>

        </div>

    </div>

    <div class="summary">

        <div class="summary-icon">

            <i class="fa-solid fa-bell"></i>

        </div>


        <div>

            <h3>
                Pending Applications
            </h3>

            <p>
                <?php echo $pending_count; ?>
            </p>

        </div>

    </div>

    <div class="table-container">
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Programme</th>
                        <th>JAMB Number</th>
                        <th>Application Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    <?php

                    $number = 1;

                    while ($row = $result->fetch_assoc()):

                    ?>

                        <tr>

                            <td>
                                <?php echo $number++; ?>
                            </td>


                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row["full_name"]
                                );
                                ?>
                            </td>


                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row["email"]
                                );
                                ?>
                            </td>


                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row["phone"]
                                );
                                ?>
                            </td>


                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row["programme"]
                                );
                                ?>
                            </td>


                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row["jamb_number"]
                                );
                                ?>
                            </td>


                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row["application_date"]
                                );
                                ?>
                            </td>


                            <td>

                                <?php

                                $status = strtolower(
                                    $row["status"]
                                );

                                ?>

                                <span
                                    class="status <?php echo $status; ?>"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $row["status"]
                                    );
                                    ?>

                                </span>

                            </td>


                            <td>

                                <a
                                    href="view-application.php?id=<?php echo $row["id"]; ?>"
                                    class="view-btn"
                                >

                                    <i class="fa-solid fa-eye"></i>

                                    View

                                </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>


        <?php else: ?>

            <div class="empty">

                <i class="fa-solid fa-inbox"></i>

                <h3>
                    No Admission Applications
                </h3>

                <p>
                    There are currently no admission applications.
                </p>
            </div>

        <?php endif; ?>
    </div>
</div>

</body>
</html>