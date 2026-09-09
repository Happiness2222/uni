const semesterSelect = document.getElementById("semester");
const tbody = document.querySelector("#resultTable tbody");

const totalUnits = document.getElementById("totalUnits");
const passedUnits = document.getElementById("passedUnits");
const gpa = document.getElementById("gpa");
const cgpa = document.getElementById("cgpa");

const gradePoint = {
    A: 5,
    B: 4,
    C: 3,
    D: 2,
    E: 1,
    F: 0
};

let allResults = [];

fetch("get_results.php")
    .then(response => response.text())
    .then(data => {

        console.log("PHP RESPONSE:", data);

        try {
            const result = JSON.parse(data);

            if (!result.success) {
                alert(result.message);
                return;
            }

            allResults = result.results;
            loadResults();

        } catch (error) {
            console.error("JSON ERROR:", error);
            alert("PHP did not return valid JSON.");
        }

    })
    // .catch(error => {
    //     console.error("Error:", error);
    //     alert("Unable to load results.");
    // });


function loadResults() {

    tbody.innerHTML = "";

    const semester = semesterSelect.value;

    const courses = allResults.filter(course =>
        course.semester === semester
    );

    let units = 0;
    let passed = 0;
    let points = 0;

    courses.forEach(course => {

        const row = document.createElement("tr");

        const status =
            course.grade === "F"
            ? "Failed"
            : "Passed";

        row.innerHTML = `
            <td>${course.course_code}</td>
            <td>${course.course_title}</td>
            <td>${course.units}</td>
            <td>${course.grade}</td>
            <td>${status}</td>
        `;

        tbody.appendChild(row);

        units += Number(course.units);

        if (course.grade !== "F") {
            passed += Number(course.units);
        }

        points +=
            Number(course.units) *
            gradePoint[course.grade];
    });

    let calculatedGPA = 0;

    if (units > 0) {
        calculatedGPA = points / units;
    }

    totalUnits.textContent = units;
    passedUnits.textContent = passed;
    gpa.textContent = calculatedGPA.toFixed(2);

    // For now, CGPA will display the same value as the current GPA.
    cgpa.textContent = calculatedGPA.toFixed(2);
}


semesterSelect.addEventListener("change", loadResults);