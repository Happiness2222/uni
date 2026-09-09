const portalBtn = document.getElementById("portalBtn");

if(portalBtn) {
    portalBtn.addEventListener("click", function () {
        window.location.href = "login.html";
    });
}