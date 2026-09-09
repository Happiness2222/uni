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

// document.getElementById("loginForm").addEventListener("submit", function (e) {
//     e.preventDefault();

//     const formData = new FormData(this);

//     fetch("login.php", {
//         method: "POST",
//         body: formData
//     })
//     .then(response => response.text())
//     .then(data => {
//         if(data.trim() === "success"){
//             alert("Login Successful!");
//             window.location.href = "dashboard.php";
//         } else {
//             alert(data)
//         }
//     })
//     .catch(error => {
//         console.error("Error:", error);
//         alert("Something went wrong. Please try again.");
//     });
// });