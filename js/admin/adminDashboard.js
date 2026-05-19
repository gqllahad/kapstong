const sideBar = document.querySelector('.sidebar');
const mainContent = document.querySelector('.content');
const navBar = document.querySelector('.navbar');

// menu profile
const menuToggle = document.getElementById("menuToggle");
const profileMenu = document.getElementById("profileMenu");

const menuItems = document.querySelectorAll('.menu li');

// layouts
const adminDashboardBtn = document.getElementById('admin-dashboard-btn');
const adminApprovalBtn = document.getElementById('admin-approval-btn');
const adminPreparationBtn = document.getElementById('admin-preparation-btn');
const adminAttendanceBtn = document.getElementById('admin-attendance-btn');

const adminDashboard = document.getElementById('admin-dashboard');
const adminApproval = document.getElementById('admin-approval');
const adminPreparation = document.getElementById('admin-preparation');
const adminAttendance = document.getElementById('admin-attendance');

// colors
const statusColors = {
    good: "#6c5eec",     
    warning: "#1b2cc4",  
    danger: "#dc3545"    
};

// bar chart
const xArray = ["Italy", "France", "Spain", "USA", "Argentina"];
const yArray = [55, 49, 44, 24, 15];

const data = [{
    x: xArray,
    y: yArray,
    type: "bar",
}];

const layout = {
    title: "World Wide Wine Production"
};

// search tables
let searchTimer;
const tableBody = document.getElementById("approvalTableBody");
const unTableBody = document.getElementById("unverifiedTableBody");
const allStudentBody = document.getElementById("allStudentBody");
const allSupervisorBody = document.getElementById("allSupervisorBody");
const activityLogBody = document.getElementById("activityLogTableBody");

// modals
const overlay = document.getElementById("overlay");

const allStudent = document.getElementById("all-student-modal");
const allStudentBtn = document.getElementById("viewAllStudentsBtn");
const allStudentClose = document.getElementById("closeAllStudentModal");


const studentApplicationView = document.getElementById("student-application-view");
// const studentApplicationViewBtn = document.getElementById("student-application-view-btn");
// const studentApplicationClose = document.getElementById("closeStudentViewModal");
let previousModal = null;

const studentApplicationApprove = document.getElementById("student-application-approve");
// const studentApplicationApproveBtn = document.getElementById("student-application-approve-btn");
// const studentApplicationApproveClose = document.getElementById("closeStudentApproveModal");

const studentApplicationReject = document.getElementById("student-application-reject");
// const studentApplicationRejectBtn = document.getElementById("student-application-reject-btn");
// const studentApplicationRejectClose = document.getElementById("closeStudentRejectModal");

const allUnverified = document.getElementById("all-unverified-modal");
const allUnverifiedBtn = document.getElementById("viewAllUnverifiedBtn");
const allUnverifiedClose = document.getElementById("closeAllUnverifiedModal");

const allSupervisor = document.getElementById("all-supervisor-modal");
const allSupervisorBtn = document.getElementById("viewAllSupervisorBtn");
const allSupervisorClose = document.getElementById("closeAllSupervisorModal");

const superCreate = document.getElementById("supervisor-container");
const superCreateBtn = document.getElementById("supervisor-btn");
const superCloseBtn = document.getElementById("closeCreateSupervisorModal");

const superView = document.getElementById("supervisor-view");
// const superAssignView = document.getElementById("supervisor-assigned-view");

const AssignStudent = document.getElementById("assign-student-container");
const AssignStudentBtn = document.getElementById("assign-student-btn");
const AssignCloseBtn = document.getElementById("closeAssignModal");

const ojtSetupBtn = document.getElementById("ojt-program-btn");
const closeOjtSetupBtn = document.getElementById("closeOjtProgramModal");

const departmentManagementBtn = document.getElementById("department-management-btn");
const departmentManagementModal = document.getElementById("department-management-modal");
const filterProgramsBody = document.getElementById("filterProgramsBody");

const closeDeparmentManagementBtn = document.getElementById("closeDepartmentManagement");
const closeDeparmentManagementModalBtn = document.getElementById("closeDepartmentManagementModal");


const rfidAttendanceBtn = document.getElementById("rfid-attendance-btn");
const closeRfidAttendanceBtn = document.getElementById("closeRfidAttendanceModal");

const evaluationSettingsBtn = document.getElementById("evaluation-settings-btn");
const closeEvaluationSettingsBtn = document.getElementById("closeEvaluationSettingsModal");


const requirementsSetupBtn = document.getElementById("requirements-setup-btn");
const closeRequirementsSetupBtn = document.getElementById("closeRequirementsSetupModal");

const ojtSetup = document.getElementById("ojt-program-container");
const departmentManagement = document.getElementById("department-management-container");
const rfidAttendance = document.getElementById("rfid-attendance-container");
const evaluationSettings = document.getElementById("evaluation-settings-container");
const requirementsSetup = document.getElementById("requirements-setup-container");

const viewAllBtn = document.getElementById("view-all-btn");
const viewAll = document.getElementById("view-all-modal");
const closeViewAllBtn = document.getElementById("closeViewAllModal");

const rfidRegister = document.getElementById("rfid-register-modal");
const closeRfidRegister = document.getElementById("closeRfidRegisterModal");

// downloads
const downloadAllStudentBtn = document.getElementById("downloadAllStudentBtn");
const downloadAllStudent = document.getElementById("download-all-student-modal");
const closeDownloadAllStudent = document.getElementById("closeAllStudentDownloadModal");

const downloadAllSupervisorBtn = document.getElementById("downloadAllSupervisorBtn");
const downloadAllSupervisor = document.getElementById("download-all-supervisor-modal");
const closeDownloadAllSupervisor = document.getElementById("closeAllSupervisorDownloadModal");

let selectedStudentIDs = [];
let selectedSupervisorID = null;

// form
const supervisorForm = document.getElementById("createSupervisorForm");

const messageBox = document.getElementById("formMessage");

const assignForm = document.getElementById("assignStudentSupervisorForm");
const studentList = document.getElementById("studentList");
const supervisorList = document.getElementById("supervisorList");

// timwers
let activitySearchTimer;
let assignSearchTimer;
let timer;

let isReassignMode = false;

