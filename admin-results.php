<?php
session_start();
include "db.php";

$sql = "SELECT 
            id,
            first_name,
            last_name,
            matric_number,
            department,
            level
        FROM students
        ORDER BY first_name, last_name";

$result = $conn->query($sql);

if (!$result) {
    die("Failed to load students: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Results | Evergreen State University</title>

    <link rel="stylesheet" href="admin-results.css">

</head>

<body>

<div class="container">

    <div class="result-box">

        <h1>Evergreen State University</h1>

        <h2>Enter Student Results</h2>

        <!-- SELECT STUDENT -->

        <div class="student-selection">

            <label for="student">Select Student</label>

            <select id="student">

                <option value="">Select Student</option>

                <?php while ($student = $result->fetch_assoc()): ?>

                    <option value="<?php echo $student['id']; ?>">

                        <?php
                        echo htmlspecialchars(
                            $student['first_name'] . " " .
                            $student['last_name'] . " - " .
                            $student['matric_number'] . " - " .
                            $student['department']
                        );
                        ?>

                    </option>

                <?php endwhile; ?>

            </select>

        </div>


        <!-- REGISTERED COURSES -->

        <div id="courses"></div>

    </div>

</div>


<script>

const studentSelect = document.getElementById("student");

const coursesDiv = document.getElementById("courses");


studentSelect.addEventListener("change", function () {

    const studentId = this.value;

    coursesDiv.innerHTML = "";


    if (!studentId) {
        return;
    }


    const formData = new FormData();

    formData.append("student_id", studentId);


    fetch("get_student_courses.php", {

        method: "POST",

        body: formData

    })

    .then(response => response.json())

    .then(data => {

        console.log("COURSES RESPONSE:", data);


        if (!data.success) {

            coursesDiv.innerHTML =
                `<p>${data.message}</p>`;

            return;

        }


        if (data.courses.length === 0) {

            coursesDiv.innerHTML =
                "<p>This student has not registered any courses.</p>";

            return;

        }


        let html = "<h3>Registered Courses</h3>";


        html += `

            <table border="1" cellpadding="10">

                <tr>

                    <th>Course Code</th>

                    <th>Course Title</th>

                    <th>Unit</th>

                    <th>Semester</th>

                    <th>Grade</th>

                </tr>

        `;


        data.courses.forEach((course, index) => {

            html += `

                <tr>

                    <td>${course.course_code}</td>

                    <td>${course.course_title}</td>

                    <td>${course.unit}</td>

                    <td>${course.semester}</td>

                    <td>

                        <select class="grade-select"
                                data-course="${course.course_code}"
                                data-semester="${course.semester}"
                                data-title="${course.course_title}"
                                data-unit="${course.unit}">

                            <option value="">Select Grade</option>

                            <option value="A">A</option>

                            <option value="B">B</option>

                            <option value="C">C</option>

                            <option value="D">D</option>

                            <option value="E">E</option>

                            <option value="F">F</option>

                        </select>

                    </td>

                </tr>

            `;

        });


        html += "</table>";


        coursesDiv.innerHTML = html;

    })


    .catch(error => {

        console.error("ERROR:", error);

        coursesDiv.innerHTML =
            "<p>Something went wrong while loading the courses.</p>";

    });

});

</script>

</body>

</html>