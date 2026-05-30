// menu profile
const menuToggle = document.getElementById("menuToggle");
const profileMenu = document.getElementById("profileMenu");

const menuItems = document.querySelectorAll('.menu li');

// table body
const approvalReport = document.getElementById("approvalReportBody");
const studentProgress = document.getElementById("studentProgressBody");
const assignTask = document.getElementById("assignedTaskBody");
const activityLogBody = document.getElementById("activityLogTableBody");

// modals
const overlay = document.getElementById("overlay");

const studentChart = document.getElementById("student-progress-container"); 
const closeStudentChart = document.getElementById("closeStudentProgress");

const studentBreakdown = document.getElementById("student-breakdown-container"); 
const closeStudentBreakdown = document.getElementById("closeStudentBreakdown");

const studentFinalEvaluation = document.getElementById("student-final-evaluation"); 
const closeSFinalEvaluation = document.getElementById("closeFinalEvaluation");

const studentFinalEvaluationView = document.getElementById("final-evaluation-view"); 
const closeSFinalEvaluationView = document.getElementById("closeFinalEvaluationView");

const createTask = document.getElementById("create-task-container");
const createTaskBtn = document.getElementById("create-task-btn"); 
const closeCreateTask = document.getElementById("closeCreateTaskModal");

const editTaskContainer = document.getElementById("task-edit-container");
const closeEditTask = document.getElementById("closeEditTaskModal");

const viewTaskDetails = document.getElementById("task-view");
const closeViewTaskDetails = document.getElementById("closeTaskViewModal");

const studentApplicationApprove = document.getElementById("student-application-approve");

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
const superActivityBtn = document.getElementById("supervisor-activity-btn");
const superAttendanceBtn = document.getElementById("supervisor-attendance-btn");

const superDashboard = document.getElementById("supervisor-dashboard");
const superOversight = document.getElementById("supervisor-oversight");
const superStudents = document.getElementById("supervisor-students");
const superEvaluation = document.getElementById("supervisor-evaluation");
const superActivity = document.getElementById("supervisor-activity");
const superAttendance = document.getElementById("supervisor-attendance");

// timers
let searchTimer;
let activitySearchTimer;
let assignSearchTimer;

// arrays
let selectedTaskStudentIDs = [];

// assign list
const studentList = document.getElementById("taskStudentList");

// settings

const accountModal = document.getElementById("account-settings-supervisor");
const changePasswordModal = document.getElementById("change-password-modal-supervisor");

const openAccountBtn = document.getElementById("openAccountSettingsBtn");
const openChangePasswordBtn = document.getElementById("openChangePasswordSupervisor");

const closeAccountBtn = document.getElementById("closeAccountModalSupervisor");
const backBtn = document.getElementById("backToAccountSettingsSupervisor");

// toggle
const toggleBtn = document.getElementById("darkModeToggle");

// current month
window.currentMonth = null;

// task swtich
const taskManage = document.getElementById("task-manage-table");
const taskSubmit = document.getElementById("task-submit-table");

// functions

// open rfid
function openRfid() {
     window.open("../../php/rfid_test.php", "_blank");
}

// force
document.addEventListener("DOMContentLoaded", function () {

    const force = window.forceChangePassword;

    if (force == 1) {

        const overlayWow = document.getElementById("forcePasswordOverlay");

        overlayWow.style.display = "flex";

        document.body.style.overflow = "hidden";

        document.querySelector(".sidebar").style.pointerEvents = "none";
        document.querySelector(".content").style.pointerEvents = "none";
    }
});

document.addEventListener("DOMContentLoaded", () => {

    const today = new Date().toISOString().split("T")[0];

    document.getElementById("due_date").setAttribute("min", today);
});

document.addEventListener("keydown", e => {
    if (forceChangePassword == 1 && e.key === "Escape") {
        e.preventDefault();
    }
});

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

function toggleTasks(){
    const el = document.getElementById("taskList");
    el.classList.toggle("expanded");
}