// Functions 

// open rfid
function openRfid(){
     window.open("../../php/rfid_test.php", "_blank");
}

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

function viewUser(studentID, source) {

    previousModal = source;

    if (source === "allStudent") {
        allStudent.classList.remove("show");
        studentApplicationView.classList.add("show");
    }
    if(source === "UnverifiedStudent"){
        allUnverified.classList.remove("show");
        studentApplicationView.classList.add("show");
    }
    if(source === "supervisorView"){
        superView.classList.remove("show");
        studentApplicationView.classList.add("show");
    }
    if(source === "main"){
        studentApplicationView.classList.add("show");
    }
    
    overlay.classList.add("show");

        studentApplicationView.innerHTML = '';

    
    fetch("functions/getStudentData.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "studentID=" + encodeURIComponent(studentID)
    })
    .then(res => res.text())
    .then(data => {
        studentApplicationView.innerHTML = data;
    });
};

function openRFIDRegisterModal(studentID)
{
    document.getElementById("rfidStudentID").value = studentID;

    allStudent.classList.remove("show");
    rfidRegister.classList.add("show");

     setTimeout(() => {
        document.getElementById("rfid_uid").focus();
    }, 200);
}

function submitRFIDRegister()
{
    const studentID = document.getElementById("rfidStudentID").value;

    const rfid_uid = document.getElementById("rfid_uid").value.trim();

    if(rfid_uid === "")
    {
        showToast("Please scan RFID first", "warning"); 
        return;
    }

    fetch("functions/register_rfid.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body:
            "studentID=" + encodeURIComponent(studentID)
            + "&rfid_uid=" + encodeURIComponent(rfid_uid)
    })
    .then(response => response.text())
    .then(data => {

        if(data === "success")
        {
            showToast("RFID Registered Successfully", "success");


            setTimeout(() => {
                location.reload();
            }, 500);
            
        }
        else
        {
            showToast(data, "error");
        }

    })
    .catch(error => {

        showToast(error, "error");
    });
}


function viewSupervisor(superID) {
    
    allSupervisor.classList.remove("show");
    superView.classList.add("show");
    overlay.classList.add("show");

    superView.innerHTML = '';

    fetch("functions/getSupervisorData.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "superID=" + encodeURIComponent(superID)
    })
    .then(res => res.text())
    .then(data => {
        superView.innerHTML = data;
    });
}

function approveUser(studentID) {
    
    overlay.classList.add("show");
    studentApplicationApprove.classList.add("show");

    studentApplicationApprove.innerHTML = '';
    
    fetch("functions/getStudentApproveData.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "studentID=" + encodeURIComponent(studentID)
    })
    .then(res => res.text())
    .then(data => {
        studentApplicationApprove.innerHTML = data;

         initApproveModal(studentID);

        // const checkbox = document.getElementById("confirmApprove");
        // const btn = document.getElementById("approveBtn");

        // if (checkbox && btn) {
        //     checkbox.addEventListener("change", () => {
        //         btn.disabled = !checkbox.checked;
        //     });
        // }
    }).catch(() => {
            showToast("Server error occurred", "error");
        });
};

function initApproveModal(studentID) {
    const checkbox = document.getElementById("confirmApprove");
    const approveBtn = document.getElementById("approveBtn");
    const rfidInput = document.getElementById("rfidInput");
    const statusText = document.getElementById("rfidStatus");

    if (!checkbox || !approveBtn || !rfidInput) return;

    approveBtn.disabled = true;

    setTimeout(() => rfidInput.focus(), 300);

    rfidInput.addEventListener("input", function () {
        clearTimeout(timer);

    timer = setTimeout(() => {
        const value = this.value.trim();

        if (value !== "") {
            statusText.innerText = "RFID Captured ✔";

            if (checkbox.checked) {
                approveBtn.disabled = false;
            }

            this.disabled = true;
        } else {
            statusText.innerText = "Waiting for scan...";
            approveBtn.disabled = true;
         }
        }, 200);
    });

    checkbox.addEventListener("change", function () {
        if (this.checked && rfidInput.value.trim().length > 5) {
            approveBtn.disabled = false;
        } else {
            approveBtn.disabled = true;
        }
    });

    rfidInput.addEventListener("keypress", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            approveStudent(studentID);
        }
    });
}

function approveStudent(studentID) {
    const rfidInput = document.getElementById("rfidInput");
    const approveBtn = document.getElementById("approveBtn");

    const rfid = rfidInput?.value.trim();

    if (!rfid) {
        showToast("Please scan RFID first or use 'Approve Without RFID'.", "error");
        return;
    }

    if (!confirm("Approve student and register RFID?")) return;

    approveBtn.disabled = true;

    fetch("functions/approveStudent.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "studentID=" + encodeURIComponent(studentID) + 
              "&rfid=" + encodeURIComponent(rfid)
    })
    .then(res => res.text())
    .then(response => {
        showToast(response, "success");
        closeApproveModal();
        location.reload();
    })
    .catch(err => {
        showToast(err, "error");
        approveBtn.disabled = false;
    });
}

function resetRFID() {
    const input = document.getElementById("rfidInput");
    const status = document.getElementById("rfidStatus");

    input.disabled = false;
    input.value = "";
    input.focus();

    if (status) status.innerText = "Waiting for scan...";
}

function approveWithoutRFID(studentID) {
    if (!confirm("Approve student WITHOUT RFID? You can register it later.")) return;

    fetch("functions/approveStudent.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "studentID=" + encodeURIComponent(studentID) + 
              "&no_rfid=1"
    })
    .then(res => res.text())
    .then(response => {
        showToast(response, "success");
        closeApproveModal();
        location.reload();
    })
    showToast(err, "error");
}


function rejectUser(studentID) {
    
    overlay.classList.add("show");
    studentApplicationReject.classList.add("show");

    studentApplicationReject.innerHTML = '';
    
    fetch("functions/getStudentRejectData.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "studentID=" + encodeURIComponent(studentID)
    })
    .then(res => res.text())
    .then(data => {
        studentApplicationReject.innerHTML = data;

        const checkbox = document.getElementById("confirmReject");
        const btn = document.getElementById("rejectBtn");

        if (checkbox && btn) {
            checkbox.addEventListener("change", () => {
                btn.disabled = !checkbox.checked;
            });
        }
    });
};

