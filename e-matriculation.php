<?php
session_start();
include "db.php";

if(!isset($_SESSION["student_id"])) {
    header("Location: login.html");
    exit();
}

$student_id = $_SESSION["student_id"];

$sql = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1){
    session_destroy();
    header("Location: login.html");
    exit();
}

$student = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Matriculation | Evergreen State University</title>
    <link rel="stylesheet" href="e-matriculation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">
</head>
<body>

<div class="container">
    <div class="matric-card">
        <h1>Evergreen State University</h1>
        <h2>E-Matriculation Form</h2>
 
        <div class="passport">
            <?php if (!empty($student["passport"])): ?>
                <img src="<?php echo htmlspecialchars($student["passport"]); ?>"
                    alt="Passport"
                    id="passport">
                <?php else: ?>
                    <p>NO passport uploaded</p>
                <?php endif; ?>
                </div>                
        
        <table>
            <tr>
                <td><strong>Full Name</strong></td>
                <td>
                    <?php echo htmlspecialchars($student["first_name"]. "" . $student["last_name"]); ?>
                </td>
            </tr>

            <tr>
                <td><strong>Registration Number</strong></td>
                <td>
                    <?php echo htmlspecialchars($student["registration_number"]); ?>
                </td>
            </tr>

            <tr>
                <td><strong>Matric Number</strong></td>
                <td>
                    <?php echo htmlspecialchars($student["matric_number"]); ?>
                </td>
            </tr>

            <tr>
                <td><strong>Faculty</strong></td>
                <td>
                    <?php echo htmlspecialchars($student["faculty"]); ?>
                </td>
            </tr>

            <tr>
                <td><strong>Department</strong></td>
                <td>
                    <?php echo htmlspecialchars($student["department"]); ?>
                </td>
            </tr>

            <tr>
                <td><strong>Level</strong></td>
                <td>
                    <?php echo htmlspecialchars($student["level"]); ?>
                </td>
            </tr>

            <tr>
                <td><strong>Academic Session</strong></td>
                <td>2026/2027</td>
            </tr>
        </table>

        <div class="oath">
            <h3>Matriculation Oath</h3>
            <p>
                I solemnly promise to obey all the rules and regulations
                of Evergreen State University and to conduct myself
                as a responsible and worthy student throughout my stay
                in the University.
            </p>
        </div>

        <div class="buttons">
            <button onclick="window.print()">
                <i class="fa-solid fa-print"></i>
                Print
            </button>

            <button onclick="window.location.href='dashboard.php'">
                <i class="fa-solid fa-arrow-left"></i>
                Dashboard
            </button>
        </div>
    </div>
</div>

<!-- <script src="e-matriculation.js"></script> -->

</body>
</html>