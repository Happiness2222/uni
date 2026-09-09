document.addEventListener("DOMContentLoaded", function () {

    const firstTable = document.querySelector("#firstSemesterTable tbody");
    const secondTable = document.querySelector("#secondSemesterTable tbody");

    const firstUnits = document.getElementById("firstUnits");
    const secondUnits = document.getElementById("secondUnits");
    const grandUnits = document.getElementById("grandUnits");

    fetch("get_courses.php")
        .then(response => response.json())
        .then(data => {

            if (!data.success) {
                alert(data.message || "Unable to load courses.");
                return;
            }

            firstTable.innerHTML = "";
            secondTable.innerHTML = "";

            data.courses
                .filter(course => course.semester === "First")
                .forEach(course => {

                    const row = document.createElement("tr");

                    row.innerHTML = `
                        <td>
                            <input
                                type="checkbox"
                                value="${course.course_code}"
                                data-unit="${course.unit}"
                                ${course.status === "Core" ? "checked disabled" : ""}
                            >
                        </td>

                        <td>${course.course_code}</td>
                        <td>${course.course_title}</td>
                        <td>${course.unit}</td>
                        <td>${course.status}</td>
                    `;

                    firstTable.appendChild(row);
                });

            data.courses
                .filter(course => course.semester === "Second")
                .forEach(course => {

                    const row = document.createElement("tr");

                    row.innerHTML = `
                        <td>
                            <input
                                type="checkbox"
                                value="${course.course_code}"
                                data-unit="${course.unit}"
                                ${course.status === "Core" ? "checked disabled" : ""}
                            >
                        </td>

                        <td>${course.course_code}</td>
                        <td>${course.course_title}</td>
                        <td>${course.unit}</td>
                        <td>${course.status}</td>
                    `;

                    secondTable.appendChild(row);
                });

            calculateUnits();
        })
        .catch(error => {
            console.error("Error loading courses:", error);
            alert("Could not load courses.");
        });

    function calculateUnits() {

        let first = 0;
        let second = 0;

        document.querySelectorAll(
            "#firstSemesterTable input[type='checkbox']"
        ).forEach(box => {

            if (box.checked) {
                first += Number(box.dataset.unit);
            }

        });


        document.querySelectorAll(
            "#secondSemesterTable input[type='checkbox']"
        ).forEach(box => {

            if (box.checked) {
                second += Number(box.dataset.unit);
            }

        });


        firstUnits.textContent = first;
        secondUnits.textContent = second;
        grandUnits.textContent = first + second;
    }


    // Recalculate when an elective is selected
    document.addEventListener("change", function (e) {

        if (e.target.type === "checkbox") {
            calculateUnits();
        }
    });

    document.getElementById("courseForm").addEventListener("submit", function (e) {

        e.preventDefault();

        const firstSemester = [];
        const secondSemester = [];


        document.querySelectorAll(
            "#firstSemesterTable input[type='checkbox']"
        ).forEach(box => {

            if (box.checked) {
                firstSemester.push(box.value);
            }

        });


        document.querySelectorAll(
            "#secondSemesterTable input[type='checkbox']"
        ).forEach(box => {

            if (box.checked) {
                secondSemester.push(box.value);
            }

        });


        const allCourses = [
            ...firstSemester,
            ...secondSemester
        ];

        console.log("FIRST SEMESTER:", firstSemester);
        console.log("SECOND SEMESTER:", secondSemester);
        console.log("ALL COURSES", allCourses);


        if (allCourses.length === 0) {
            alert("Please select at least one course.");
            return;
        }


        fetch("register_courses.php", {

            method: "POST",

            headers: {
                "Content-Type": "application/json"
            },

            body: JSON.stringify({
                courses: allCourses,
                firstSemester: firstSemester,
                secondSemester: secondSemester
            })

        })

        .then(response => response.text())

        .then(data => {

            if (data.trim() === "success") {

                document.getElementById(
                    "registrationStatus"
                ).textContent = "COMPLETED";

                alert("Course Registration Successful!");

            } else {

                alert(data);

            }

        })

        .catch(error => {

            console.error("Registration error:", error);

            alert(
                "Something went wrong. Please try again."
            );
        });
    });

    document.getElementById("printBtn").addEventListener(
        "click",
        function () {
            window.print();
        }
    );

    document.getElementById("dashboardBtn").addEventListener(
        "click",
        function () {
            window.location.href = "dashboard.php";
        }
    );

});