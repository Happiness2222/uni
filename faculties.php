<?php
include "db.php";

$sql = "SELECT * FROM faculties_info";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Faculties | Evergreen State University</title>
    <link rel="stylesheet" href="faculties.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">
</head>
<body>

<header>
    <div class="logo">
        <i class="fa-solid fa-graduation-cap"></i>
        <h2>Evergreen State University</h2>
    </div>

    <nav>
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="admissions.php">Admission</a>
        <a href="faculties.php" class="active">
            Faculties
        </a>

        <a href="login.html">
            Student Portal
        </a>
    </nav>
</header>


<section class="hero">
    <h1>Our Faculties</h1>
    <p>
        Discover the different faculties available at
        Evergreen State University.
    </p>
</section>

<section class="faculty-container">
    <?php while ($faculty = $result->fetch_assoc()): ?>
        <div class="faculty-card">
            <i class="<?php echo $faculty['icon_class']; ?>"></i>
            <h2>
                <?php echo $faculty['faculty_name']; ?>
            </h2>

            <p>
                <?php echo $faculty['faculty_description']; ?>
            </p>
        </div>
    <?php endwhile; ?>
</section>

<div class="buttons">
    <button id="portalBtn">
        Student Portal →
    </button>
</div>

<footer>
    <p>
        <?php echo date("Y"); ?>
        Evergreen State University
    </p>
</footer>

<script src="faculties.js"></script>

</body>
</html>