function rejectStudent(studentID) {

    const reason = document.getElementById("rejectReason").value;
    const note = document.getElementById("rejectNote").value;

    if (!reason) {
        showToast("Please select a rejection reason", "error");
        return;
    }

    if (!confirm("Are you sure you want to reject this student?")) return;

    fetch("functions/rejectStudent.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "studentID=" + encodeURIComponent(studentID)
    })
    .then(res => res.text())
    .then(response => {
        showToast("Student rejected successfully!", "success");

        closeRejectModal();
        location.reload();
    })
    showToast(err, "error");
}

function closeSuperViewModal() {

    superView.classList.remove("show");
     allSupervisor.classList.add("show");

    overlay.classList.add("show");
    
}

function closeApproveModal() {

    const rfidInput = document.getElementById("rfidInput");
    const statusText = document.getElementById("rfidStatus");

    if (rfidInput) {
        rfidInput.value = "";
        rfidInput.disabled = false;
    }

    if (statusText) {
        statusText.innerText = "Waiting for scan...";
    }

    studentApplicationApprove.classList.remove("show");
    overlay.classList.remove("show");
}

function closeRejectModal() {

    studentApplicationReject.classList.remove("show");
    overlay.classList.remove("show");
    
}

function closeModal() {

   studentApplicationView.classList.remove("show");

    if (previousModal === "allStudent") {
        allStudent.classList.add("show");
        overlay.classList.add("show");
    } else if(previousModal === "UnverifiedStudent") {
        allUnverified.classList.add("show");
        overlay.classList.add("show");
    }else if(previousModal === "supervisorView"){
        superView.classList.add("show");
        overlay.classList.add("show");

    }else{
        overlay.classList.remove("show");
    }

    previousModal = null;
}

function initSupervisorForm() {
    const checkbox = document.getElementById("confirmCreateSupervisor");
    const btn = document.getElementById("createSupervisorBtn");

    if (!checkbox || !btn) return;

    checkbox.addEventListener("change", () => {
        btn.disabled = !checkbox.checked;
    });
}

function previewImage(src) {
    document.getElementById("previewImg").src = src;
    document.getElementById("imagePreviewModal").style.display = "flex";
}

document.addEventListener("click", function (e) {

    if (e.target.id === "closeImagePreview") {
        document.getElementById("imagePreviewModal").style.display = "none";
        document.getElementById("previewImg").src = "";
    }

    if (e.target.id === "imagePreviewModal") {
        document.getElementById("imagePreviewModal").style.display = "none";
        document.getElementById("previewImg").src = "";
    }
});

function toggleSection(header) {
    const card = header.parentElement;

    document.querySelectorAll(".info-card").forEach(c => {
        if (c !== card) c.classList.remove("active");
    });

    card.classList.toggle("active");
}

function getStatusColor(value) {
    if (value >= 1) return statusColors.good;
    if (value >= 20) return statusColors.warning;
    return statusColors.danger;
}

// reassign student
function reAssignUser(studentID) {

    fetch("functions/unAssignStudent.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "studentID=" + encodeURIComponent(studentID)
    })
    .then(res => res.json())
    .then(data => {
        
        if (data.status === "success") {
            showToast("Student Reassigning...", "success");

             isReassignMode = true;

            refreshAssignStudentList();

            overlay.classList.add("show");
            AssignStudent.classList.add("show");

            studentApplicationView.classList.remove("show");

            AssignCloseBtn.style.display = "none";

        } else {
            showToast(data.message, "error");
        }

    })
    .catch(err => console.error(err));
}

function refreshAssignStudentList() {

    fetch("functions/renderAssignStudentList.php")
        .then(res => res.text())
        .then(html => {

            document.getElementById("studentList").innerHTML = html;

        })
        .catch(err => {
            console.error("Failed to refresh student list:", err);
        });
}


function loadBarChart() {
    fetch("../../php/admin/functions/getBarChartData.php")
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
                        label: 'Attendance per Day (Last 7 Days)',
                        data: data.values,
                        backgroundColor: "#60a5fa",
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            let existing = document.querySelector(".chart-empty-message");

        if (existing) {
            existing.remove();
        }

        if (data.empty) {

            const message = document.createElement("p");

            message.className = "chart-empty-message";

            message.textContent =
                "No Task records available yet.";

            ctx.parentNode.appendChild(message);
        }

        })
        .catch(err => console.error("Bar chart error:", err));
}

// line chart
function loadLineChart() {
    fetch("../../php/admin/functions/getLineChartData.php")
        .then(res => res.json())
        .then(data => {

            const ctx = document.getElementById('lineChart');

            if (window.lineChartInstance) {
                window.lineChartInstance.destroy();
            }

            window.lineChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Attendance Trend (Last 30 Days)',
                        data: data.values,

                        borderColor: '#60a5fa',
                        backgroundColor: 'rgba(96, 165, 250, 0.2)',

                        fill: true,
                        tension: 0.4,

                        pointRadius: 5,
                        pointHoverRadius: 7
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
                            position: 'bottom'
                        }
                    },

                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

        let existing = document.querySelector(".chart-empty-message");

        if (existing) {
            existing.remove();
        }

        if (data.empty) {

            const message = document.createElement("p");

            message.className = "chart-empty-message";

            message.textContent =
                "No Attendance record trends yet.";

            ctx.parentNode.appendChild(message);
        }

        })
        .catch(err => console.error("Line chart error:", err));
}


// pie chart
function loadPieChart() {
    fetch("../../php/admin/functions/getDonutChartData.php")
        .then(res => res.json())
        .then(data => {

            const ctx = document.getElementById('pieChart');

            if (window.pieChartInstance) {
                window.pieChartInstance.destroy();
            }

            const colors = {
                 Present: "#3b82f6",
                Late: "#93c5fd",
                Absent: "#1d4ed8",
                Excused: "#bfdbfe"
            };

            window.pieChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.values,
                        backgroundColor: data.labels.map(l => colors[l] || "#94a3b8"),
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },

                    cutout: '65%'
                }
            });

        let existing = document.querySelector(".chart-empty-message");

        if (existing) {
            existing.remove();
        }

        if (data.empty) {

            const message = document.createElement("p");

            message.className = "chart-empty-message";

            message.textContent =
                "No Attendance Overview available yet.";

            ctx.parentNode.appendChild(message);
        }

        })
        .catch(err => console.error("Donut chart error:", err));
}

