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

const studentBreakdown = document.getElementById("student-breakdown-container"); 
const closeStudentBreakdown = document.getElementById("closeStudentBreakdown");

const createTask = document.getElementById("create-task-container");
const createTaskBtn = document.getElementById("create-task-btn"); 
const closeCreateTask = document.getElementById("closeCreateTaskModal");

const editTaskContainer = document.getElementById("task-edit-container");
const closeEditTask = document.getElementById("closeEditTaskModal");

const buttons = document.querySelectorAll(".view-student-btn");
const views = {
    hours: document.getElementById("hoursView"),
    attendance: document.getElementById("attendanceView"),
    tasks: document.getElementById("tasksView")
};

// layouts
const superDashboardBtn = document.getElementById("supervisor-dashboard-btn");
const superOversightBtn = document.getElementById("supervisor-oversight-btn");
const superStudentsBtn = document.getElementById("supervisor-students-btn");
const superEvaluationBtn = document.getElementById("supervisor-evaluation-btn");

const superDashboard = document.getElementById("supervisor-dashboard");
const superOversight = document.getElementById("supervisor-oversight");
const superStudents = document.getElementById("supervisor-students");
const superEvaluation = document.getElementById("supervisor-evaluation");

// timers
let searchTimer;
let assignSearchTimer;

// arrays
let selectedTaskStudentIDs = [];

// assign list
const studentList = document.getElementById("taskStudentList");


// functions

// toggles
function toggleSection(header) {
    const card = header.parentElement;

    document.querySelectorAll(".info-card").forEach(c => {
        if (c !== card) c.classList.remove("active");
    });

    card.classList.toggle("active");
}

// charts

// student view charts 
function viewStudentProgress(studentID) {
    overlay.classList.add("show");
    studentChart.classList.add("show");

    setTimeout(() => {
        loadStudentProgressChart(studentID);
        loadStudentAttendanceChart(studentID);
        loadStudentAttendanceTable(studentID);
        loadStudentTaskTable(studentID);
        }, 200);
}