function openFinalEvaluation(studentID){
       studentFinalEvaluation.classList.add("show");
    overlay.classList.add("show");

    const content = document.getElementById("finalEvaluationContent");

    document.getElementById("evalLoading").style.display = "block";
    document.getElementById("evaluationWrapper").style.display = "none";

    fetch("functions/getFinalEvaluation.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "studentID=" + encodeURIComponent(studentID)
    })
    .then(res => res.json())
    .then(data => {

        if (!data.success) {
            content.innerHTML = "<p>Error loading data</p>";
            return;
        }

        const student = data.student;
        const scores = data.scores;
        const tasks = data.tasks;

        document.getElementById("evalLoading").style.display = "none";
        document.getElementById("evaluationWrapper").style.display = "block";

        document.getElementById("evalAvatar").innerText =
            student.name.charAt(0).toUpperCase();

        document.getElementById("evalName").innerText = student.name;
        document.getElementById("evalID").innerText = "ID: " + student.studentID;

        document.getElementById("attendanceScore").innerText = scores.attendance + "%";
        document.getElementById("progressScore").innerText = scores.progress + "%";
        document.getElementById("taskScore").innerText = scores.tasks + "%";
        document.getElementById("finalScore").innerText = scores.final + "%";

        let taskHTML = "";

        tasks.forEach(task => {

            let color = "#9CA3AF";

            if (task.status === "APPROVED") color = "#059669";
            else if (task.status === "SUBMITTED") color = "#3B82F6";
            else if (task.status === "IN PROGRESS") color = "#F59E0B";
            else if (task.status === "REJECTED") color = "#EF4444";

            taskHTML += `
                <div class="task-mini">
                    <span>${task.title}</span>
                    <span>${task.score}%</span>
                </div>
            `;
        });

        document.getElementById("taskList").innerHTML = taskHTML;

        let final = scores.final;

        let remark = "";
        let remarkColor = "";

        if (final >= 90) {
            remark = "EXCELLENT - Recommended for certification";
            remarkColor = "#059669";
        }
        else if (final >= 80) {
            remark = "VERY GOOD - Approved";
            remarkColor = "#2563EB";
        }
        else if (final >= 70) {
            remark = "SATISFACTORY - Passed with remarks";
            remarkColor = "#F59E0B";
        }
        else if (final >= 60) {
            remark = "NEEDS IMPROVEMENT";
            remarkColor = "#F97316";
        }
        else {
            remark = "FAILED";
            remarkColor = "#DC2626";
        }

        const box = document.getElementById("recommendationBox");

        let title = "";
        let icon = "";
        let toneColor = "";
        let recommendation = "";
        let suggestions = [];
        let strengths = [];

        if (final >= 90) {

            title = "Outstanding Performance";
            icon = "🏆";
            toneColor = "#059669";

            recommendation =
                "The student has demonstrated exceptional professionalism, technical competency, and work discipline throughout the internship period.";

            strengths = [
                "Consistently completes assigned tasks with high quality",
                "Shows excellent responsibility and accountability",
                "Maintains professional communication and teamwork",
                "Demonstrates initiative and adaptability in the workplace"
            ];

            suggestions = [
                "Continue developing leadership capabilities",
                "Take on advanced technical responsibilities",
                "Mentor junior trainees when possible"
            ];
        }
        else if (final >= 80) {

            title = "Strong Performance";
            icon = "✅";
            toneColor = "#2563EB";

            recommendation =
                "The student performed very well and successfully met the internship expectations and requirements.";

            strengths = [
                "Reliable task completion",
                "Good communication and collaboration",
                "Demonstrates positive work ethics",
                "Shows willingness to learn and improve"
            ];

            suggestions = [
                "Improve confidence in decision-making",
                "Enhance problem-solving under pressure",
                "Continue refining technical proficiency"
            ];
        }
        else if (final >= 70) {

            title = "Satisfactory Performance";
            icon = "📘";
            toneColor = "#F59E0B";

            recommendation =
                "The student achieved acceptable internship performance with several areas requiring continuous improvement.";

            strengths = [
                "Completes assigned responsibilities",
                "Shows basic understanding of workflows",
                "Maintains attendance and participation"
            ];

            suggestions = [
                "Improve consistency in task execution",
                "Strengthen communication skills",
                "Develop better time management habits",
                "Increase initiative in workplace activities"
            ];
        }
        else if (final >= 60) {

            title = "Needs Improvement";
            icon = "⚠️";
            toneColor = "#F97316";

            recommendation =
                "The student requires additional supervision and improvement in several professional and technical areas.";

            strengths = [
                "Shows willingness to participate",
                "Demonstrates potential for growth"
            ];

            suggestions = [
                "Improve task completion consistency",
                "Develop stronger workplace discipline",
                "Increase engagement and communication",
                "Seek guidance proactively when difficulties arise"
            ];
        }
        else {

            title = "Performance Improvement Required";
            icon = "🚨";
            toneColor = "#DC2626";

            recommendation =
                "The student is currently below the expected internship performance standards and requires structured development support.";

            strengths = [
                "Has opportunity for professional growth",
                "Can improve with consistent guidance and mentoring"
            ];

            suggestions = [
                "Focus on attendance and punctuality",
                "Improve completion of assigned tasks",
                "Develop stronger communication habits",
                "Participate more actively in workplace responsibilities",
                "Follow supervisor feedback consistently"
            ];
        }

        box.innerHTML = `

        <div class="evaluation-summary-card">

            <div class="summary-header" style="border-left:5px solid ${toneColor}">
                <div class="summary-title">
                    <span class="summary-icon">${icon}</span>
                    <div>
                        <h3>${title}</h3>
                        <p>Supervisor Evaluation Summary</p>
                    </div>
                </div>

                <div class="summary-grade" style="background:${toneColor}">
                    ${final}%
                </div>
            </div>

            <table class="evaluation-table">

                <tr>
                    <td class="table-label">Recommendation</td>
                    <td>${recommendation}</td>
                </tr>

                <tr>
                    <td class="table-label">Key Strengths</td>
                    <td>
                        <ul>
                            ${strengths.map(item => `<li>${item}</li>`).join("")}
                        </ul>
                    </td>
                </tr>

                <tr>
                    <td class="table-label">Suggestions</td>
                    <td>
                        <ul>
                            ${suggestions.map(item => `<li>${item}</li>`).join("")}
                        </ul>
                    </td>
                </tr>

            </table>

        </div>
        `;
    })
    .catch(err => {
        console.error(err);
        content.innerHTML = "<p>Something went wrong</p>";
    });

    function bindSlider(sliderId, valueId) {
    const slider = document.getElementById(sliderId);
    const value = document.getElementById(valueId);

    if (!slider || !value) return;

        const update = () => {
            value.innerText = slider.value + "%";
        };

        slider.addEventListener("input", update);
        update();
    }

    bindSlider("ethicsRating", "ethicsValue");
    bindSlider("communicationRating", "communicationValue");
    bindSlider("initiativeRating", "initiativeValue");
    bindSlider("disciplineRating", "disciplineValue");
}

function saveFinalEvaluation() {

    const studentID = document.getElementById("evalID").innerText.replace("ID: ", "").trim();

    const attendance = document.getElementById("attendanceScore").innerText.replace("%", "").trim();
    const progress = document.getElementById("progressScore").innerText.replace("%", "").trim();
    const tasks = document.getElementById("taskScore").innerText.replace("%", "").trim();
    const final = document.getElementById("finalScore").innerText.replace("%", "").trim();

    const ethics = document.getElementById("ethicsRating").value;
    const communication = document.getElementById("communicationRating").value;
    const initiative = document.getElementById("initiativeRating").value;
    const discipline = document.getElementById("disciplineRating").value;

    const recommendation = document.getElementById("finalRecommendation").value;
    const remarks = document.getElementById("finalRemarks").value;

    const box = document.getElementById("recommendationBox");

    const rec_title =
        box.querySelector("h3")?.innerText || "";

    const rec_text =
        box.innerText || "";

    fetch("functions/saveFinalEvaluation.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body:
            "studentID=" + encodeURIComponent(studentID) +
            "&attendance=" + encodeURIComponent(attendance) +
            "&progress=" + encodeURIComponent(progress) +
            "&tasks=" + encodeURIComponent(tasks) +
            "&final=" + encodeURIComponent(final) +
            "&ethics=" + encodeURIComponent(ethics) +
            "&communication=" + encodeURIComponent(communication) +
            "&initiative=" + encodeURIComponent(initiative) +
            "&discipline=" + encodeURIComponent(discipline) +
            "&recommendation=" + encodeURIComponent(recommendation) +
            "&remarks=" + encodeURIComponent(remarks) +
            "&rec_title=" + encodeURIComponent(rec_title) +
            "&rec_text=" + encodeURIComponent(rec_text)
    })
    .then(res => res.json())
    .then(data => {

        if (data.success) {
            showToast("Evaluation saved successfully!", "success");

            studentFinalEvaluation.classList.remove("show");
            overlay.classList.remove("show");

        } else {
            showToast("Failed to save evaluation", "error");
        }

    })
    .catch(err => {
         showToast("Server error while saving evaluation", "error");
    });
}

