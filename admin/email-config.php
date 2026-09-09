<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "../vendor/autoload.php";
require_once "email-secret.php";

function sendEmail($recipientEmail, $recipientName, $subject, $message)
{
    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();
        $mail->Host       = "smtp.gmail.com";
        $mail->SMTPAuth   = true;

        $mail->Username   = "oluwalanah@gmail.com";

        $mail->Password   = GMAIL_APP_PASSWORD;

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender
        $mail->setFrom(
            "oluwalanah@gmail.com",
            "Evergreen University Admissions"
        );


        $mail->addAddress(
            $recipientEmail,
            $recipientName
        );

        $mail->isHTML(true);

        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();

        return true;

    } catch (Exception $e) {

        die("Email Error: " . $mail->ErrorInfo);
    }
}