function viewEvaluationBreakdown(studentID){
    overlay.classList.add("show");
    studentBreakdown.classList.add("show");
    
    setTimeout(() => {
        viewEvaluationReport(studentID);
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

function loadStudentAttendanceChart(studentID) {

    fetch("functions/getStudentAttendance.php?studentID=" + studentID)
        .then(res => res.json())
        .then(data => {

            console.log("Attendance data:", data);

            const ctx = document.getElementById('attendanceChart');

            if (!ctx) {
                console.error("Canvas not found");
                return;
            }

            if (window.attendanceChartInstance) {
                window.attendanceChartInstance.destroy();
            }

            window.attendanceChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Present', 'Late', 'Absent', 'Excused'],
                    datasets: [{
                        data: [
                            data.present ?? 0,
                            data.late ?? 0,
                            data.absent ?? 0,
                            data.excused ?? 0
                        ],
                        backgroundColor: [
                            '#16a34a',
                            '#f59e0b',
                            '#ef4444',
                            '#3b82f6'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

        })
        .catch(err => console.error("Attendance chart error:", err));
}

function loadStudentAttendanceTable(studentID) {

    fetch("functions/getStudentAttendanceTable.php?studentID=" + studentID)
        .then(res => res.text())
        .then(data => {
            document.getElementById("attendanceReportBody").innerHTML = data;
        })
        .catch(err => console.error("Table load error:", err));
}

function loadStudentTaskTable(studentID) {

    fetch("functions/getStudentTaskTable.php?studentID=" + studentID)
        .then(res => res.text())
        .then(data => {
            document.getElementById("assignedStudentTaskBody").innerHTML = data;
        })
        .catch(err => console.error("Table load error:", err));
}

function getRemarkClass(remark) {
    switch (remark) {
        case "EXCELLENT":
            return "excellent";
        case "VERY GOOD":
            return "very-good";
        case "SATISFACTORY":
            return "satisfactory";
        case "NEEDS IMPROVEMENT":
        case "FAILED":
            return "needs-improvement";
        default:
            return "";
    }
}


function viewEvaluationReport(studentID) {

    fetch("functions/getStudentEvaluationReport.php?studentID=" + studentID)
        .then(res => res.json())
        .then(data => {

            document.getElementById("reportSummary").innerHTML = `
                <div class="report-card">

                    <h2>📊 Student Evaluation Report</h2>

                    <div class="eval-grid">

                        <div class="eval-box blue">
                            <h3>Attendance</h3>
                            <p><b>${data.attendance.score}%</b></p>
                            <small>
                                Present: ${data.attendance.present} |
                                Late: ${data.attendance.late} |
                                Absent: ${data.attendance.absent} |
                                Excused: ${data.attendance.excused}
                            </small>
                        </div>

                        <div class="eval-box green">
                            <h3>Progress</h3>
                            <p><b>${data.progress.score.toFixed(2)}%</b></p>
                            <small>
                                ${data.progress.completed} / ${data.progress.required} hours
                            </small>
                        </div>

                        <div class="eval-box orange">
                            <h3>Tasks</h3>
                            <p><b>${data.tasks.score.toFixed(2)}%</b></p>
                            <small>
                                Approved: ${data.tasks.approved} |
                                Submitted: ${data.tasks.submitted} |
                                In Progress: ${data.tasks.in_progress}
                            </small>
                        </div>

                    </div>

                    <hr>

                    <div class="final-grade">
                        <h2>Final Grade: ${data.final_grade}%</h2>
                      <h3 class="remarks ${getRemarkClass(data.remarks)}">
                            ${data.remarks}
                        </h3>
                    </div>

                </div>
            `;
        });
}

// edit tasks
function editTask(taskID) {

    fetch("functions/getTaskDetails.php?taskID=" + taskID)
        .then(res => res.json())
        .then(data => {

            document.getElementById("editTaskID").value = data.taskID;
            document.getElementById("editTitle").value = data.title;
            document.getElementById("editDescription").value = data.description;
            document.getElementById("editDueDate").value = data.due_date;
            document.getElementById("editStatus").value = data.status;

            overlay.classList.add("show");
            editTaskContainer.classList.add("show");
        });
}

// delete task
function deleteTask(taskID) {

    if (!confirm("Are you sure you want to delete this task?")) {
        return;
    }

    const formData = new FormData();
    formData.append("taskID", taskID);

    fetch("functions/deleteTask.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);

        if (data.status === "success") {
            location.reload();
        }
    });
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
     superEvaluation.style.display = "none";
});

superOversightBtn.addEventListener("click", () => {
    superOversight.style.display = "block";

    superDashboard.style.display = "none";
    superStudents.style.display = "none";
    superEvaluation.style.display = "none";
});

superStudentsBtn.addEventListener("click", () => {
    superStudents.style.display = "block";

    superDashboard.style.display = "none";
    superOversight.style.display = "none";
    superEvaluation.style.display = "none";
});

superEvaluationBtn.addEventListener("click", () => {
    superEvaluation.style.display = "block";
    
    superStudents.style.display = "none";
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
    studentBreakdown.classList.remove("show");
    editTaskContainer.classList.remove("show");
});


createTaskBtn.addEventListener("click", () => {
    overlay.classList.add("show");

    createTask.classList.add("show");
});

closeStudentBreakdown.addEventListener("click", () => {
    overlay.classList.remove("show");
    studentBreakdown.classList.remove("show");
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

// edit tasks
document.getElementById("editTaskForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch("functions/updateTask.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);

        if (data.status === "success") {
            location.reload();
        }
    });
});

closeEditTask.addEventListener("click", () => {
    overlay.classList.remove("show");
    editTaskContainer.classList.remove("show");

});

// buttons student dashboard view
buttons.forEach(btn => {
    btn.addEventListener("click", () => {

        buttons.forEach(b => b.classList.remove("active"));
        btn.classList.add("active");

        Object.values(views).forEach(v => v.style.display = "none");

        const selected = btn.getAttribute("data-view");
        views[selected].style.display = "block";
    });
});

// windows (onload)
window.addEventListener("DOMContentLoaded", () => {
    loadLineChart();
    loadPieChart();
});