// doughnut chart health score
function loadHealthScore() {

    fetch("../../php/admin/functions/getAttendanceHealthScore.php")
        .then(res => res.json())
        .then(data => {

            document.getElementById("health-score").innerText = data.score + "%";

            const total =
                Number(data.present) +
                Number(data.late) +
                Number(data.absent) +
                Number(data.excused);

            document.getElementById("total-attendance").innerText =
                "Total Attendance: " + total;
        });
}

// at risk students
function loadRiskStudents() {

    fetch("../../php/admin/functions/getAtRiskStudents.php")
        .then(res => res.json())
        .then(data => {

            const container = document.getElementById("risk-list");

            container.innerHTML = "";

            data.forEach(student => {

                const progress = parseFloat(student.progress_percent);

                let badgeClass = "track";
                let badgeText = "ON TRACK";
                let progressClass = "good";

                if (progress < 75) {
                    badgeClass = "behind";
                    badgeText = "BEHIND";
                    progressClass = "low";
                }
                else if (progress < 90) {
                    badgeClass = "soon";
                    badgeText = "DUE SOON";
                    progressClass = "medium";
                }

                container.innerHTML += `
                    <div class="task-item">

                        <div class="student-top">

                            <div class="student-info">
                                <strong>${student.name}</strong>
                                <span class="student-role">
                                    ${student.role}
                                </span>
                            </div>

                            <span class="status-badge ${badgeClass}">
                                ${badgeText}
                            </span>

                        </div>

                        <div class="progress-wrapper">

                            <div class="progress-label">
                                <span>
                                    ${student.completed_hours} / 
                                    ${student.required_hours} Hours
                                </span>

                                <span>
                                    ${progress}%
                                </span>
                            </div>

                            <div class="progress-bar">
                               <div 
                                    class="progress-fill ${progressClass}"
                                    style="width:${student.progress_percent}%">
                                </div>
                            </div>

                        <div class="student-meta">
                            <span class="meta-pill">
                                Absents: ${student.absents}
                            </span>

                            <span class="meta-pill">
                                Lates: ${student.lates}
                            </span>

                            <span class="meta-pill">
                                ${student.overdue_tasks} Overdue Tasks
                            </span>
                        </div>

                    </div>
                `;
            });
        });
}

function loadAllRiskStudents() {

    fetch("../../php/admin/functions/getAllAtRiskStudent.php")
        .then(res => res.json())
        .then(data => {

            const container = document.getElementById("all-risk-list");

            container.innerHTML = "";

            data.forEach(student => {

                const progress = parseFloat(student.progress_percent);

                let badgeClass = "track";
                let badgeText = "ON TRACK";
                let progressClass = "good";

                if (progress < 75) {
                    badgeClass = "behind";
                    badgeText = "BEHIND";
                    progressClass = "low";
                }
                else if (progress < 90) {
                    badgeClass = "soon";
                    badgeText = "DUE SOON";
                    progressClass = "medium";
                }

                container.innerHTML += `
                    <div class="task-item">

                        <div class="student-top">

                            <div class="student-info">
                                <strong>${student.name}</strong>
                                <span class="student-role">
                                    ${student.role}
                                </span>
                            </div>

                            <span class="status-badge ${badgeClass}">
                                ${badgeText}
                            </span>

                        </div>

                        <div class="progress-wrapper">

                            <div class="progress-label">
                                <span>
                                    ${student.completed_hours} / 
                                    ${student.required_hours} Hours
                                </span>

                                <span>
                                    ${progress}%
                                </span>
                            </div>

                            <div class="progress-bar">
                               <div 
                                    class="progress-fill ${progressClass}"
                                    style="width:${student.progress_percent}%">
                                </div>
                            </div>

                        <div class="student-meta">
                            <span class="meta-pill">
                                Absents: ${student.absents}
                            </span>

                            <span class="meta-pill">
                                Lates: ${student.lates}
                            </span>

                            <span class="meta-pill">
                                ${student.overdue_tasks} Overdue Tasks
                            </span>
                        </div>

                    </div>
                `;
            });
        });
}

// evaluation settings
function loadEvaluationSettings(superID = null) {

    fetch("functions/getEvaluationSettings.php?superID=" + superID)
        .then(res => res.json())
        .then(data => {

            document.getElementById("attendanceWeight").value = data.attendance_weight * 100;
            document.getElementById("progressWeight").value = data.progress_weight * 100;
            document.getElementById("taskWeight").value = data.task_weight * 100;
        })
}

function saveEvaluationSettings() {

    const attendance = document.getElementById("attendanceWeight").value / 100;
    const progress = document.getElementById("progressWeight").value / 100;
    const task = document.getElementById("taskWeight").value / 100;

     if (isNaN(attendance) || isNaN(progress) || isNaN(task)) {
        showToast("Please enter valid values.", "error");
        return;
    }

    const formData = new URLSearchParams();
    formData.append("attendance", attendance);
    formData.append("progress", progress);
    formData.append("task", task);

    fetch("functions/saveEvaluationSettings.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        showToast(data.message, data.status ? "success" : "error");
        
        if (data.status) {
            setTimeout(() => {
                evaluationSettings.classList.remove("show");
                overlay.classList.remove("show");
            }, 500);
        
        }

    }).catch(() => {
    showToast("Server error occurred", "error");
    });
}

// rfid settings

function loadRfidSettings() {

    document.getElementById("rfid_enabled")
        .addEventListener("change", function () {
            toggleRfidInputs(this.value);
        });

    fetch("functions/getRfidSettings.php")
    .then(res => res.json())
    .then(data => {

        document.querySelector("[name='rfid_enabled']").value = data.rfid_enabled;
        document.getElementById("morning_time_in").value = data.morning_time_in;
        document.getElementById("morning_time_out").value = data.morning_time_out;
        document.getElementById("afternoon_time_in").value = data.afternoon_time_in;
        document.getElementById("afternoon_time_out").value = data.afternoon_time_out;
        document.getElementById("allowed_late_minutes").value = data.late_threshold_minutes;
        document.getElementById("late_time").value = data.late_time;

        toggleRfidInputs(data.rfid_enabled);
    });
}

