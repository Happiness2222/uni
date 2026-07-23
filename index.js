document.querySelector(".btn").addEventListener("click", function () {
    window.location.href = "login.html";
});

document.querySelector(".secondary").addEventListener("click", function () {
    window.location.href = "register.html";
});

window.addEventListener("load", () => {
    document.querySelector(".hero-content").style.opacity = "1";
    document.querySelector(".hero-content").style.transform = "translateY(0)";
});