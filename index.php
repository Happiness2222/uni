<?php
include "db.php";

$sql = "SELECT * FROM university_info LIMIT 1";
$result = $conn->query($sql);

$university = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evergreen State University</title>

    <link rel="stylesheet" href="index.css">

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <header>
        <div class="logo">
            <i class="fa-solid fa-graduation-cap"></i>
            <h2>Evergreen State University</h2>
        </div>
        <nav>
            <ul>
                <li>
                    <a href="index.php">Home</a>
                </li>
                <li>
                    <a href="about.php">About</a>
                </li>
                <li>
                    <a href="admissions.php">Admissions</a>
                </li>
                <li>
                    <a href="faculties.php">Faculties</a>
                </li>
                <li>
                    <a href="login.php">Student Portal</a>
                </li>
            </ul>
        </nav>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h3>WELCOME TO</h3>
            <h1><?php echo $university['university_name']; ?></h1>
            <h2><?php echo $university['tagline']; ?></h2>
            <p>
                <?php echo $university['description']; ?>
            </p>

            <div class="hero-buttons">
                <a href="login.html" class="btn">
                    Student Portal
                </a>

                <a href="register.html" class="btn secondary">
                    Apply Now
                </a>
            </div>
        </div>
    </section>

    <section class="features">
        <div class="card">
            <i class="fa-solid fa-book-open"></i>
            <h2><?php echo $university['academic_title']; ?></h2>
            <p>
                <?php echo $university['academic_description']; ?>
            </p>
        </div>

        <div class="card">
            <i class="fa-solid fa-user-graduate"></i>
            <h2><?php echo $university['admissions_title']; ?></h2>
            <p>
                <?php echo $university['admissions_description']; ?>
            </p>
        </div>

        <div class="card">
            <i class="fa-solid fa-laptop"></i>
            <h2><?php echo $university['portal_title']; ?></h2>
            <p>
                <?php echo $university['portal_description']; ?>
            </p>
        </div>

        <div class="card">
            <i class="fa-solid fa-building-columns"></i>
            <h2><?php echo $university['faculties_title']; ?></h2>
            <p>
                <?php echo $university['faculties_description']; ?>
            </p>
        </div>
    </section>

    <section class="why-us">
        <h2>Why Choose Evergreen?</h2>
        <div class="why-container">
            <div class="reason">
                <i class="fa-solid fa-check"></i>
                <span><?php echo $university['reason1']; ?></span>
            </div>

            <div class="reason">
                <i class="fa-solid fa-check"></i>
                <span><?php echo $university['reason2']; ?></span>
            </div>

            <div class="reason">
                <i class="fa-solid fa-check"></i>
                <span><?php echo $university['reason3']; ?></span>
            </div>

            <div class="reason">
                <i class="fa-solid fa-check"></i>
                <span><?php echo $university['reason4']; ?></span>
            </div>

            <div class="reason">
                <i class="fa-solid fa-check"></i>
                <span><?php echo $university['reason5']; ?></span>
            </div>

            <div class="reason">
                <i class="fa-solid fa-check"></i>
                <span><?php echo $university['reason6']; ?></span>
            </div>
        </div>
    </section>

    <footer>
        <h3><?php echo $university['university_name']; ?></h3>
        <p><?php echo $university['tagline']; ?></p>
        <p>Email: <?php echo $university['email']; ?></p>
        <p>Phone: <?php echo $university['phone']; ?></p>

        <p>
            <?php echo date("Y"); ?>
            <?php echo $university['university_name'] ?>.
            All Rights Reserved.
        </p>
    </footer>

    <script src="index.js"></script>

</body>
</html>