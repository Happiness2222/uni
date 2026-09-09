<?php
include "db.php";

$sql = "SELECT * FROM about_info LIMIT 1";
$result = $conn->query($sql);

$about = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Evergreen State University</title>
    <link rel="stylesheet" href="about.css">
    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">
</head>

<body>

<header>
    <div class="logo">
        <i class="fa-solid fa-graduation-cap"></i>

        <h2>
            Evergreen State University
        </h2>
    </div>

    <nav>
        <ul>
            <li>
                <a href="index.php">Home</a>
            </li>

            <li>
                <a href="about.php" class="active">About</a>
            </li>

            <li>
                <a href="admissions.html">Admissions</a>
            </li>

            <li>
                <a href="faculties.html">Faculties</a>
            </li>

            <li>
                <a href="login.html">Student Portal</a>
            </li>
        </ul>
    </nav>
</header>

<section class="hero">

    <div class="hero-content">

        <h1>
            <?php echo $about['hero_title']; ?>
        </h1>

        <p>
            <?php echo $about['hero_description']; ?>
        </p>

    </div>

</section>

<section class="about-container">

    <div class="about-card">

        <h2>
            <?php echo $about['who_title']; ?>
        </h2>

        <p>
            <?php echo $about['who_description']; ?>
        </p>

    </div>

    <div class="about-grid">
        <div class="card">

            <i class="fa-solid fa-bullseye"></i>

            <h3>
                <?php echo $about['mission_title']; ?>
            </h3>

            <p>
                <?php echo $about['mission_description']; ?>
            </p>

        </div>

        <div class="card">

            <i class="fa-solid fa-eye"></i>

            <h3>
                <?php echo $about['vision_title']; ?>
            </h3>

            <p>
                <?php echo $about['vision_description']; ?>
            </p>

        </div>

        <div class="card">
            <i class="fa-solid fa-star"></i>

            <h3>
                <?php echo $about['values_title']; ?>
            </h3>

            <p>
                <?php echo $about['values_description']; ?>
            </p>

        </div>

    </div>

    <div class="why">

        <h2>
            <?php echo $about['why_title']; ?>
        </h2>


        <div class="why-grid">
            <div class="why-card">

                <i class="fa-solid fa-user-graduate"></i>

                <h3>
                   <?php echo $about['why1_title']; ?>
                </h3>

                <p>
                   <?php echo $about['why1_description']; ?>
                </p>

        </div>

            <div class="why-card">
                <i class="fa-solid fa-laptop-code"></i>
                <h3>
                   <?php echo $about['why2_title']; ?>
                </h3>

                <p>
                    <?php echo $about['why2_description']; ?>
                </p>

            </div>

            <div class="why-card">
                <i class="fa-solid fa-earth-africa"></i>
                <h3>
                    <?php echo $about['why3_title']; ?>
                </h3>

                <p>
                    <?php echo $about['why3_description']; ?>
                </p>
            </div>
        </div>

    </div>
    <div class="statistics">

        <div class="stat">

            <h2><?php echo $about['stat1_value'] ?></h2>

            <p>
               <?php echo $about['stat1_label'] ?>
            </p>

        </div>


        <div class="stat">

            <h2><?php echo $about['stat2_value'] ?></h2>

            <p>
               <?php echo $about['stat2_label'] ?>
            </p>

        </div>


        <div class="stat">

            <h2><?php echo $about['stat3_value'] ?></h2>

            <p>
                <?php echo $about['stat3_label'] ?>
            </p>

        </div>


        <div class="stat">

            <h2><?php echo $about['stat4_value'] ?></h2>

            <p>
                <?php echo $about['stat4_label'] ?>
            </p>

        </div>

    </div>


    <!-- BUTTON -->

    <div class="buttons">

        <button id="aboutBtn">
            Back To Home
        </button>

    </div>

</section>

<footer>

    <h3>
        Evergreen State University
    </h3>

    <p>
        Learn Today. Lead Tomorrow.
    </p>

    <p>
        &copy; <?php echo date("Y"); ?>
        Evergreen State University.
        All Rights Reserved.
    </p>

</footer>


<script src="about.js"></script>

</body>
</html>