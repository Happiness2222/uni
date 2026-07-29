console.log("Password JS loaded");
const student = JSON.parse(localStorage.getItem("loggedInStudent"));

if(!student){

alert("Please login first.");
window.location.href="login.html";

}

document.getElementById("passwordForm").addEventListener("submit",function(e){

e.preventDefault();

const current=document.getElementById("currentPassword").value;

const newPass=document.getElementById("newPassword").value;

const confirm=document.getElementById("confirmPassword").value;


if(current!== student.password){

alert("Current password is incorrect.");

return;

}

// Check new passwords

if(newPass!==confirm){

alert("New passwords do not match.");

return;

}

// Update password

student.password=newPass;

localStorage.setItem("loggedInStudent",JSON.stringify(student));

// Update students

let students=JSON.parse(localStorage.getItem("students"))||[];

const index=students.findIndex(s=>s.email===student.email);

if(index!==-1){
students[index]=student;
localStorage.setItem("students",JSON.stringify(students));
}

alert("Password changed successfully!");
window.location.href="dashboard.html";
});