function toggleRfidInputs(isEnabled) {

    const inputs = [
        "morning_time_in",
        "morning_time_out",
        "afternoon_time_in",
        "afternoon_time_out",
        "allowed_late_minutes",
        "late_time"
    ];

    inputs.forEach(id => {
        const el = document.getElementById(id);

        if (el) {
            el.disabled = (isEnabled == "0");
        }
    });
}

function saveRfidSettings() {

    const formData = new URLSearchParams();

    formData.append("rfid_enabled",
        document.querySelector("[name='rfid_enabled']").value
    );

    formData.append("morning_time_in",
        document.getElementById("morning_time_in").value
    );

    formData.append("morning_time_out",
        document.getElementById("morning_time_out").value
    );

    formData.append("afternoon_time_in",
        document.getElementById("afternoon_time_in").value
    );

    formData.append("afternoon_time_out",
        document.getElementById("afternoon_time_out").value
    );

    formData.append("allowed_late_minutes",
        document.getElementById("allowed_late_minutes").value
    );

    formData.append("late_time",
        document.getElementById("late_time").value
    );

    fetch("functions/saveRfidSettings.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        showToast(data.message, data.status ? "success" : "error");

        if (data.status) {
            setTimeout(() => {
                rfidAttendance.classList.remove("show");
                overlay.classList.remove("show");
            }, 500);
        }

    })
    .catch(() => {
        showToast("Server error occurred", "error");
    });
}

// ojt settings

function loadActiveOJTCard() {

    fetch("functions/getActiveOJTCard.php")
    .then(res => res.text())
    .then(html => {

        document.getElementById("activeOJTContainer").innerHTML = html;

    })
    .catch(err => {
        showToast(err, "error");
    });
}

function loadOJTSettings() {

    fetch("functions/getOjtSettings.php")
    .then(res => res.json())
    .then(data => {

        if (data.success) {

            document.getElementById("academicYear").value =
                data.academic_year || "";

            document.getElementById("requiredHours").value =
                data.required_hours || "";

            document.getElementById("status").value =
                data.status || "ACTIVE";
        }

    });

}

function saveOJTSettings() {

    const academicYear = document.getElementById("academicYear").value;
    const requiredHours = document.getElementById("requiredHours").value;
    const status = document.getElementById("status").value;

    if (!academicYear && !requiredHours && !status) {
        showToast("Please enter one field!", "error");
        return;
    }

    const formData = new URLSearchParams();

    if (academicYear) {
        formData.append("academic_year", academicYear);
    }

    if (requiredHours) {
        formData.append("required_hours", requiredHours);
    }

    if (status) {
        formData.append("status", status);
    }

    fetch("functions/saveOjtSettings.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
       showToast(data.message, data.success ? "success" : "error");
       if (data.success) {

        loadActiveOJTCard();
        loadOJTSettings();

        ojtSetup.classList.remove("show");
        setTimeout(() => {
            
             ojtSetup.classList.add("show");
        }, 500);
        
    }
    })
    .catch(err => {
       showToast(err, "error");
    });
}

function filterPrograms() {

    const department = document.getElementById("departmentFilter").value;

    clearTimeout(searchTimer);

    let value = this.value;

    searchTimer = setTimeout(() => {
     filterProgramsBody.classList.add("fade-out");

    fetch("functions/filterPrograms.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "department=" + department
    })
    .then(res => res.text())
    .then(data => {

        setTimeout(() => {
                filterProgramsBody.innerHTML = data;

                filterProgramsBody.classList.remove("fade-out");
                filterProgramsBody.classList.add("fade-in");

                setTimeout(() => {
                    filterProgramsBody.classList.remove("fade-in");
                }, 200);

            }, 200);
        
    })
    .catch(() => {
        showToast("Failed to load data", "error");
    });
    }, 300);
}

function updateDepartmentCode() {
    const select = document.getElementById("prg_department");
    const selectedText = select.options[select.selectedIndex].text;

    const match = selectedText.match(/\((.*?)\)/);

    document.getElementById("prg_department_code").value =
        match ? match[1] : "";
}

function updateProgram() {

    const programId = document.getElementById("program_id").value;
    const programName = document.getElementById("prg_name").value;
    const programCode = document.getElementById("prg_acro").value;
    const departmentName = document.getElementById("prg_department").value;
    const departmentCode = document.getElementById("prg_department_code").value;
    const status = document.getElementById("prg_status").value;

    if (
        !programName ||
        !programCode ||
        !departmentName ||
        !departmentCode ||
        !status
    ) {
        showToast("Please complete all fields.", "error");
        return;
    }

    const formData = new URLSearchParams();
    formData.append("program_id", programId);
    formData.append("prg_name", programName);
    formData.append("prg_acro", programCode);
    formData.append("prg_department", departmentName);
    formData.append("prg_department_code", departmentCode);
    formData.append("status", status);

    fetch("functions/updateProgram.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        showToast(data.message, data.success ? "success" : "error");

        if (data.success) {

            document.getElementById("department-management-modal").style.display = "none";

            document.getElementById("program_id").value = "";
            document.getElementById("prg_name").value = "";
            document.getElementById("prg_acro").value = "";
            document.getElementById("prg_department").value = "";
            document.getElementById("prg_department_code").value = "";
            document.getElementById("prg_status").value = "";

            filterPrograms();

            setTimeout(() => {
                departmentManagementModal.classList.remove("show");
                departmentManagement.classList.add("show");
            }, 500);
        }
    })
    .catch(() => {
        showToast("Server error occurred.", "error");
    });
}

function openCreateCourseModal(){
    const modal = document.getElementById("create-course-modal");
    const exitModal = document.getElementById("closeCreateCourseModal");

    if (!modal) {
        showToast("Create Course Modal not found", "error");
        return;
    }

    departmentManagement.classList.remove("show");
    modal.classList.add("show");

    exitModal.addEventListener("click", () => {
        modal.classList.remove("show");
        departmentManagement.classList.add("show");

    });

    overlay.addEventListener("click", () => {
        modal.classList.remove("show");
        departmentManagement.classList.remove("show");
    })

}

