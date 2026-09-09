alert("Register.js is working");

const departments = {
    "Faculty of Science": [
        "Computer Science",
        "Mathematics",
        "Statistics",
        "Physics",
        "Chemistry"
    ],

    "Faculty of Engineering": [
        "Civil Engineering",
        "Mechanical Engineering",
        "Electrical Engineering",
        "Computer Engineering"
    ],

    "Faculty of Health Sciences": [
        "Nursing",
        "Medical Laboratory Science",
        "Public Health",
        "Human Anatomy",
        "Human Physiology"
    ],

    "Faculty of Management Sciences": [
        "Accounting",
        "Banking and Finance",
        "Business Administration",
        "Economics"
    ],

    "Faculty of Law": [
        "Law"
    ],

    "Faculty of Education": [
        "Educational Management",
        "Guidance and Counselling",
        "Science Education",
        "Arts Education"
    ]
};


const faculty = document.getElementById("faculty");
const department = document.getElementById("department");

faculty.addEventListener("change", function () {

    department.innerHTML =
        '<option value="">Select Department</option>';

    const selectedFaculty = departments[this.value];

    if (selectedFaculty) {

        selectedFaculty.forEach(function (dept) {

            const option = document.createElement("option");

            option.value = dept;
            option.textContent = dept;

            department.appendChild(option);
        });
    }
});


document.getElementById("registerForm").addEventListener("submit", function (e) {

    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;

    if (password !== confirmPassword) {
        e.preventDefault();
        alert("Passwords do not match.");
        return;
    }
});