function viewFinalReport(studentID) {

    document.getElementById("final-evaluation-view").setAttribute("data-student-id", studentID);

     fetch("functions/getFinalReport.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "studentID=" + encodeURIComponent(studentID)
    })
    .then(res => res.json())
    .then(data => {

        if(!data.success){
            showToast(data.message, "success");
            return;
        }

        const e = data.evaluation;

        const modal = document.getElementById("final-evaluation-view");
        modal.classList.add("show");
        overlay.classList.add("show");

        document.getElementById("reportStudentName").innerText = e.student_name;
        document.getElementById("reportStudentID").innerText = e.studentID ;

        document.getElementById("reportFinalScore").innerText = e.final_score + "%";

        document.getElementById("reportAttendance").innerText = e.attendance_score + "%";
        document.getElementById("reportProgress").innerText = e.progress_score + "%";
        document.getElementById("reportTasks").innerText = e.task_score + "%";

        document.getElementById("rEthics").innerText = e.ethics_rating;
        document.getElementById("rCommunication").innerText = e.communication_rating;
        document.getElementById("rInitiative").innerText = e.initiative_rating;
        document.getElementById("rDiscipline").innerText = e.discipline_rating;

        document.getElementById("rRecommendationTitle").innerText = e.final_recommendation;
        document.getElementById("rRecommendationText").innerHTML = formatRecommendationText(cleanRecommendation(e.recommendation_text));

        document.getElementById("rRemarks").innerText = e.final_remarks;
    }).catch(err => console.error(err));
    
}

function cleanRecommendation(text) {
    return text.replace(/^[A-Z\s]+/g, "").trim();
}

function formatRecommendationText(text) {

    if (!text) return "";

    const sections = {
        recommendation: "",
        strengths: [],
        suggestions: []
    };

    let current = "";

    const lines = text.split("\n").map(l => l.trim()).filter(l => l);

    lines.forEach(line => {

        if (/recommendation/i.test(line)) {
            current = "recommendation";
            sections.recommendation = line.replace(/recommendation/i, "").trim();
        }
        else if (/strengths/i.test(line)) {
            current = "strengths";
        }
        else if (/suggestions/i.test(line)) {
            current = "suggestions";
        }
        else {
            if (current === "strengths") {
                sections.strengths.push(line);
            }
            else if (current === "suggestions") {
                sections.suggestions.push(line);
            }
            else if (current === "recommendation") {
                sections.recommendation += " " + line;
            }
        }
    });

    return `
        <div class="evaluation-summary-view">

            <div class="info-card-view">
                <div class="info-card-view-header" onclick="toggleSection(this)">
                    <h4>Recommendation</h4>
                </div>
                <div class="info-card-view-body">
                    <p>${sections.recommendation}</p>
                </div>
            </div>

            <div class="info-card-view">
                <div class="info-card-view-header" onclick="toggleSection(this)" >
                    <h4>Key Strengths</h4>
                </div>
                <div class="info-card-view-body">
                    <ul>
                        ${sections.strengths.map(s => `<li>${s}</li>`).join("")}
                    </ul>
                </div>
            </div>

            <div class="info-card-view">
                <div class="info-card-view-header" onclick="toggleSection(this)">
                    <h4>Suggestions</h4>
                </div>
                <div class="info-card-view-body">
                    <ul>
                        ${sections.suggestions.map(s => `<li>${s}</li>`).join("")}
                    </ul>
                </div>
            </div>

        </div>
    `;
}

function downloadEvaluationReport() {

    const modal = document.getElementById("final-evaluation-view");
    const studentID = modal.getAttribute("data-student-id");

    if (!studentID) {
        showToast("Student ID not found", "warning");
        return;
    }

    window.open(
        "functions/exportFinalEvaluation.php?studentID=" + encodeURIComponent(studentID),
        "_blank"
    );
}

// settings
function togglePassword(inputId, icon) {
    const input = document.getElementById(inputId);

    if (input.type === "password") {
        input.type = "text";
        icon.textContent = "Show"; 
    } else {
        input.type = "password";
        icon.textContent = "Hide";
    }
};

// toast
function showToast(message, type = "success") {
     const toast = document.getElementById("toast");

    toast.innerText = message;

    toast.style.background =
        type === "success" ? "#28a745" :
        type === "error" ? "#dc3545" :
        type === "warning" ? "#ffc107" :
        "#333";

    toast.classList.add("show");

    setTimeout(() => {
        toast.classList.remove("show");
    }, 3000);
}

// theme detector
function isDarkMode() {
    return document.body.classList.contains("dark-mode");
}

function getChartTextColor() {
    return document.body.classList.contains("dark-mode")
        ? '#e2e8f0'
        : '#000';
}

function reloadAllCharts() {

    if (window.lineChartInstance) {
        window.lineChartInstance.destroy();
    }

    if (window.pieChartInstance) {
        window.pieChartInstance.destroy();
    }

    if (window.barChartInstance) {
        window.barChartInstance.destroy();
    }

    loadTaskProgressChart();

    loadLineChart();
    loadPieChart();
}