function saveCourse() {

    const prg_name = document.getElementById("prg_name_create").value.trim();
    const prg_acro = document.getElementById("prg_acro_create").value.trim();
    const prg_department = document.getElementById("prg_department_create").value.trim();
    const prg_department_code = document.getElementById("prg_department_code_create").value.trim();
    const prg_status = document.getElementById("prg_status_create").value;

    console.log(prg_name, "HAHA");

    if (!prg_name || !prg_acro || !prg_department || !prg_department_code) {
        showToast("Please complete all fields!!", "error");
        return;
    }

    const formData = new URLSearchParams();

    formData.append("prg_name", prg_name);
    formData.append("prg_acro", prg_acro);
    formData.append("prg_department", prg_department);
    formData.append("prg_department_code", prg_department_code);
    formData.append("prg_status", prg_status);

    fetch("functions/saveCourse.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        showToast(data.message, data.success ? "success" : "error");

        if (data.success) {

            document.getElementById("prg_name_create").value = "";
            document.getElementById("prg_acro_create").value = "";
            document.getElementById("prg_department_create").value = "";
            document.getElementById("prg_department_code_create").value = "";
            document.getElementById("prg_status_create").value = "ACTIVE";

            setTimeout(() => {
                document.getElementById("create-course-modal").classList.remove("show");
                departmentManagement.classList.add("show");
            }, 500);

            
        }

    })
    .catch(err => {
        showToast(err, "error");
    });
}

// bar chart (go)
// document.addEventListener("DOMContentLoaded", loadChart, loadPieChart);
// setInterval(loadChart, 5000);

document.addEventListener("DOMContentLoaded", () => {
    loadBarChart();   
    loadPieChart();   
    loadLineChart();
    loadRiskStudents();
    loadHealthScore();
});

// menu (Upper) 
menuToggle.addEventListener("click", (e) => {
    e.stopPropagation(); 
    profileMenu.hidden = !profileMenu.hidden;
});

document.addEventListener("click", (e) => {
    if (!menuToggle.contains(e.target) && !profileMenu.contains(e.target)) {
        profileMenu.hidden = true;
    }
});

document.addEventListener("click", function (e) {

    if (e.target.classList.contains("edit-program-btn")) {

        const btn = e.target;

        departmentManagement.classList.remove("show");
        departmentManagementModal.classList.add("show");

        document.getElementById("program_id").value = btn.dataset.id;
        document.getElementById("prg_name").value = btn.dataset.name;
        document.getElementById("prg_acro").value = btn.dataset.acro;
        document.getElementById("prg_department").value = btn.dataset.department;
        document.getElementById("prg_department_code").value = btn.dataset.departmentcode;
        document.getElementById("prg_status").value = btn.dataset.status || "ACTIVE";
    }
});


// sidebar items SIDE (active)
menuItems.forEach(item => {
    item.addEventListener('click', () => {

        menuItems.forEach(li => li.classList.remove('active'));

        item.classList.add('active');

    });
});

// sideMenu galaw
sideBar.addEventListener('mouseenter', () => {
      mainContent.classList.add('shifted');
      navBar.classList.add('shifted');
    });

sideBar.addEventListener('mouseleave', () => {
    mainContent.classList.remove('shifted');
    navBar.classList.remove('shifted');
});

adminDashboardBtn.addEventListener("click", () => {
    adminDashboard.style.display = "block";

    adminApproval.style.display = "none";
    adminPreparation.style.display = "none";
    adminAttendance.style.display = "none";

});

adminApprovalBtn.addEventListener("click", () => {

    adminApproval.style.display = "block";

    adminDashboard.style.display = "none";
    adminPreparation.style.display = "none";
    adminAttendance.style.display = "none";
});

adminPreparationBtn.addEventListener("click", () => {

    adminPreparation.style.display = "block";

    adminDashboard.style.display = "none";
    adminApproval.style.display = "none";
    adminAttendance.style.display = "none";

});

adminAttendanceBtn.addEventListener("click", () => {
    adminAttendance.style.display = "block";

    adminPreparation.style.display = "none";
    adminDashboard.style.display = "none";
    adminApproval.style.display = "none";

})

document.getElementById("approvalSearch").addEventListener("keyup", function () {
    clearTimeout(searchTimer);

    let value = this.value;

    searchTimer = setTimeout(() => {

        tableBody.classList.add("fade-out");

        fetch("functions/searchStudent.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "search=" + encodeURIComponent(value)
        })
        .then(res => res.text())
        .then(data => {
            setTimeout(() => {
                tableBody.innerHTML = data;

                tableBody.classList.remove("fade-out");
                tableBody.classList.add("fade-in");

                setTimeout(() => {
                    tableBody.classList.remove("fade-in");
                }, 200);

            }, 200);
        });

    }, 300);
});

// modals
overlay.addEventListener('click', () => {
    overlay.classList.remove('show');

    allStudent.classList.remove("show");
    studentApplicationView.classList.remove("show");
    studentApplicationApprove.classList.remove("show");
    superCreate.classList.remove("show");
    studentApplicationReject.classList.remove("show");
     allSupervisor.classList.remove("show");
      allUnverified.classList.remove("show");
      AssignStudent.classList.remove("show");
      superView.classList.remove("show");
      ojtSetup.classList.remove("show")

      departmentManagement.classList.remove("show")
      departmentManagementModal.classList.remove("show")

      rfidAttendance.classList.remove("show")
      evaluationSettings.classList.remove("show")
      requirementsSetup.classList.remove("show")
      viewAll.classList.remove("show")
      downloadAllStudent.classList.remove("show")
      downloadAllSupervisor.classList.remove("show")
      rfidRegister.classList.remove("show");
});

allStudentBtn.addEventListener("click", () => {
    overlay.classList.add("show");

    allStudent.classList.add("show");
});

allStudentClose.addEventListener("click", () => {
    overlay.classList.remove("show");
    allStudent.classList.remove("show");

});

allUnverifiedBtn.addEventListener("click", () => {
    overlay.classList.add("show");

    allUnverified.classList.add("show");
});

