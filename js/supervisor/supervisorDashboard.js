// menu profile
const menuToggle = document.getElementById("menuToggle");
const profileMenu = document.getElementById("profileMenu");

const menuItems = document.querySelectorAll('.menu li');

// table body
const approvalReport = document.getElementById("approvalReportBody");
const studentProgress = document.getElementById("studentProgressBody");
const assignTask = document.getElementById("assignedTaskBody");

// modals
const overlay = document.getElementById("overlay");

const studentChart = document.getElementById("student-progress-container"); 
const closeStudentChart = document.getElementById("closeStudentProgress");

const createTask = document.getElementById("create-task-container");
const createTaskBtn = document.getElementById("create-task-btn"); 
const closeCreateTask = document.getElementById("closeCreateTaskModal");

// layouts
const superDashboardBtn = document.getElementById("supervisor-dashboard-btn");
const superOversightBtn = document.getElementById("supervisor-oversight-btn");
const superStudentsBtn = document.getElementById("supervisor-students-btn");

const superDashboard = document.getElementById("supervisor-dashboard");
const superOversight = document.getElementById("supervisor-oversight");
const superStudents = document.getElementById("supervisor-students");

// timers
let searchTimer;
let assignSearchTimer;

// arrays
let selectedTaskStudentIDs = [];

// assign list
const studentList = document.getElementById("taskStudentList");


// functions

// charts

// student view charts 
function viewStudentProgress(studentID) {
    overlay.classList.add("show");
    studentChart.classList.add("show");

    setTimeout(() => {
        loadStudentProgressChart(studentID);
    }, 200);
}


function loadPieChart() {
    fetch("../../php/admin/functions/getChartData.php")
        .then(res => res.json())
        .then(data => {

            const ctx = document.getElementById('pieChart');

            //colors
            const pieColors = data.labels.map(label => {
                if (label === "ADMIN") {
                    return "#5c77f0";
                } else if (label === "student") {
                    return "#4e69e0";
                }
                return "#6c757d";
            });
            
            if (window.pieChartInstance) {
                window.pieChartInstance.destroy();
            }

            window.pieChartInstance = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.values,
                        backgroundColor: pieColors
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

        })
        .catch(err => console.error("Pie chart error:", err));
}

function loadLineChart() {
    fetch("../../php/admin/functions/getChartData.php")
        .then(res => res.json())
        .then(data => {

            const ctx = document.getElementById('lineChart');

            //colors
            const pieColors = data.labels.map(label => {
                if (label === "ADMIN") {
                    return "#5c77f0";
                } else if (label === "student") {
                    return "#4e69e0";
                }
                return "#6c757d";
            });

            if (window.lineChartInstance) {
                window.lineChartInstance.destroy();
            }

            window.lineChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Users',
                        data: data.values,
                        borderColor: '#5c77f0',
                        backgroundColor: 'rgba(92, 119, 240, 0.2)',
                        fill: true,
                        tension: 0.3,
                        pointHoverRadius: 6,
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    tooltip: {
                        enabled: true
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

        })
        .catch(err => console.error("Line chart error:", err));
}

function loadStudentProgressChart(studentID) {

    fetch("functions/getStudentProgress.php?studentID=" + studentID)
        .then(res => res.json())
        .then(data => {

            console.log("Progress data:", data);

            const ctx = document.getElementById('progressChart');

            if (!ctx) {
                console.error("Canvas not found");
                return;
            }

            if (window.progressChartInstance) {
                window.progressChartInstance.destroy();
            }

            window.progressChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Completed Hours', 'Remaining Hours'],
                    datasets: [{
                        label: 'OJT Progress',
                        data: [data.completed ?? 0, data.remaining ?? 0],
                        backgroundColor: ['#059669', '#2563EB']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

        })
        .catch(err => console.error("Progress chart error:", err));
}


// profile menu
menuToggle.addEventListener("click", (e) => {
    e.stopPropagation();
    profileMenu.hidden = !profileMenu.hidden;
});

document.addEventListener("click", (e) => {
    if (!menuToggle.contains(e.target) && !profileMenu.contains(e.target)) {
        profileMenu.hidden = true;
    }
});

menuItems.forEach(item => {
    item.addEventListener('click', () => {

        menuItems.forEach(li => li.classList.remove('active'));

        item.classList.add('active');

    });
});

// layouts

superDashboardBtn.addEventListener("click", () => {
    superDashboard.style.display = "block";

    superOversight.style.display = "none";
     superStudents.style.display = "none";
});

superOversightBtn.addEventListener("click", () => {
    superOversight.style.display = "block";

    superDashboard.style.display = "none";
    superStudents.style.display = "none";
});

superStudentsBtn.addEventListener("click", () => {
    superStudents.style.display = "block";

    superDashboard.style.display = "none";
    superOversight.style.display = "none";
});


// search tables
document.getElementById("reportApprovalSearch").addEventListener("keyup", function () {
    clearTimeout(searchTimer);

    let value = this.value;

    searchTimer = setTimeout(() => {

        approvalReport.classList.add("fade-out");

        fetch("functions/searchApprovalReport.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "search=" + encodeURIComponent(value)
        })
        .then(res => res.text())
        .then(data => {
            setTimeout(() => {
                approvalReport.innerHTML = data;

                approvalReport.classList.remove("fade-out");
                approvalReport.classList.add("fade-in");

                setTimeout(() => {
                    approvalReport.classList.remove("fade-in");
                }, 200);

            }, 200);
        });

    }, 300);
});

document.getElementById("studentProcessSearch").addEventListener("keyup", function () {
    clearTimeout(searchTimer);

    let value = this.value;

    searchTimer = setTimeout(() => {

        studentProgress.classList.add("fade-out");

        fetch("functions/searchStudentProgress.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "search=" + encodeURIComponent(value)
        })
        .then(res => res.text())
        .then(data => {
            setTimeout(() => {
                studentProgress.innerHTML = data;

                studentProgress.classList.remove("fade-out");
                studentProgress.classList.add("fade-in");

                setTimeout(() => {
                    studentProgress.classList.remove("fade-in");
                }, 200);

            }, 200);
        });

    }, 300);
});

document.getElementById("assignedTaskSearch").addEventListener("keyup", function () {
    clearTimeout(searchTimer);

    let value = this.value;

    searchTimer = setTimeout(() => {

        assignTask.classList.add("fade-out");

        fetch("functions/searchTask.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "search=" + encodeURIComponent(value)
        })
        .then(res => res.text())
        .then(data => {
            setTimeout(() => {
                assignTask.innerHTML = data;

                assignTask.classList.remove("fade-out");
                assignTask.classList.add("fade-in");

                setTimeout(() => {
                    assignTask.classList.remove("fade-in");
                }, 200);

            }, 200);
        });

    }, 300);
});





