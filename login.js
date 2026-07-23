const password = document.getElementById("password");
const togglePassword = document.getElementById("togglePassword");
togglePassword.addEventListener("click", () => {
    if (password.type === "password") {
        password.type = "text";
        togglePassword.classList.remove("fa-eye");
        togglePassword.classList.add("fa-eye-slash");
    } else {
        password.type = "password";
        togglePassword.classList.remove("fa-eye-slash");
        togglePassword.classList.add("fa-eye");
    }
});

document.getElementById("loginForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;

    let students = JSON.parse(localStorage.getItem("students")) || [];
    const student = students.find(function (s) {
        return s.email === email && s.password === password;
    });

    if (!student) {
        alert("Invalid Email or Password.");
        return;
    }

    localStorage.setItem("loggedInStudent", JSON.stringify(student));
    alert("Login Successful!");
    window.location.href = "dashboard.html";
});