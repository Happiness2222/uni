alert("E-Matriculation JS Loaded");
const student = JSON.parse(localStorage.getItem("loggedInStudent"));
console.log(student);

if (!student) {
    alert("Please login first.");
    window.location.href = "login.html";
}

document.getElementById("fullname").textContent = student.fullname;
document.getElementById("regno").textContent = student.regno;
document.getElementById("matric").textContent = student.matric;
document.getElementById("faculty").textContent = student.faculty;
document.getElementById("department").textContent = student.department;
document.getElementById("level").textContent = student.level;

if (student.password) {
    document.getElementById("passport").src = student.passport;
}
function printMatriculation() {
    window.print();
}