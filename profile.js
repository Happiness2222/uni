const logoutBtn = document.getElementById("logoutBtn");

logoutBtn.addEventListener("click", function () {
    const logout = confirm("Are you sure you want to logout?");

    if (logout) {
        window.location.href ="logout.php";
    }
});