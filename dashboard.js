    document.getElementById("studentName").textContent =
        student.first_name + " " + student.last_name;

    document.getElementById("regno").textContent =
        student.registration_number;
        
    document.getElementById("matric").textContent =
        student.matric_number;
        
    document.getElementById("faculty").textContent =
        student.faculty;
        
    document.getElementById("department").textContent =
        student.department;

    document.getElementById("level").textContent =
        student.level;
        
    document.getElementById("email").textContent =
        student.email;
        
    document.getElementById("phone").textContent =
        student.phone;
        
    document.getElementById("gender").textContent =
        student.gender;    
        
const logoutBtn = document.getElementById("logoutBtn");
logoutBtn.addEventListener("click", function (){
    const logout = confirm("Are you sure you want to logout?");

    if (logout){
        window.location.href = "logout.php"
    }
});