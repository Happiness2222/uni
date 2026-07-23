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
    e.preventDefault();
    const firstname = document.getElementById("firstname").value.trim();
    const lastname = document.getElementById("lastname").value.trim();
    const email = document.getElementById("email").value.trim();
    const phone = document.getElementById("phone").value.trim();
    const gender = document.getElementById("gender").value;
    const facultyValue = document.getElementById("faculty").value;
    const departmentValue = document.getElementById("department").value;
    const level = document.getElementById("level").value;
    const regno = document.getElementById("regno").value.trim();
    const matric = document.getElementById("matric").value.trim();
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;

    if (password !== confirmPassword) {
        alert("Passwords do not match.");
        return;
    }

    let students = JSON.parse(localStorage.getItem("students")) || [];
    const emailExists = students.find(student => student.email === email);
    if (emailExists) {
        alert("Email already exists.");
        return;
    }

    const regExists = students.find(student => student.regno === regno);
    if (regExists) {
        alert("Registration Number already exists.");
        return;
    }

    const student = {
        fullname: firstname + " " + lastname,
        firstname,
        lastname,
        email,
        phone,
        gender,
        faculty: facultyValue,
        department: departmentValue,
        level,
        regno,
        matric,
        programme: departmentValue,
        password

    };

    students.push(student);
    localStorage.setItem("students", JSON.stringify(students));
    alert("Registration Successful!");
    window.location.href = "login.html";
});