allUnverifiedClose.addEventListener("click", () => {
    overlay.classList.remove("show");
    allUnverified.classList.remove("show");
});

allSupervisorBtn.addEventListener("click", () => {
    overlay.classList.add("show");

    allSupervisor.classList.add("show");
});

allSupervisorClose.addEventListener("click", () => {
    overlay.classList.remove("show");
    allSupervisor.classList.remove("show");
});



document.getElementById("allStudentSearch").addEventListener("keyup", function () {
    clearTimeout(searchTimer);

    let value = this.value;

    searchTimer = setTimeout(() => {

        allStudentBody.classList.add("fade-out");

        fetch("functions/searchAllStudent.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "search=" + encodeURIComponent(value)+"&status=VERIFIED"
        })
        .then(res => res.text())
        .then(data => {
            setTimeout(() => {
                allStudentBody.innerHTML = data;

                allStudentBody.classList.remove("fade-out");
                allStudentBody.classList.add("fade-in");

                setTimeout(() => {
                    allStudentBody.classList.remove("fade-in");
                }, 200);

            }, 200);
        });

    }, 300);
});

document.getElementById("allSupervisorSearch").addEventListener("keyup", function () {
    clearTimeout(searchTimer);

    let value = this.value;

    searchTimer = setTimeout(() => {

        allSupervisorBody.classList.add("fade-out");

        fetch("functions/searchAllSupervisor.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "search=" + encodeURIComponent(value)+"&status=VERIFIED"
        })
        .then(res => res.text())
        .then(data => {
            setTimeout(() => {
                allSupervisorBody.innerHTML = data;

                allSupervisorBody.classList.remove("fade-out");
                allSupervisorBody.classList.add("fade-in");

                setTimeout(() => {
                    allSupervisorBody.classList.remove("fade-in");
                }, 200);

            }, 200);
        });

    }, 300);
});

document.getElementById("allUnverifiedSearch").addEventListener("keyup", function () {
    clearTimeout(searchTimer);

    let value = this.value;

    searchTimer = setTimeout(() => {

        unTableBody.classList.add("fade-out");

        fetch("functions/searchAllStudent.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "search=" + encodeURIComponent(value)+"&status=NOTVERIFIED"
        })
        .then(res => res.text())
        .then(data => {
            setTimeout(() => {
                unTableBody.innerHTML = data;

                unTableBody.classList.remove("fade-out");
                unTableBody.classList.add("fade-in");

                setTimeout(() => {
                    unTableBody.classList.remove("fade-in");
                }, 200);

            }, 200);
        });

    }, 300);
});

