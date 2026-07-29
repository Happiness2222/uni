alert("Course Registration JS Loaded");
const student = JSON.parse(localStorage.getItem("loggedInStudent"));

if (!student) {
    alert("Please login first.");
    window.location.href = "login.html";
}

document.getElementById("fullname").textContent = student.fullname;
document.getElementById("regno").textContent = student.regno;
document.getElementById("matric").textContent = student.matric;
document.getElementById("faculty").textContent = student.faculty;
document.getElementById("department").textContent = student.department;
document.getElementById("level").textContent = student.level;

const courseDatabase = {
    "100 Level":{
        firstSemester:[
            {code:"CSC101",title:"Introduction to Computer Science",unit:3,status:"Core"},
            {code:"CSC109",title:"Computer Programming I",unit:3,status:"Core"},
            {code:"CSC112",title:"Digital Logic Design",unit:2,status:"Core"},
            {code:"MTH102",title:"Algebra & Trigonometry",unit:3,status:"Core"},
            {code:"PHY101",title:"General Physics I",unit:3,status:"Core"},
            {code:"GST111",title:"Communication in English",unit:2,status:"Core"},
            {code:"CSC113",title:"Computer Appreciation",unit:2,status:"Elective"},
            {code:"CSC115",title:"Problem Solving Techniques",unit:2,status:"Elective"},
            {code:"MTH104",title:"Introduction to Mathematical Logic",unit:2,status:"Elective"}
        ],

        secondSemester:[
            {code:"CSC102",title:"Introduction to Information Technology",unit:3,status:"Core"},
            {code:"CSC110",title:"Computer Programming II",unit:3,status:"Core"},
            {code:"MTH103",title:"Calculus I",unit:3,status:"Core"},
            {code:"PHY102",title:"General Physics Practical",unit:1,status:"Core"},
            {code:"CHEM101",title:"General Chemistry",unit:3,status:"Core"},
            {code:"GST112",title:"Nigerian People & Culture",unit:2,status:"Core"},
            {code:"STA101",title:"Introduction to Statistics",unit:2,status:"Elective"},
            {code:"GST115",title:"ICT Fundamentals",unit:2,status:"Elective"},
            {code:"BIO101",title:"General Biology",unit:2,status:"Elective"}
        ]
    },

    "200 Level":{
        firstSemester:[
            {code:"CSC201",title:"Data Structures",unit:3,status:"Core"},
            {code:"CSC203",title:"Object Oriented Programming",unit:3,status:"Core"},
            {code:"CSC205",title:"Computer Architecture",unit:3,status:"Core"},
            {code:"MTH201",title:"Linear Algebra",unit:3,status:"Core"},
            {code:"STA201",title:"Probability Theory",unit:2,status:"Core"},
            {code:"GST211",title:"Entrepreneurship I",unit:2,status:"Core"},
            {code:"CSC217",title:"Computer Graphics",unit:2,status:"Elective"},
            {code:"CSC219",title:"Digital Electronics",unit:2,status:"Elective"},
            {code:"CSC221",title:"Information Systems",unit:2,status:"Elective"}
        ],

        secondSemester:[
            {code:"CSC202",title:"Algorithms",unit:3,status:"Core"},
            {code:"CSC204",title:"Operating Systems I",unit:3,status:"Core"},
            {code:"CSC206",title:"Database Systems",unit:3,status:"Core"},
            {code:"MTH203",title:"Numerical Methods",unit:2,status:"Core"},
            {code:"GST213",title:"Logic and Philosophy",unit:2,status:"Core"},
            {code:"CSC218",title:"Assembly Language",unit:2,status:"Elective"},
            {code:"CSC220",title:"Microprocessor Systems",unit:2,status:"Elective"},
            {code:"CSC222",title:"Internet Technology",unit:2,status:"Elective"}
        ]
    },

    "300 Level":{

        firstSemester:[
            {code:"CSC301",title:"Software Engineering",unit:3,status:"Core"},
            {code:"CSC303",title:"Computer Networks",unit:3,status:"Core"},
            {code:"CSC305",title:"Web Application Development",unit:3,status:"Core"},
            {code:"CSC307",title:"Artificial Intelligence",unit:3,status:"Core"},
            {code:"CSC309",title:"Compiler Construction",unit:3,status:"Core"},
            {code:"GST311",title:"Entrepreneurship II",unit:2,status:"Core"},
            {code:"CSC317",title:"Computer Graphics",unit:2,status:"Elective"},
            {code:"CSC319",title:"Mobile Computing",unit:2,status:"Elective"},
            {code:"CSC321",title:"Data Mining",unit:2,status:"Elective"}

        ],

        secondSemester:[

            {code:"CSC302",title:"Operating Systems II",unit:3,status:"Core"},
            {code:"CSC304",title:"Data Communication",unit:3,status:"Core"},
            {code:"CSC306",title:"Database Management Systems",unit:3,status:"Core"},
            {code:"CSC308",title:"Human Computer Interaction",unit:2,status:"Core"},
            {code:"CSC310",title:"Cyber Security",unit:3,status:"Core"},
            {code:"CSC318",title:"Cloud Fundamentals",unit:2,status:"Elective"},
            {code:"CSC320",title:"Machine Vision",unit:2,status:"Elective"},
            {code:"CSC322",title:"Web Development",unit:2,status:"Elective"}
        ]
    },

    "400 Level":{
        firstSemester:[
            {code:"CSC401",title:"Machine Learning",unit:3,status:"Core"},
            {code:"CSC403",title:"Mobile Application Development",unit:3,status:"Core"},
            {code:"CSC405",title:"Cloud Computing",unit:3,status:"Core"},
            {code:"CSC407",title:"Distributed Systems",unit:3,status:"Core"},
            {code:"CSC411",title:"Project I",unit:4,status:"Core"},
            {code:"CSC417",title:"Blockchain Technology",unit:2,status:"Elective"},
            {code:"CSC419",title:"Robotics",unit:2,status:"Elective"},
            {code:"CSC421",title:"Advanced Networking",unit:2,status:"Elective"}
        ],

        secondSemester:[
            {code:"CSC402",title:"Information Security",unit:3,status:"Core"},
            {code:"CSC404",title:"Data Science",unit:3,status:"Core"},
            {code:"CSC406",title:"System Programming",unit:3,status:"Core"},
            {code:"CSC408",title:"Professional Ethics",unit:2,status:"Core"},
            {code:"CSC413",title:"Project II",unit:4,status:"Core"},
            {code:"CSC418",title:"Internet of Things",unit:2,status:"Elective"},
            {code:"CSC420",title:"Big Data Analytics",unit:2,status:"Elective"},
            {code:"CSC422",title:"Virtual Reality",unit:2,status:"Elective"}
        ]
    }
};

