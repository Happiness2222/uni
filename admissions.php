<?php
include "db.php";

$sql = "SELECT * FROM admissions_info LIMIT 1";
$result = $conn->query($sql);

$admission = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo $admission['hero_title']; ?> | Evergreen State University
    </title>

    <link rel="stylesheet" href="admissions.css">
</head>

<body>

<header>

    <div class="logo">
        <h2>Evergreen State University</h2>
    </div>

    <nav>
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="admissions.php" class="active">Admissions</a>
        <a href="faculties.html">Faculties</a>
        <a href="login.html">Student Portal</a>
    </nav>

</header>


<section class="hero">

    <h1>
        <?php echo $admission['hero_title']; ?>
    </h1>

    <p>
        <?php echo $admission['hero_description']; ?>
    </p>

</section>


<section class="admission-container">

    <!-- Undergraduate Admission -->

    <div class="card">

        <h2>
            <?php echo $admission['undergraduate_title']; ?>
        </h2>

        <p>
            <?php echo $admission['undergraduate_description']; ?>
        </p>

    </div>


    <!-- Admission Requirements -->

    <div class="card">

        <h2>
            <?php echo $admission['requirements_title']; ?>
        </h2>

        <ul>

            <li>
                <?php echo $admission['requirement1']; ?>
            </li>

            <li>
                <?php echo $admission['requirement2']; ?>
            </li>

            <li>
                <?php echo $admission['requirement3']; ?>
            </li>

            <li>
                <?php echo $admission['requirement4']; ?>
            </li>

            <li>
                <?php echo $admission['requirement5']; ?>
            </li>

        </ul>

    </div>


    <!-- Admission Process -->

    <div class="card">

        <h2>
            🗓 <?php echo $admission['process_title']; ?>
        </h2>

        <ol>

            <li>
                <?php echo $admission['process1']; ?>
            </li>

            <li>
                <?php echo $admission['process2']; ?>
            </li>

            <li>
                <?php echo $admission['process3']; ?>
            </li>

            <li>
                <?php echo $admission['process4']; ?>
            </li>

            <li>
                <?php echo $admission['process5']; ?>
            </li>

        </ol>

    </div>

</section>


<div class="buttons">

    <button id="applyBtn">
        Apply Now →
    </button>

</div>


<footer>

    <p>
        <?php echo date("Y"); ?> Evergreen State University
    </p>

</footer>


<script src="admissions.js"></script>

</body>
</html>