// activity log search
document.addEventListener("DOMContentLoaded", function () {

    const searchInput = document.getElementById("activityLogSearch");
    const moduleFilter = document.getElementById("moduleFilter");
    const dateFromInput = document.getElementById("dateFrom");
    const dateToInput = document.getElementById("dateTo");
    const tableBody = document.getElementById("activityLogTableBody");

    if (!searchInput || !moduleFilter || !tableBody || !dateFromInput || !dateToInput ) return;

    let timer;

    function fetchLogs() {

        const search = searchInput.value;
        const module = moduleFilter.value.toUpperCase();
        const dateFrom = dateFromInput.value;
        const dateTo = dateToInput.value;

        fetch("functions/searchActivityLog.php", {
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
             setTimeout(() => {
                tableBody.innerHTML = data;

                tableBody.classList.remove("fade-out");
                tableBody.classList.add("fade-in");

                setTimeout(() => {
                    tableBody.classList.remove("fade-in");
                }, 200);

            }, 200);
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

// supervisor create
superCreateBtn.addEventListener("click", () => {
    overlay.classList.add("show");
    superCreate.classList.add("show");

    initSupervisorForm();
});

superCloseBtn.addEventListener("click", () => {
    overlay.classList.remove("show");
    superCreate.classList.remove("show");
});

supervisorForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(supervisorForm);

    fetch("functions/createSupervisor.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        messageBox.textContent = data.message;

        if (data.status === "success") {
            messageBox.style.color = "green";

            supervisorForm.reset();
            document.getElementById("createSupervisorBtn").disabled = true;

            overlay.classList.remove("show");
            superCreate.classList.remove("show");

            showToast(data.message, data.status ? "success" : "error");

            setTimeout(() => {
    location.reload();
}, 1000);

        } else {
            messageBox.style.color = "red";
        }
    })
    .catch(err => {
        showToast(err, "error");
    });
});

// assign student-supervisor btn
AssignStudentBtn.addEventListener("click", () => {

    isReassignMode = false;

    overlay.classList.add("show");
    AssignStudent.classList.add("show");

    AssignCloseBtn.style.display = "block";

});

AssignCloseBtn.addEventListener("click", () => {
     AssignStudent.classList.remove("show");

    if (isReassignMode) {

        studentApplicationView.classList.add("show");
        isReassignMode = false;

    } else {

        overlay.classList.remove("show");

    }

});

document.getElementById("studentList").addEventListener("click", function (e) {

    const item = e.target.closest(".student-item");

    if (!item) return;

    item.classList.toggle("selected-student");

    let id = item.dataset.id;

    if (selectedStudentIDs.includes(id)) {
        selectedStudentIDs = selectedStudentIDs.filter(i => i !== id);
    } else {
        selectedStudentIDs.push(id);
    }

});

document.getElementById("supervisorList").addEventListener("click", function (e) {

    const item = e.target.closest(".supervisor-item");

    if (!item) return;

    // document.querySelectorAll(".supervisor-item")
    //     .forEach(i => i.classList.remove("selected-supervisor"));

    // item.classList.toggle("selected-supervisor");

    // selectedSupervisorID = item.dataset.id;

    const alreadySelected = item.classList.contains("selected-supervisor");

    document.querySelectorAll(".supervisor-item")
        .forEach(i => i.classList.remove("selected-supervisor"));

    if (!alreadySelected) {
        item.classList.add("selected-supervisor");
        selectedSupervisorID = item.dataset.id;
    } else {
    
        selectedSupervisorID = null;
    }

});

// search assigns
document.getElementById("studentAssignSearch").addEventListener("keyup", function () {
    clearTimeout(assignSearchTimer);

    let value = this.value;

    assignSearchTimer = setTimeout(() => {

        studentList.classList.add("fade-out");

        fetch("functions/searchAssignStudent.php", {
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

document.getElementById("supervisorAssignSearch").addEventListener("keyup", function () {
    clearTimeout(assignSearchTimer);

    let value = this.value;

    assignSearchTimer = setTimeout(() => {

        supervisorList.classList.add("fade-out");

        fetch("functions/searchAssignSupervisor.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "search=" + encodeURIComponent(value)
        })
        .then(res => res.text())
        .then(data => {

            setTimeout(() => {
                supervisorList.innerHTML = data;

                supervisorList.classList.remove("fade-out");
                supervisorList.classList.add("fade-in");

                setTimeout(() => {
                    supervisorList.classList.remove("fade-in");
                }, 200);

            }, 200);

        });

    }, 300);
});

// assign submit

document.getElementById("assign-btn").addEventListener("click", function () {

    if (selectedStudentIDs.length === 0) {
        showToast("Please select at least one student.", "error");
        return;
    }

    if (!selectedSupervisorID) {
        showToast("Please select a supervisor.", "error");
        return;
    }

    const formData = new FormData();

    formData.append("superID", selectedSupervisorID);

    selectedStudentIDs.forEach(studentID => {
        formData.append("studentIDs[]", studentID);
    });

    fetch("functions/assignStudentSupervisor.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        if (data.status === "success") {
             showToast(data.message, "success");
             setTimeout(() => {
                location.reload();
             }, 500);
            
        }
    })
    .catch(err => {
        showToast("Something went wrong", "error");
    });

});

document.addEventListener("DOMContentLoaded", function () {

    const searchInput = document.getElementById("studentAttendanceSearch");
    const statusFilter = document.getElementById("attendanceStatusFilter");
    const courseFilter = document.getElementById("attendanceCourseFilter");
    const attendanceDateFrom = document.getElementById("attendanceDateFrom");
    const attendanceDateTo = document.getElementById("attendanceDateTo");
    const tableBody = document.getElementById("studentAttendanceBody");

    if (
        !searchInput ||
        !statusFilter ||
        !attendanceDateFrom ||
        !attendanceDateTo ||
        !courseFilter ||
        !tableBody
    ) return;

    let timer;

    function fetchAdminAttendance() {

        const search = searchInput.value;
        const status = statusFilter.value;
        const dateFrom = attendanceDateFrom.value;
        const dateTo = attendanceDateTo.value;
        const course = courseFilter.value;

        fetch("functions/searchAdminAttendance.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body:
                "search=" + encodeURIComponent(search) +
                "&status=" + encodeURIComponent(status) +
                "&course=" + encodeURIComponent(course) +
                "&dateFromAttendance=" + encodeURIComponent(dateFrom) +
                "&dateToAttendance=" + encodeURIComponent(dateTo)
        })
        .then(response => response.text())
        .then(data => {
            tableBody.innerHTML = data;
        })
        .catch(error => {
            console.error("Admin attendance fetch error:", error);
        });
    }

    searchInput.addEventListener("keyup", function () {
        clearTimeout(timer);
        timer = setTimeout(fetchAdminAttendance, 300);
    });

    statusFilter.addEventListener("change", fetchAdminAttendance);
    attendanceDateFrom.addEventListener("change", fetchAdminAttendance);
    attendanceDateTo.addEventListener("change", fetchAdminAttendance);
    courseFilter.addEventListener("change", fetchAdminAttendance);

    fetchAdminAttendance();
});


// system config buttons

ojtSetupBtn.addEventListener("click", () => {
    overlay.classList.add("show");
    ojtSetup.classList.add("show");

    loadOJTSettings();
});
closeOjtSetupBtn.addEventListener("click", () => {
    overlay.classList.remove("show");
    ojtSetup.classList.remove("show");
});

departmentManagementBtn.addEventListener("click", () => {
    overlay.classList.add("show");
    departmentManagement.classList.add("show");
});
closeDeparmentManagementBtn.addEventListener("click", () => {
     overlay.classList.remove("show");
    departmentManagement.classList.remove("show");
});

closeDeparmentManagementModalBtn.addEventListener("click", () => {
    departmentManagementModal.classList.remove("show");
    departmentManagement.classList.add("show");
});

rfidAttendanceBtn.addEventListener("click", () => {
    overlay.classList.add("show");
    rfidAttendance.classList.add("show");

    loadRfidSettings();
});
closeRfidAttendanceBtn.addEventListener("click", () => {
     overlay.classList.remove("show");
    rfidAttendance.classList.remove("show");
});

evaluationSettingsBtn.addEventListener("click", () => {
    overlay.classList.add("show");
    evaluationSettings.classList.add("show");
});
closeEvaluationSettingsBtn.addEventListener("click", () => {
     overlay.classList.remove("show");
    evaluationSettings.classList.remove("show");
});

requirementsSetupBtn.addEventListener("click", () => {
    overlay.classList.add("show");
    requirementsSetup.classList.add("show");
});
closeRequirementsSetupBtn.addEventListener("click", () => {
    overlay.classList.remove("show");
    requirementsSetup.classList.remove("show");
}); 

viewAllBtn.addEventListener("click", () => {
    overlay.classList.add("show");
    viewAll.classList.add("show");
    loadAllRiskStudents();
}); 

closeViewAllBtn.addEventListener("click", () => {
    overlay.classList.remove("show");
    viewAll.classList.remove("show");
}); 

// downloads laayout

downloadAllStudentBtn.addEventListener("click", () => {
    allStudent.classList.remove("show");
    downloadAllStudent.classList.add("show");
});

closeDownloadAllStudent.addEventListener("click", () => {
    downloadAllStudent.classList.remove("show");
    allStudent.classList.add("show");
});

downloadAllSupervisorBtn.addEventListener("click", () => {
    allSupervisor.classList.remove("show");
    downloadAllSupervisor.classList.add("show");
});

closeDownloadAllSupervisor.addEventListener("click", () => {
    downloadAllSupervisor.classList.remove("show");
    allSupervisor.classList.add("show");
});

// reigister rfid


closeRfidRegister.addEventListener("click", () => {
    rfidRegister.classList.remove("show");
    allStudent.classList.add("show");
});









