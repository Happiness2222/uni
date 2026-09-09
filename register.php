<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $faculty = trim($_POST["faculty"]);
    $department = trim($_POST["department"]);
    $registration_number = trim($_POST["registration_number"]);
    $matric_number = trim($_POST["matric_number"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $phone = trim($_POST["phone"]);
    $gender = trim($_POST["gender"]);
    $level = trim($_POST["level"]);

    if (
        empty($first_name) ||
        empty($last_name) ||
        empty($faculty) ||
        empty($department) ||
        empty($registration_number) ||
        empty($matric_number) ||
        empty($email) ||
        empty($password) ||
        empty($phone) ||
        empty($gender) ||
        empty($level)
    ) {

        die("Please fill in all fields.");

    }
    $check = $conn->prepare(
        "SELECT id FROM students 
         WHERE email = ? 
         OR registration_number = ? 
         OR matric_number = ?"
    );

    $check->bind_param(
        "sss",
        $email,
        $registration_number,
        $matric_number
    );

    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        die("Email, registration number, or matric number already exists.");
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = $conn->prepare(

        "INSERT INTO students 
        (first_name, last_name, faculty, department, registration_number, matric_number, email, password, phone, gender, level)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"

    );

    $sql->bind_param(
        "sssssssssss",
        $first_name,
        $last_name,
        $faculty,
        $department,
        $registration_number,
        $matric_number,
        $email,
        $hashed_password,
        $phone,
        $gender,
        $level
    );

    if ($sql->execute()) {
        header("Location: login.html?registered=success");
        exit();
    } else {
        echo "Registration failed. Please try again.";
    }

    $sql->close();
    $check->close();
}
$conn->close();
?>