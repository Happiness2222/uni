const student = JSON.parse(localStorage.getItem("loggedInStudent"));
console.log(student);

if (!student) {
    alert("Please login first.");
    window.location.href = "login.html";
}

const resultDatabase = {

"100 Level":{

firstSemester:[
{code:"CSC101",title:"Introduction to Computer Science",unit:3,grade:"A"},
{code:"CSC109",title:"Computer Programming I",unit:3,grade:"B"},
{code:"CSC112",title:"Digital Logic Design",unit:2,grade:"A"},
{code:"MTH102",title:"Algebra & Trigonometry",unit:3,grade:"B"},
{code:"PHY101",title:"General Physics I",unit:3,grade:"C"},
{code:"GST111",title:"Communication in English",unit:2,grade:"A"}
],

secondSemester:[
{code:"CSC102",title:"Introduction to Information Technology",unit:3,grade:"A"},
{code:"CSC110",title:"Computer Programming II",unit:3,grade:"B"},
{code:"MTH103",title:"Calculus I",unit:3,grade:"B"},
{code:"PHY102",title:"General Physics Practical",unit:1,grade:"A"},
{code:"CHEM101",title:"General Chemistry",unit:3,grade:"C"},
{code:"GST112",title:"Nigerian People & Culture",unit:2,grade:"A"}
]

},

"200 Level":{

firstSemester:[
{code:"CSC201",title:"Data Structures",unit:3,grade:"A"},
{code:"CSC203",title:"Object Oriented Programming",unit:3,grade:"B"},
{code:"CSC205",title:"Computer Architecture",unit:3,grade:"A"},
{code:"MTH201",title:"Linear Algebra",unit:3,grade:"B"},
{code:"STA201",title:"Probability Theory",unit:2,grade:"C"},
{code:"GST211",title:"Entrepreneurship I",unit:2,grade:"A"}
],

secondSemester:[
{code:"CSC202",title:"Algorithms",unit:3,grade:"A"},
{code:"CSC204",title:"Operating Systems I",unit:3,grade:"B"},
{code:"CSC206",title:"Database Systems",unit:3,grade:"A"},
{code:"MTH203",title:"Numerical Methods",unit:2,grade:"B"},
{code:"GST213",title:"Logic and Philosophy",unit:2,grade:"C"}
]

},

"300 Level":{

firstSemester:[
{code:"CSC301",title:"Software Engineering",unit:3,grade:"A"},
{code:"CSC303",title:"Computer Networks",unit:3,grade:"C"},
{code:"CSC305",title:"Web Application Development",unit:3,grade:"A"},
{code:"CSC307",title:"Artificial Intelligence",unit:3,grade:"B"},
{code:"CSC309",title:"Compiler Construction",unit:3,grade:"E"},
{code:"GST311",title:"Entrepreneurship II",unit:2,grade:"C"}
],

secondSemester:[
{code:"CSC302",title:"Operating Systems II",unit:3,grade:"A"},
{code:"CSC304",title:"Data Communication",unit:3,grade:"B"},
{code:"CSC306",title:"Database Management Systems",unit:3,grade:"A"},
{code:"CSC308",title:"Human Computer Interaction",unit:2,grade:"B"},
{code:"CSC310",title:"Cyber Security",unit:3,grade:"A"}
]

},

"400 Level":{

firstSemester:[
{code:"CSC401",title:"Machine Learning",unit:3,grade:"A"},
{code:"CSC403",title:"Mobile Application Development",unit:3,grade:"A"},
{code:"CSC405",title:"Cloud Computing",unit:3,grade:"B"},
{code:"CSC407",title:"Distributed Systems",unit:3,grade:"A"},
{code:"CSC411",title:"Project I",unit:4,grade:"A"}
],

secondSemester:[
{code:"CSC402",title:"Information Security",unit:3,grade:"A"},
{code:"CSC404",title:"Data Science",unit:3,grade:"B"},
{code:"CSC406",title:"System Programming",unit:3,grade:"C"},
{code:"CSC408",title:"Professional Ethics",unit:2,grade:"A"},
{code:"CSC413",title:"Project II",unit:4,grade:"A"}
]
}
};

const gradePoint = {
A:5,
B:4,
C:3,
D:2,
E:1,
F:0
};

const semesterSelect = document.getElementById("semester");
console.log(student.level);
console.log(semester);
console.log(resultDatabase[student.level]);
const tbody = document.querySelector("#resultTable tbody");

function loadResults(){

tbody.innerHTML="";

let semester = semesterSelect.value;
let courses = resultDatabase[student.level][semester];

let totalUnits=0;
let passedUnits=0;
let totalPoints=0;

courses.forEach(course=>{

let row=document.createElement("tr");

let status = course.grade==="F" ? "Failed" : "Passed";

row.innerHTML=`
<td>${course.code}</td>
<td>${course.title}</td>
<td>${course.unit}</td>
<td>${course.grade}</td>
<td>${status}</td>
`;

tbody.appendChild(row);

totalUnits += course.unit;

if(course.grade!=="F"){
passedUnits += course.unit;
}

totalPoints += course.unit * gradePoint[course.grade];

});

let gpa=(totalPoints/totalUnits).toFixed(2);

document.getElementById("totalUnits").textContent=totalUnits;
document.getElementById("passedUnits").textContent=passedUnits;
document.getElementById("gpa").textContent=gpa;
document.getElementById("cgpa").textContent=gpa;

}

loadResults();

semesterSelect.addEventListener("change",loadResults);