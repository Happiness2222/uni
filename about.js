window.addEventListener("load", () => {
    const content = document.querySelector(".about-container");

    if(content){
        content.style.opacity = "1";
        content.style.transform = "translateY(0)";
    }
});

const aboutBtn = document.getElementById("aboutBtn");
console.log(aboutBtn);

if(aboutBtn){
    aboutBtn.addEventListener("click", function () {
        // alert("Button clicked!");
        window.location.href = "index.php";
    });
}