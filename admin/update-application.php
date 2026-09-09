<?php

session_start();

include "../db.php";
include "email-config.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin-login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admission-applications.php");
    exit();
}

$application_id = (int) $_POST["id"];
$action = $_POST["action"];


// Get applicant information
$stmt = $conn->prepare(
    "SELECT
        full_name,
        email,
        programme
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

$stmt->close();


// Determine action
if ($action === "approve") {

    $status = "Approved";

    $subject = "Admission Offer - Evergreen University";

    $message = "
        <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>

            <h2 style='color: #5b2c83;'>
                Evergreen University
            </h2>

            <p>Dear <strong>" .
                htmlspecialchars($application["full_name"]) .
            "</strong>,</p>

            <p>
                We are pleased to inform you that you have been
                <strong>offered admission</strong> to Evergreen University.
            </p>

            <p>
                <strong>Programme:</strong>
                " . htmlspecialchars($application["programme"]) . "
            </p>

            <p>
                Congratulations on your admission! 🎉
            </p>

            <p>
                Please follow the instructions provided by the university
                regarding your admission and registration.
            </p>

            <p>
                We look forward to welcoming you to Evergreen University.
            </p>

            <br>

            <p>
                <strong>Evergreen University Admissions Office</strong>
            </p>

        </div>
    ";

} elseif ($action === "reject") {

    $status = "Rejected";

    $subject = "Admission Application Update - Evergreen University";

    $message = "
        <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>

            <h2 style='color: #5b2c83;'>
                Evergreen University
            </h2>

            <p>Dear <strong>" .
                htmlspecialchars($application["full_name"]) .
            "</strong>,</p>

            <p>
                Thank you for applying to Evergreen University.
            </p>

            <p>
                We regret to inform you that your admission application
                was <strong>not successful</strong> at this time.
            </p>

            <p>
                We appreciate your interest in Evergreen University
                and encourage you to consider applying again in a
                future admission cycle.
            </p>

            <p>
                We wish you success in your future academic pursuits.
            </p>

            <br>

            <p>
                <strong>Evergreen University Admissions Office</strong>
            </p>

        </div>
    ";

} else {

    header("Location: admission-applications.php");
    exit();
}


// Update application status
$stmt = $conn->prepare(
    "UPDATE admission_applications
     SET status = ?
     WHERE id = ?"
);

$stmt->bind_param(
    "si",
    $status,
    $application_id
);


if (!$stmt->execute()) {

    $stmt->close();

    die("Failed to update application: " . $conn->error);
}

$stmt->close();


// Send email
$emailSent = sendEmail(
    $application["email"],
    $application["full_name"],
    $subject,
    $message
);


// Check whether email was sent
if ($emailSent) {

    header(
        "Location: view-application.php?id=" .
        $application_id .
        "&email=sent"
    );

    exit();

} else {

    die(
        "Application status was updated successfully, " .
        "but the email could not be sent."
    );
}

?>