const firstTable = document.querySelector("#firstSemesterTable tbody");
const secondTable = document.querySelector("#secondSemesterTable tbody");

const firstUnits = document.getElementById("firstUnits");
const secondUnits = document.getElementById("secondUnits");
const grandUnits = document.getElementById("grandUnits");

function loadCourses() {
    firstTable.innerHTML = "";
    secondTable.innerHTML = "";
    const studentCourses = courseDatabase[student.level];

    // ---------- FIRST SEMESTER ----------
    studentCourses.firstSemester.forEach(course => {
        let row = document.createElement("tr");
        row.innerHTML = `
        <td>
        <input
        type="checkbox"
        value="${course.code}"
        data-unit="${course.unit}"
        data-status="${course.status}"
        ${course.status==="Core" ? "checked disabled" : ""}
        >
        </td>

        <td>${course.code}</td>
        <td>${course.title}</td>
        <td>${course.unit}</td>
        <td>${course.status}</td>
        `;
        firstTable.appendChild(row);

    });

    // ---------- SECOND SEMESTER ----------
    studentCourses.secondSemester.forEach(course => {
        let row = document.createElement("tr");
        row.innerHTML = `
        <td>
        <input
        type="checkbox"
        value="${course.code}"
        data-unit="${course.unit}"
        data-status="${course.status}"
        ${course.status==="Core" ? "checked disabled" : ""}
        >
        </td>

        <td>${course.code}</td>
        <td>${course.title}</td>
        <td>${course.unit}</td>
        <td>${course.status}</td>
        `;
        secondTable.appendChild(row);
    });
    calculateUnits();
}

loadCourses();

// ======================
// UNIT CALCULATOR
// ======================
function calculateUnits(){
let first = 0;
let second = 0;
document.querySelectorAll("#firstSemesterTable input").forEach(box=>{
if(box.checked){
first += Number(box.dataset.unit);
}
});

document.querySelectorAll("#secondSemesterTable input").forEach(box=>{
if(box.checked){
second += Number(box.dataset.unit);
}
});

firstUnits.textContent = first;
secondUnits.textContent = second;
grandUnits.textContent = first + second;
}

document.addEventListener("change",function(e){
if(e.target.type==="checkbox"){
calculateUnits();
}
});

const savedCourses = student.registeredCourses || [];

document.querySelectorAll("input[type='checkbox']").forEach(box => {

    if(savedCourses.includes(box.value)){
        box.checked = true;
    }

});

calculateUnits();

if(savedCourses.length > 0){
    const status = document.getElementById("registrationStatus");

    if (status){
        status.textContent = "COMPLETED";
    }
}

document.getElementById("courseForm").addEventListener("submit", function(e){

    e.preventDefault();

    const selectedCourses = [];

    document.querySelectorAll("input[type='checkbox']").forEach(box=>{

        if(box.checked){
            selectedCourses.push(box.value);
        }

    });

    student.registeredCourses = selectedCourses;

    localStorage.setItem("loggedInStudent", JSON.stringify(student));

    let students = JSON.parse(localStorage.getItem("students")) || [];

    const index = students.findIndex(s => s.email === student.email);

    if(index !== -1){
        students[index] = student;
        localStorage.setItem("students", JSON.stringify(students));
    }

    alert("Course Registration Successful!");
    window.location.href = "dashboard.html";

});

document.getElementById("printBtn").addEventListener("click", function(){
    window.print();
})

document.getElementById("dashboardBtn").addEventListener("click", function(){
    window.location.href = "dashboard.html";
})
