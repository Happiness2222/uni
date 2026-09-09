<?php

include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $date_of_birth = $_POST['date_of_birth'];
    $state_of_origin = $_POST['state_of_origin'];
    $programme = $_POST['programme'];
    $jamb_number = $_POST['jamb_number'];
    $olevel_result = $_POST['olevel_result'];
    $address = $_POST['address'];

    // New applications start as Pending
    $status = "Pending";

    $sql = "INSERT INTO admission_applications
            (
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
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {

        $message = "Something went wrong. Please try again.";

    } else {

        $stmt->bind_param(
            "sssssssssss",
            $full_name,
            $email,
            $phone,
            $gender,
            $date_of_birth,
            $state_of_origin,
            $programme,
            $jamb_number,
            $olevel_result,
            $address,
            $status
        );

        if ($stmt->execute()) {

            $message = "Application submitted successfully!";

        } else {

            $message = "Something went wrong. Please try again.";

        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Application | Evergreen State University</title>
    <link rel="stylesheet" href="admission-form.css">
</head>
<body>

<div class="form-container">
    <h1>
        Admission Application
    </h1>

    <p>
        Complete the form below to apply for admission.
    </p>
    <?php if ($message != ""): ?>
        <div class="message">
            <?php
            echo htmlspecialchars($message);
            ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label>
            Full Name
        </label>

        <input
            type="text"
            name="full_name"
            required
        >

        <label>
            Email Address
        </label>

        <input
            type="email"
            name="email"
            required
        >

        <label>
            Phone Number
        </label>

        <input
            type="text"
            name="phone"
            required
        >

        <label>
            Gender
        </label>

        <select
            name="gender"
            required
        >

            <option value="">
                Select Gender
            </option>

            <option value="Male">
                Male
            </option>

            <option value="Female">
                Female
            </option>

        </select>



        <label>
            Date of Birth
        </label>

        <input
            type="date"
            name="date_of_birth"
            required
        >



        <label>
            State of Origin
        </label>

        <input
            type="text"
            name="state_of_origin"
            required
        >



        <label>
            Programme
        </label>

        <input
            type="text"
            name="programme"
            placeholder="e.g. Computer Science"
            required
        >



        <label>
            JAMB/UTME Number
        </label>

        <input
            type="text"
            name="jamb_number"
        >



        <label>
            O'Level Result
        </label>

        <textarea
            name="olevel_result"
            placeholder="Enter your O'Level subjects and grades"
        ></textarea>



        <label>
            Address
        </label>

        <textarea
            name="address"
        ></textarea>



        <div class="form-buttons">

            <button
                type="submit"
            >
                Submit Application
            </button>


            <button
                type="button"
                id="homeBtn"
            >
                Back to Home
            </button>

        </div>


    </form>

</div>

<script>

    document
        .getElementById("homeBtn")
        .addEventListener("click", function () {

            window.location.href = "index.php";

        });

</script>

</body>
</html>