function loadPieChart() {

    fetch("functions/getOverallAttendanceChart.php", {
        credentials: "include"
    })

    .then(res => res.json())

    .then(data => {

        const ctx = document.getElementById('pieChart');

        // Colors
        const pieColors = data.labels.map(label => {

            if (label === "Present") return "#22c55e";
            if (label === "Late") return "#f59e0b";
            if (label === "Absent") return "#ef4444";
            if (label === "Excused") return "#3b82f6";

            return "#6b7280";
        });

        if (window.pieChartInstance) {
            window.pieChartInstance.destroy();
        }

        window.pieChartInstance = new Chart(ctx, {

            type: 'doughnut',  

            data: {

                labels: data.labels,

                datasets: [{

                    data: data.values,

                    backgroundColor: pieColors,

                    borderWidth: 0,

                    hoverOffset: 8
                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                cutout: '70%', 

                plugins: {

                    legend: {

                        position: 'bottom',

                        labels: {

                            color: getChartTextColor(),

                            usePointStyle: true,

                            pointStyle: 'circle',

                            padding: 16,

                            font: {
                                size: 13
                            }

                        }

                    },

                    tooltip: {

                        backgroundColor: '#111827',

                        titleColor: '#fff',

                        bodyColor: '#d1d5db',

                        borderColor: 'rgba(255,255,255,0.08)',

                        borderWidth: 0,

                        padding: 10

                    }

                }

            },

            plugins: [{

                    id: 'centerText',

                    beforeDraw(chart) {

                        const { width, height, ctx } = chart;

                        ctx.save();

                        const total =
                            chart.data.datasets[0].data
                            .reduce((a, b) => a + b, 0);

                        const textColor = isDarkMode() ? "#e2e8f0" : "#000";

                        ctx.font = "bold 28px sans-serif";
                        ctx.fillStyle = textColor;
                        ctx.textAlign = "center";
                        ctx.textBaseline = "middle";

                
                        ctx.fillText(total, width / 2, height / 2 - 10);

                    
                        ctx.font = "18px sans-serif";
                        ctx.fillStyle = textColor;

                        ctx.fillText("TOTAL", width / 2, height / 2 + 18);

                        ctx.restore();
                    }

                }]

        });

    })

    .catch(err => console.error("Pie chart error:", err));
}
// line chart
function populateMonthDropdown() {

    const select = document.getElementById("monthSelector");

    const months = [
        "January", "February", "March",
        "April", "May", "June",
        "July", "August", "September",
        "October", "November", "December"
    ];

    const currentDate = new Date();

    const currentMonth = currentDate.getMonth() + 1;

    const currentYear = currentDate.getFullYear();

    months.forEach((month, index) => {

        const option = document.createElement("option");

        const monthValue = String(index + 1).padStart(2, '0');

        option.value = `${currentYear}-${monthValue}`;

        option.textContent = `${month} ${currentYear}`;

        if ((index + 1) === currentMonth) {
            option.selected = true;
        }

        select.appendChild(option);

    });

}

function loadLineChart() {

    const selectedMonth =
        document.getElementById("monthSelector").value;

        window.currentMonth = selectedMonth;

    fetch(`functions/getAttendanceChart.php?month=${selectedMonth}`, {
        credentials: "include"
    })

    .then(res => res.json())

    .then(data => {

        const ctx = document.getElementById('lineChart');

        if (window.lineChartInstance) {
            window.lineChartInstance.destroy();
        }

        window.currentChartData = data;

        window.lineChartInstance = new Chart(ctx, {

            type: 'line',

            data: {

                labels: data.labels,

                datasets: [

                    {
                        label: 'Present',

                        data: data.present,

                        borderColor: '#22c55e',

                        backgroundColor: 'transparent',

                        borderWidth: 2,

                        tension: 0.2,

                        pointRadius: 2,

                        pointHoverRadius: 5
                    },

                    {
                        label: 'Late',

                        data: data.late,

                        borderColor: '#f59e0b',

                        backgroundColor: 'transparent',

                        borderWidth: 2,

                        tension: 0.2,

                        pointRadius: 2,

                        pointHoverRadius: 5
                    },

                    {
                        label: 'Excused',

                        data: data.excused,

                        borderColor: '#3b82f6',

                        backgroundColor: 'transparent',

                        borderWidth: 2,

                        tension: 0.2,

                        pointRadius: 2,

                        pointHoverRadius: 5
                    },

                    {
                        label: 'Absent',

                        data: data.absent,

                        borderColor: '#ef4444',

                        backgroundColor: 'transparent',

                        borderWidth: 2,

                        tension: 0.2,

                        pointRadius: 2,

                        pointHoverRadius: 5
                    }

                ]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                interaction: {
                    mode: 'index',
                    intersect: false
                },

                plugins: {

                    legend: {

                        position: 'top',

                        align: 'start',

                        labels: {

                            color: getChartTextColor(),

                            usePointStyle: true,

                            pointStyle: 'line',

                            padding: 16,

                            font: {
                                size: 12
                            }

                        }

                    },

                    tooltip: {

                        backgroundColor: '#111827',

                        titleColor: '#fff',

                        bodyColor: '#d1d5db',

                        borderColor: 'rgba(255,255,255,0.08)',

                        borderWidth: 1

                    }

                },

                scales: {

                    x: {

                        ticks: {
                            color: isDarkMode() ? '#cbd5e1' : '#9ca3af'
                        },

                        grid: {
                            display: false
                        }

                    },

                    y: {

                        beginAtZero: true,

                        ticks: {
                            color: isDarkMode() ? '#cbd5e1' : '#9ca3af',
                            stepSize: 1
                        },

                        grid: {
                            color: isDarkMode()
                                ? 'rgba(255,255,255,0.08)'
                                : 'rgba(0,0,0,0.20)'
                        }

                    }

                },
                layout: {
                        padding: 0
                    },

            }

        });

    })

    .catch(err => console.error("Line chart error:", err));
}

function downloadAttendancePDF() {

    const superID = window.currentSuperID;
    const month = window.currentMonth || document.getElementById("monthSelector").value;

    if (!superID || !month) {
        showToast("Missing supervisor or month.", "error");
        return;
    }

    const url = `functions/download_month_attendance.php?superID=${superID}&month=${month}`;

    window.open(url, "_blank");
}
// function loadLineChart() {
//     fetch("functions/getAttendanceChart.php", {credentials: "include"})
//          .then(res => res.json())
//         .then(data => {

//             const ctx = document.getElementById('lineChart');

//             if (window.lineChartInstance) {
//                 window.lineChartInstance.destroy();
//             }

//             window.lineChartInstance = new Chart(ctx, {
//                 type: 'line',
//                 data: {
//                     labels: data.labels,
//                     datasets: [
//                         {
//                             label: 'Present',
//                             data: data.present,
//                             borderColor: '#22c55e',
//                             backgroundColor: 'rgba(34, 197, 94, 0.2)',
//                             fill: true
//                         },
//                         {
//                             label: 'Late',
//                             data: data.late,
//                             borderColor: '#f59e0b',
//                             backgroundColor: 'rgba(245, 158, 11, 0.2)',
//                             fill: true
//                         },
//                         {
//                             label: 'Absent',
//                             data: data.absent,
//                             borderColor: '#ef4444',
//                             backgroundColor: 'rgba(239, 68, 68, 0.2)',
//                             fill: true
//                         }
//                     ]
//                 },
//                 options: {
//                     responsive: true,
//                     interaction : {
//                         mode :'index',
//                         intersect : false
//                     },
//                     plugins: {
//                         legend: {
//                             position: 'bottom'
//                         }
//                     },
//                     scales: {
//                         y: {
//                             beginAtZero: true
//                         }
//                     }
//                 }
//             });

//         })
//         .catch(err => console.error("Line chart error:", err));

// }

function loadTaskProgressChart() {
    fetch("functions/getTaskChart.php", { credentials: "include" })
        .then(res => res.json())
        .then(data => {

            const ctx = document.getElementById('barChart');

            if (window.barChartInstance) {
                window.barChartInstance.destroy();
            }

            window.barChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Task Completion %',
                        data: data.progress,

                        backgroundColor: data.progress.map(value => {
                            if (value >= 80) return '#22c55e';
                            if (value >= 50) return '#f59e0b';
                            return '#ef4444';
                        }),

                        borderRadius: 10,
                        barThickness: 18
                    }]
                },
                options: {

                    responsive: true,

                    maintainAspectRatio: false,

                    interaction: {
                        mode: 'index',
                        intersect: false
                    },

                    plugins: {

                        legend: {
                            display: false
                        },

                        tooltip: {

                            backgroundColor: '#111827',
                            titleColor: '#fff',
                            bodyColor: '#d1d5db',

                            callbacks: {
                                label: function(context) {
                                    return context.raw + "%";
                                }
                            }
                        }

                    },

                    scales: {

                        x: {

                            ticks: {
                                color: '#9ca3af',
                                font: {
                                    size: 11
                                }
                            },

                            grid: {
                                display: false
                            }

                        },

                        y: {

                            beginAtZero: true,
                            max: 100,

                            ticks: {
                                color: '#9ca3af',
                                callback: value => value + "%"
                            },

                            grid: {
                                color: 'rgba(0,0,0,0.20)'
                            }

                        }

                    }

                }
            });

        })
        .catch(err => console.error("Progress chart error:", err));
}

