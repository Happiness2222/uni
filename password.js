document.getElementById("passwordForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const current = document.getElementById("currentPassword").value;
    const newPass = document.getElementById("newPassword").value;
    const confirm = document.getElementById("confirmPassword").value;

    if (newPass !== confirm) {
        alert("New passwords do not match.");
        return;
    }

    fetch("change-password.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body:
        "currentPassword=" + encodeURIComponent(current) +
        "&newPassword=" + encodeURIComponent(newPass)
    })
    .then(response => response.text())
    .then(data => {

        if (data === "success") {
            alert("Password changed successfully!");
            window.location.href = "dashboard.php";
        } else {
            alert(data);
        }
    })
    .catch(error => {
        console.error(error);
        alert("Something went wrong. Please try again.");
    });
});