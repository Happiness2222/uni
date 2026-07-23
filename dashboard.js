const student = JSON.parse(localStorage.getItem("loggedInStudent"));

if (!student) {
    alert("Please login first.");
    window.location.href = "login.html";
}

document.getElementById("studentName").textContent = student.fullname;
document.getElementById("regno").textContent = student.regno;
document.getElementById("matric").textContent = student.matric;
document.getElementById("faculty").textContent = student.faculty;
document.getElementById("department").textContent = student.department;
document.getElementById("level").textContent = student.level;
document.getElementById("email").textContent = student.email;
document.getElementById("phone").textContent = student.phone;
document.getElementById("gender").textContent = student.gender;

const logoutBtn = document.getElementById("logoutBtn");

logoutBtn.addEventListener("click", function () {

    const logout = confirm("Are you sure you want to logout?");
    if (logout) {
        localStorage.removeItem("loggedInStudent");
        window.location.href = "login.html";
    }
});