// modals
overlay.addEventListener("click", () => {
    overlay.classList.remove('show');

    createTask.classList.remove("show");
    studentChart.classList.remove("show");
});


createTaskBtn.addEventListener("click", () => {
    overlay.classList.add("show");

    createTask.classList.add("show");
});

closeCreateTask.addEventListener("click", () => {
    overlay.classList.remove("show");

    createTask.classList.remove("show");
});

closeStudentChart.addEventListener("click", () => {
    overlay.classList.remove("show");
    studentChart.classList.remove("show");

    if (window.progressChartInstance) {
        window.progressChartInstance.destroy();
        window.progressChartInstance = null;
    }
});



// assigning task to students
document.getElementById("taskStudentList").addEventListener("click", function (e) {

    const item = e.target.closest(".task-student-item");

    if (!item) return;

    item.classList.toggle("selected-student");

    let id = item.dataset.id;

    if (selectedTaskStudentIDs.includes(id)) {
        selectedTaskStudentIDs = selectedTaskStudentIDs.filter(i => i !== id);
    } else {
        selectedTaskStudentIDs.push(id);
    }
});

document.getElementById("taskStudentSearch").addEventListener("keyup", function () {
    clearTimeout(assignSearchTimer);

    let value = this.value;

    assignSearchTimer = setTimeout(() => {

        studentList.classList.add("fade-out");

        fetch("functions/searchAssignTaskStudent.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "search=" + encodeURIComponent(value)
        })
        .then(res => res.text())
        .then(data => {

            setTimeout(() => {
                studentList.innerHTML = data;

                studentList.classList.remove("fade-out");
                studentList.classList.add("fade-in");

                setTimeout(() => {
                    studentList.classList.remove("fade-in");
                }, 200);

            }, 200);

        });

    }, 300);
});

document.getElementById("createTaskForm").addEventListener("submit", function (e) {
    e.preventDefault();

    if (selectedTaskStudentIDs.length === 0) {
        alert("Please select at least one student.");
        return;
    }

    const formData = new FormData(this);

    const selectedSupervisorID = document.getElementById("superID").value;

    formData.append("superID", selectedSupervisorID);

    selectedTaskStudentIDs.forEach(id => {
        formData.append("studentIDs[]", id);
    });

    fetch("functions/createAndAssignTask.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);

        if (data.status === "success") {
            location.reload();
        }
    })
    .catch(err => {
        alert("Something went wrong.");
        console.log(err);
    });
    
});

// toggles
function toggleSection(header) {
    const card = header.parentElement;

    document.querySelectorAll(".info-card").forEach(c => {
        if (c !== card) c.classList.remove("active");
    });

    card.classList.toggle("active");
}

// windows (onload)
window.addEventListener("DOMContentLoaded", () => {
    loadLineChart();
    loadPieChart();
});