function loadStudentProgressChart(studentID) {

    fetch("functions/getStudentProgress.php?studentID=" + studentID, {credentials: "include"})
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

    fetch("functions/getStudentAttendance.php?studentID=" + studentID, {credentials: "include"})
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

    fetch("functions/getStudentAttendanceTable.php?studentID=" + studentID, {credentials: "include"})
        .then(res => res.text())
        .then(data => {
            document.getElementById("attendanceReportBody").innerHTML = data;
        })
        .catch(err => console.error("Table load error:", err));
}

function loadStudentTaskTable(studentID) {

    fetch("functions/getStudentTaskTable.php?studentID=" + studentID, {credentials: "include"})
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

// quick actions
function openCreateTask(){
    createTask.classList.add("show");
    overlay.classList.add("show");
}


function viewEvaluationReport(studentID) {

    fetch("functions/getStudentEvaluationReport.php?studentID=" + studentID, {credentials: "include"})
        .then(res => res.json())
        .then(data => {

            document.getElementById("reportSummary").innerHTML = `
                <div class="report-card">

                    <h2><i class="bi bi-bar-chart-fill"></i> Student Evaluation Report</h2>

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

function previewTask(taskID) {

    overlay.classList.add("show");
    studentApplicationApprove.classList.add("show");

    studentApplicationApprove.innerHTML = '';

    fetch("functions/getTaskApproveData.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "taskID=" + encodeURIComponent(taskID)
    })
    .then(res => res.text())
    .then(data => {
        studentApplicationApprove.innerHTML = data;
    });
}

function updateTaskStatus(taskID, status) {

    const feedback = document.getElementById("supervisorFeedback")?.value.trim() || "";
    
    if (status === "REJECTED" && !feedback) {
        showToast("Please provide supervisor feedback before rejecting the task.", "warning");
        return;
    }

    fetch("functions/updateTaskStatus.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "taskID=" + encodeURIComponent(taskID) +
              "&status=" + encodeURIComponent(status) +
              "&supervisor_feedback=" + encodeURIComponent(feedback)
              
    })
    .then(async res => {

    const text = await res.text();

    console.log("RAW RESPONSE:");
    console.log(text);

    return JSON.parse(text);
})
    .then(data => {
        
        showToast(data.message, "success");

        if (data.status === "success") {
            overlay.classList.remove("show");
            studentApplicationApprove.classList.remove("show");
            location.reload();
        }
    }).catch(err => {
        console.error(err);
        showToast("Something went wrong", "error");
    });
}

// function previewImage(src) {

//     const modal = document.getElementById("imagePreviewModal");
//     const img = document.getElementById("previewImg");

//     const files = src.split(",");

//     const cleanSrc = files[0].trim();

//     img.src = cleanSrc;
//     modal.style.display = "flex";
// }

// preview Imgae



function previewFile(src) {

    const modal = document.getElementById("imagePreviewModal");
    const container = document.getElementById("previewContainer");

    const cleanSrc = src.trim();
    const safeSrc = encodeURI(cleanSrc);

    const extension = cleanSrc.split('.').pop().toLowerCase();

    container.innerHTML = "";

    if (["jpg", "jpeg", "png", "gif", "webp"].includes(extension)) {

        container.innerHTML = `
           <img src="${safeSrc}" style="max-width:100%; max-height:85vh; border-radius:10px; object-fit:contain;">
        `;

    } 
    else if (extension === "pdf") {

        container.innerHTML = `
            <iframe src="${safeSrc}" 
    style="width:100%;height:80vh;border:none;border-radius:10px;"></iframe>

<p style="margin-top:10px; color:#cbd5e1; font-size:13px;">
    If the PDF doesn't load,
    <a href="${safeSrc}" target="_blank">Click here to open file</a>
</p>
        `;

    } 
    else {

        container.innerHTML = `
                <div style="display:flex; flex-direction:column; gap:10px; align-items:center;">
                    <img src="${safeSrc}" style="max-width:100%; max-height:85vh; border-radius:10px; object-fit:contain;">

                    <a href="${safeSrc}" download 
                    style="
                            text-decoration:none;
                            padding:10px 14px;
                            background:rgba(34,197,94,0.15);
                            color:#22c55e;
                            border-radius:8px;
                            font-weight:600;
                            border:1px solid rgba(34,197,94,0.3);
                    ">
                        Download Image
                    </a>
                </div>
            `;
    }

    modal.style.display = "flex";
}

function previewImage(src) {
    previewFile(src);
}

document.getElementById("closeImagePreview").addEventListener("click", function () {
    document.getElementById("imagePreviewModal").style.display = "none";
});

document.getElementById("imagePreviewModal").addEventListener("click", function (e) {
    if (e.target.id === "imagePreviewModal") {
        this.style.display = "none";
    }
});

// view task
function viewTask(taskID) {
    overlay.classList.add("show");
    viewTaskDetails.classList.add("show");

    fetch("functions/getTaskDetailsFinished.php?taskID=" + taskID, {credentials: "include"})
        .then(res => res.json())
        .then(data => {

            const fileBtn = document.getElementById("viewUploadedFileBtn");
            const fileStatus = document.getElementById("uploadedFileStatus");

            if (data.submission_file) {
                const files = data.submission_file.split(",");
                const firstFile = files[0].trim();

                const filePath = "../../uploads/student_tasks/" +
                    data.studentID + "/" + firstFile;

                fileBtn.dataset.file = filePath;
                fileBtn.style.display = "inline-block";

                fileStatus.innerText = "Uploaded";
                fileStatus.className = "status-badge success";
            } else {
                fileBtn.style.display = "none";

                fileStatus.innerText = "No File Uploaded";
                fileStatus.className = "status-badge missing";
            }

            document.getElementById("modalTaskTitle").innerText = data.title;
            document.getElementById("modalTaskDesc").innerText = data.description;
            document.getElementById("modalTaskStatus").innerText = data.status;
            document.getElementById("modalTaskDue").innerText = data.due_date;
            document.getElementById("modalTaskCompleted").innerText =
                data.completed_at ? data.completed_at : "Not completed yet";

            document.getElementById("modalTaskProgress").innerText =
                data.progress + "%";

            const warningBox = document.getElementById("taskDeadlineWarning");
            const warningText = document.getElementById("taskDeadlineWarningText");

            warningBox.style.display = "none";
            warningBox.className = "task-deadline-warning";

            if (data.due_date) {

                const today = new Date();
                const dueDate = new Date(data.due_date);

                today.setHours(0,0,0,0);
                dueDate.setHours(0,0,0,0);

                const diffTime = dueDate - today;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                const taskStatus = data.status;

                if (taskStatus !== "APPROVED" && taskStatus !== "REJECTED") {

                    if (diffDays < 0) {

                        warningBox.style.display = "flex";
                        warningBox.classList.add("danger");

                        warningText.innerHTML =
                            "⚠ This task is overdue.";

                    }
                    else if (diffDays <= 2) {

                        warningBox.style.display = "flex";
                        warningBox.classList.add("danger");

                        warningText.innerHTML =
                            "⚠ This task is nearing its deadline. Due in " +
                            diffDays + " day(s).";

                    }
                    else if (diffDays <= 5) {

                        warningBox.style.display = "flex";
                        warningBox.classList.add("warning");

                        warningText.innerHTML =
                            "⏳ This task is close to its due date.";

                    }
                }

                if(taskStatus === "APPROVED"){
                     warningBox.style.display = "flex";
                    warningBox.classList.add("success");

                    warningText.innerHTML =
                        "⚠ This task is approved.";
                }

                if(taskStatus === "REJECTED"){
                    warningBox.style.display = "flex";
                    warningBox.classList.add("danger");

                    warningText.innerHTML =
                        "⚠ This task is rejected.";
                }
            }

            document.getElementById("modalStudentNote").innerText =
                data.student_note ? data.student_note : "No student note provided";

            document.getElementById("modalSupervisorFeedback").innerText =
                data.supervisor_feedback ? data.supervisor_feedback : "No supervisor feedback";

            document.getElementById("studentNoteSection").style.display = "block";
            document.getElementById("supervisorFeedbackSection").style.display = "block";
        });
}

// edit tasks
function editTask(taskID) {

    fetch("functions/getTaskDetails.php?taskID=" + taskID, {credentials: "include"})
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
const modal = document.getElementById("deleteModal");

modal.classList.add("show");
overlay.classList.add("show");

document.getElementById("cancelDeleteBtn").onclick = () => {
    const modal = document.getElementById("deleteModal");

      modal.classList.remove("show");
      overlay.classList.remove("show");
};

document.getElementById("confirmDeleteBtn").onclick = () => {

    if (!taskID) return;

    const formData = new FormData();
    formData.append("taskID", taskID);

    fetch("functions/deleteTask.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        showToast(data.message, data.status);

        if (data.status === "success") {
        reloadTaskTable();
    }
    });

    modal.classList.remove("show");
    overlay.classList.remove("show");
};

    // if (!confirm("Are you sure you want to delete this task?")) {
    //     return;
    // }

    // const formData = new FormData();
    // formData.append("taskID", taskID);

    // fetch("functions/deleteTask.php", {
    //     method: "POST",
    //     body: formData
    // })
    // .then(res => res.json())
    // .then(data => {
    //     showToast(data.message, "success");

    //     if (data.status === "success") {
    //         location.reload();
    //     }
    // });
}

function reloadTaskTable() {

    const formData = new FormData();

    formData.append(
        "search",
        document.getElementById("assignedTaskSearch").value
    );

    formData.append(
        "status",
        document.getElementById("taskStatusFilter").value
    );

    formData.append(
        "deadline",
        document.getElementById("dateDeadline").value
    );

    fetch("functions/searchTask.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(html => {
        document.getElementById("assignedTaskBody").innerHTML = html;
    });
}

function closeTaskModal(){
    studentApplicationApprove.classList.remove("show");
    overlay.classList.remove("show");
}

closeViewTaskDetails.addEventListener("click", () => {
    overlay.classList.remove("show");
    viewTaskDetails.classList.remove("show");
});

// task switch
function setActiveTab(tabName) {

    document.querySelectorAll(".tab-btn").forEach(btn => {

        btn.classList.toggle(
            "active",
            btn.dataset.tab === tabName
        );
    });
}
function showManageTable() {

    taskSubmit.classList.remove("show");
    taskManage.classList.add("show");

    setActiveTab("manage");
}

function showSubmittedTable() {

    taskManage.classList.remove("show");
    taskSubmit.classList.add("show");

    setActiveTab("submitted");
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
     superActivity.style.display = "none";
     superAttendance.style.display = "none";
});

superOversightBtn.addEventListener("click", () => {
    superOversight.style.display = "block";

    superDashboard.style.display = "none";
    superStudents.style.display = "none";
    superEvaluation.style.display = "none";
    superActivity.style.display = "none";
    superAttendance.style.display = "none";

});

superStudentsBtn.addEventListener("click", () => {
    superStudents.style.display = "block";

    superDashboard.style.display = "none";
    superOversight.style.display = "none";
    superActivity.style.display = "none";
    superEvaluation.style.display = "none";
    superAttendance.style.display = "none";
});

superEvaluationBtn.addEventListener("click", () => {
    superEvaluation.style.display = "block";
    
    superStudents.style.display = "none";
    superDashboard.style.display = "none";
    superActivity.style.display = "none";
    superOversight.style.display = "none";
    superAttendance.style.display = "none";
    
});

superActivityBtn.addEventListener("click", () => {
    superActivity.style.display = "block";
    
    superEvaluation.style.display = "none";
    superStudents.style.display = "none";
    superDashboard.style.display = "none";
    superOversight.style.display = "none";
    superAttendance.style.display = "none";
    
});

superAttendanceBtn.addEventListener("click", () => {
    superAttendance.style.display = "block";
    
    superEvaluation.style.display = "none";
    superStudents.style.display = "none";
    superDashboard.style.display = "none";
    superOversight.style.display = "none";
    superActivity.style.display = "none";
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

// tasks
document.addEventListener("DOMContentLoaded", function () {

    const searchInput = document.getElementById("assignedTaskSearch");
    const statusFilter = document.getElementById("taskStatusFilter");
    const deadlineDate = document.getElementById("dateDeadline");
    const tableBody = document.getElementById("assignedTaskBody");

    if (!searchInput || !statusFilter || !tableBody || !deadlineDate) return;

    let timer;

    function fetchLogs() {

        const search = searchInput.value;
        const status = statusFilter.value.toUpperCase();
        const deadline = deadlineDate.value;

        fetch("functions/searchTask.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body:
                "search=" + encodeURIComponent(search) +
                "&status=" + encodeURIComponent(status) +
                "&deadline=" + encodeURIComponent(deadline)
              
        })
        .then(res => res.text())
        .then(data => {
            tableBody.innerHTML = data;
        });
    }

    searchInput.addEventListener("keyup", function () {
        clearTimeout(timer);
        timer = setTimeout(fetchLogs, 300);
    });

    statusFilter.addEventListener("change", fetchLogs);
    deadlineDate.addEventListener("change", fetchLogs);

    fetchLogs();
});

// modals
overlay.addEventListener("click", () => {
    overlay.classList.remove('show');

    createTask.classList.remove("show");
    studentChart.classList.remove("show");
    studentBreakdown.classList.remove("show");
    editTaskContainer.classList.remove("show");
    studentApplicationApprove.classList.remove("show");
    viewTaskDetails.classList.remove("show");
     accountModal.classList.remove('show');
      changePasswordModal.classList.remove('show');
      studentFinalEvaluation.classList.remove("show");
      studentFinalEvaluationView.classList.remove("show");
});


createTaskBtn.addEventListener("click", () => {
    overlay.classList.add("show");

    createTask.classList.add("show");
});

closeStudentBreakdown.addEventListener("click", () => {
    overlay.classList.remove("show");
    studentBreakdown.classList.remove("show");
});

closeSFinalEvaluation.addEventListener("click", () => {
    overlay.classList.remove("show");
    studentFinalEvaluation.classList.remove("show");

});

closeSFinalEvaluationView.addEventListener("click", () => {
    overlay.classList.remove("show");
    studentFinalEvaluationView.classList.remove("show");

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
studentList.addEventListener("click", function (e) {

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
        showToast("Please select at least one student.", "warning");
        return;
    }

    const formData = new FormData(this);

    const selectedSupervisorID = document.getElementById("superID").value;

    formData.append("superID", selectedSupervisorID);

    selectedTaskStudentIDs.forEach(id => {
        formData.append("studentIDs[]", id);
    });

    console.log(selectedTaskStudentIDs, selectedSupervisorID);

    fetch("functions/createAndAssignTask.php", {
    method: "POST",
    body: formData
})
.then(async res => {
    const text = await res.text();
    console.log("Raw response:", text);
    return JSON.parse(text);
})
.then(data => {

    if (data.status === "success") {
        reloadApprovalReportTable();
        reloadTaskTable();
        document.getElementById("createTaskForm").reset();

        selectedTaskStudentIDs = [];
        
        createTask.classList.remove("show");
        overlay.classList.remove("show");

        showToast(data.message, "success");
    }
})
.catch(err => {
    console.error(err);
    showToast("Something went wrong.", "error");
});
    
});

function reloadApprovalReportTable() {

    const search = document.getElementById("reportApprovalSearch")?.value || "";

    fetch("functions/searchApprovalReport.php", {
        method: "POST",
        headers: {
            "Content-Type":
                "application/x-www-form-urlencoded"
        },
        body: "search=" + encodeURIComponent(search)
    })
    .then(res => res.text())
    .then(html => {
        document.getElementById("approvalReportBody").innerHTML = html;
    });
}

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
        showToast(data.message,"success");

        if (data.status === "success") {
            location.reload();
        }
    });
});

closeEditTask.addEventListener("click", () => {
    overlay.classList.remove("show");
    editTaskContainer.classList.remove("show");

});

// activity search
// document.getElementById("superActivityLogSearch").addEventListener("keyup", function () {

//     clearTimeout(activitySearchTimer);

//     let value = this.value;

//     const superID = document.getElementById("superID").value;

//     activitySearchTimer = setTimeout(() => {

//         activityLogBody.classList.add("fade-out");

//         fetch("functions/searchSupervisorActivityLog.php", {
//             method: "POST",
//             headers: {
//                 "Content-Type": "application/x-www-form-urlencoded"
//             },
//             body: "search=" + encodeURIComponent(value) +
//                   "&superID=" + encodeURIComponent(superID)
//         })
//         .then(res => res.text())
//         .then(data => {

//             setTimeout(() => {
//                 activityLogBody.innerHTML = data;

//                 activityLogBody.classList.remove("fade-out");
//                 activityLogBody.classList.add("fade-in");

//                 setTimeout(() => {
//                     activityLogBody.classList.remove("fade-in");
//                 }, 200);

//             }, 200);

//         });

//     }, 300);
// });

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
    loadTaskProgressChart();
   
});

document.addEventListener("DOMContentLoaded", () => {

    populateMonthDropdown();

    loadLineChart();

    document
        .getElementById("monthSelector")
        .addEventListener("change", loadLineChart);

    document
        .getElementById("downloadChartBtn")
        .addEventListener("click", downloadAttendancePDF);

});

document.getElementById("darkModeToggle").addEventListener("click", () => {

     setTimeout(reloadAllCharts, 100);
});


// activity log
document.addEventListener("DOMContentLoaded", function () {

    const searchInput = document.getElementById("superActivityLogSearch");
    const moduleFilter = document.getElementById("moduleFilter");
    const dateFromInput = document.getElementById("dateFrom");
    const dateToInput = document.getElementById("dateTo");
    const tableBody = document.getElementById("activityLogTableBody");

    if (!searchInput || !moduleFilter || !tableBody || !dateFromInput || !dateToInput) return;

    let timer;

    function fetchLogs() {

        const search = searchInput.value;
        const module = moduleFilter.value.toUpperCase();
        const dateFrom = dateFromInput.value;
        const dateTo = dateToInput.value;

        fetch("functions/searchSupervisorActivityLog.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body:
                "search=" + encodeURIComponent(search) +
                "&module=" + encodeURIComponent(module) +
                "&dateFrom=" + encodeURIComponent(dateFrom) +
                "&dateTo=" + encodeURIComponent(dateTo)
              
        })
        .then(res => res.text())
        .then(data => {
            tableBody.innerHTML = data;
        });
    }

    searchInput.addEventListener("keyup", function () {
        clearTimeout(timer);
        timer = setTimeout(fetchLogs, 300);
    });

    moduleFilter.addEventListener("change", fetchLogs);
    dateFromInput.addEventListener("change", fetchLogs);
    dateToInput.addEventListener("change", fetchLogs);

    fetchLogs();
});

// supervisor attendance
document.addEventListener("DOMContentLoaded", function () {

    const searchInput = document.getElementById("studentAttendanceSearch");
    const statusFilter = document.getElementById("attendanceStatusFilter");
    const AttendanceDateFromInput = document.getElementById("attendanceDateFrom");
    const AttendanceDateToInput = document.getElementById("attendanceDateTo");
    const tableBody = document.getElementById("studentAttendanceBody");

    

    if (!searchInput || !statusFilter || !tableBody || !AttendanceDateFromInput || !AttendanceDateToInput) return;

    let timer;

    function fetchAttendance() {

        const search = searchInput.value;
        const status = statusFilter.value;
        const attendanceFrom = AttendanceDateFromInput.value;
        const attendanceTo = AttendanceDateToInput.value;

        fetch("functions/searchStudentAttendance.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body:
                "search=" + encodeURIComponent(search) +
                "&status=" + encodeURIComponent(status) + 
                "&attendanceFrom=" + encodeURIComponent(attendanceFrom) +
                "&attendanceTo=" + encodeURIComponent(attendanceTo)
        })
        .then(res => res.text())
        .then(data => {
            tableBody.innerHTML = data;
        })
        .catch(err => {
            console.error("Attendance fetch error:", err);
        });
    }

    searchInput.addEventListener("keyup", function () {
        clearTimeout(timer);
        timer = setTimeout(fetchAttendance, 300);
    });

    statusFilter.addEventListener("change", fetchAttendance);
    AttendanceDateFromInput.addEventListener("change", fetchAttendance);
    AttendanceDateToInput.addEventListener("change", fetchAttendance);

    fetchAttendance();
});


// settings
openAccountBtn.addEventListener("click", () => {
    overlay.classList.add("show");
    accountModal.classList.add("show");
});

closeAccountBtn.addEventListener("click", () => {
    overlay.classList.remove("show");
    accountModal.classList.remove("show");
});

document.getElementById('openChangePasswordSupervisor').addEventListener('click', () => {
    accountModal.classList.remove('show');
    changePasswordModal.classList.add('show');
});

document.getElementById('backToAccountSettingsSupervisor').addEventListener('click', () => {
    changePasswordModal.classList.remove('show');
    accountModal.classList.add('show');
});

// dark mode
toggleBtn.addEventListener("click", () => {

    document.body.classList.toggle("dark-mode");

    if (document.body.classList.contains("dark-mode")) {
        localStorage.setItem("theme", "dark");
    } else {
        localStorage.setItem("theme", "light");
    }
});

window.addEventListener("DOMContentLoaded", () => {

    const savedTheme = localStorage.getItem("theme");

    if (savedTheme === "dark") {
        document.body.classList.add("dark-mode");
    }
});

