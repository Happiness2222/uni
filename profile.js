alert("Profile JS Loaded");

const student = JSON.parse(localStorage.getItem("loggedInStudent"));

console.log(student);

if (!student) {
    alert("Please login first.");
    window.location.href = "login.html";
}

document.getElementById("firstname").value = student.firstname;
document.getElementById("lastname").value = student.lastname;
document.getElementById("email").value = student.email;
document.getElementById("phone").value = student.phone;
document.getElementById("gender").value = student.gender;
document.getElementById("faculty").value = student.faculty;
document.getElementById("department").value = student.department;
document.getElementById("level").value = student.level;
document.getElementById("regno").value = student.regno;
document.getElementById("matric").value = student.matric;

document.getElementById("profileForm").addEventListener("submit", function (e) {

    e.preventDefault();

    student.firstname = document.getElementById("firstname").value.trim();
    student.lastname = document.getElementById("lastname").value.trim();
    student.fullname = student.firstname + " " + student.lastname;
    student.phone = document.getElementById("phone").value.trim();
    student.gender = document.getElementById("gender").value;
    student.faculty = document.getElementById("faculty").value.trim();
    student.department = document.getElementById("department").value.trim();
    student.level = document.getElementById("level").value;

    localStorage.setItem("loggedInStudent", JSON.stringify(student));

    // Update student in students array
    let students = JSON.parse(localStorage.getItem("students")) || [];

    const index = students.findIndex(function (s) {
        return s.email === student.email;
    });

    if (index !== -1) {
        students[index] = student;
        localStorage.setItem("students", JSON.stringify(students));
    }

    alert("Profile Updated Successfully!");

    window.location.href = "dashboard.html";

});

document.getElementById("logoutBtn").addEventListener("click", function () {

    const logout = confirm("Are you sure you want to logout?");

    if (logout) {
        localStorage.removeItem("loggedInStudent");
        window.location.href = "login.html";
    }

});