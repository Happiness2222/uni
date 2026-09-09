<?php
session_start();
include "../db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin-login.php");
    exit();
}

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: admission-applications.php");
    exit();
}

$application_id = (int) $_GET["id"];

$stmt = $conn->prepare(
    "SELECT
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
     WHERE id = ?"
);

$stmt->bind_param("i", $application_id);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Application not found.");
}

$application = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>View Application</title>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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
            font-size: 26px;
            color: #333;
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
            color: white;

            padding: 11px 17px;

            border-radius: 8px;

            display: inline-flex;
            align-items: center;
            gap: 8px;

            font-weight: bold;
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

        .application-card {
            background: white;
            border-radius: 12px;
            padding: 30px;

            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }


        .section-title {
            color: #5b2c83;
            font-size: 19px;

            margin-bottom: 20px;
            padding-bottom: 10px;

            border-bottom: 2px solid #eee;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }


        .detail-box {
            background: #faf9fc;

            padding: 16px;

            border-radius: 8px;

            border: 1px solid #eee;
        }


        .detail-box.full {
            grid-column: 1 / -1;
        }


        .detail-label {
            display: block;

            color: #777;

            font-size: 13px;

            margin-bottom: 7px;
        }


        .detail-value {
            color: #333;

            font-size: 15px;

            font-weight: bold;

            word-break: break-word;
        }

        .status {
            display: inline-block;

            padding: 7px 13px;

            border-radius: 20px;

            font-size: 12px;

            font-weight: bold;
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

        .actions {
            margin-top: 30px;

            padding-top: 25px;

            border-top: 1px solid #eee;

            display: flex;

            gap: 12px;
        }


        .action-btn {
            border: none;
            padding: 12px 18px;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }


        .approve-btn {
            background: #16a34a;
        }


        .approve-btn:hover {
            background: #15803d;
        }


        .reject-btn {
            background: #d9534f;
        }


        .reject-btn:hover {
            background: #c9302c;
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
                flex-wrap: wrap;
            }


            .details-grid {
                grid-template-columns: 1fr;
            }


            .detail-box.full {
                grid-column: auto;
            }


            .actions {
                flex-direction: column;
            }

        }

    </style>

</head>
<body>
<div class="main">
    <div class="topbar">

        <h1>

            <i class="fa-solid fa-file-lines"></i>

            Application Details

        </h1>


        <div class="top-buttons">

            <a
                href="admission-applications.php"
                class="back-btn"
            >

                <i class="fa-solid fa-arrow-left"></i>

                Back to Applications

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

    <div class="application-card">


        <h2 class="section-title">

            <i class="fa-solid fa-user"></i>

            Applicant Information

        </h2>


        <div class="details-grid">
            <div class="detail-box">

                <span class="detail-label">
                    Full Name
                </span>

                <span class="detail-value">

                    <?php
                    echo htmlspecialchars(
                        $application["full_name"]
                    );
                    ?>

                </span>

            </div>

            <div class="detail-box">

                <span class="detail-label">
                    Email Address
                </span>

                <span class="detail-value">

                    <?php
                    echo htmlspecialchars(
                        $application["email"]
                    );
                    ?>

                </span>

            </div>

            <div class="detail-box">

                <span class="detail-label">
                    Phone Number
                </span>

                <span class="detail-value">

                    <?php
                    echo htmlspecialchars(
                        $application["phone"]
                    );
                    ?>

                </span>

            </div>

            <div class="detail-box">

                <span class="detail-label">
                    Gender
                </span>

                <span class="detail-value">

                    <?php
                    echo htmlspecialchars(
                        $application["gender"]
                    );
                    ?>

                </span>

            </div>

            <div class="detail-box">

                <span class="detail-label">
                    Date of Birth
                </span>

                <span class="detail-value">

                    <?php
                    echo htmlspecialchars(
                        $application["date_of_birth"]
                    );
                    ?>

                </span>

            </div>

            <div class="detail-box">

                <span class="detail-label">
                    State of Origin
                </span>

                <span class="detail-value">

                    <?php
                    echo htmlspecialchars(
                        $application["state_of_origin"]
                    );
                    ?>

                </span>

            </div>

            <div class="detail-box">

                <span class="detail-label">
                    Programme
                </span>

                <span class="detail-value">

                    <?php
                    echo htmlspecialchars(
                        $application["programme"]
                    );
                    ?>

                </span>

            </div>

            <div class="detail-box">

                <span class="detail-label">
                    JAMB Number
                </span>

                <span class="detail-value">

                    <?php
                    echo htmlspecialchars(
                        $application["jamb_number"]
                    );
                    ?>

                </span>

            </div>

            <div class="detail-box full">

                <span class="detail-label">
                    O'Level Result
                </span>

                <span class="detail-value">

                    <?php
                    echo nl2br(
                        htmlspecialchars(
                            $application["olevel_result"]
                        )
                    );
                    ?>

                </span>

            </div>

            <div class="detail-box full">

                <span class="detail-label">
                    Address
                </span>

                <span class="detail-value">

                    <?php
                    echo nl2br(
                        htmlspecialchars(
                            $application["address"]
                        )
                    );
                    ?>

                </span>

            </div>

            <div class="detail-box">

                <span class="detail-label">
                    Application Date
                </span>

                <span class="detail-value">

                    <?php
                    echo htmlspecialchars(
                        $application["application_date"]
                    );
                    ?>

                </span>

            </div>

            <div class="detail-box">

                <span class="detail-label">
                    Application Status
                </span>


                <?php

                $status = strtolower(
                    $application["status"]
                );

                ?>


                <span
                    class="status <?php echo $status; ?>"
                >

                    <?php
                    echo htmlspecialchars(
                        $application["status"]
                    );
                    ?>

                </span>

            </div>

        </div>

       <?php if ($application["status"] === "Pending"): ?>

    <div class="actions">

        <!-- APPROVE BUTTON -->

        <form
            method="POST"
            action="update-application.php"
        >

            <input
                type="hidden"
                name="id"
                value="<?php echo $application["id"]; ?>"
            >

            <input
                type="hidden"
                name="action"
                value="approve"
            >

            <button
                type="submit"
                class="action-btn approve-btn"
                onclick="return confirm('Are you sure you want to approve this application?');"
            >

                <i class="fa-solid fa-check"></i>

                Approve Application

            </button>

        </form>


        <!-- REJECT BUTTON -->

        <form
            method="POST"
            action="update-application.php"
        >

            <input
                type="hidden"
                name="id"
                value="<?php echo $application["id"]; ?>"
            >

            <input
                type="hidden"
                name="action"
                value="reject"
            >

            <button
                type="submit"
                class="action-btn reject-btn"
                onclick="return confirm('Are you sure you want to reject this application?');"
            >

                <i class="fa-solid fa-xmark"></i>

                Reject Application

            </button>

        </form>

    </div>

<?php endif; ?>
</div>

</body>
</html>