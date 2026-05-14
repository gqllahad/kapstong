const sideBar = document.querySelector('.sidebar');
const mainContent = document.querySelector('.content');

const studentID = document.body.dataset.studentId;

// menu profile
const menuToggle = document.getElementById("menuToggle");
const profileMenu = document.getElementById("profileMenu");

const navBar = document.querySelector('.navbar');

const menuItems = document.querySelectorAll('.menu li');

const overlay = document.getElementById('overlay');

// profile modal
const openProfileBtn = document.getElementById('openProfileBtn');
const openProfilePendingBtn = document.getElementById('openProfilePendingBtn');

const profilePendingModal = document.getElementById('profilePendingModal');
const profileModal = document.getElementById('profileModal');

const openProfileVerifiedBtn = document.getElementById('openProfileVerifiedBtn');
const profileVerifiedModal = document.getElementById('profileVerifiedModal');

const profileUpload = document.getElementById('profileUpload');
const profilePreview = document.getElementById('profilePreview');



const profileContainer = document.querySelector('.profile-picture-container');
// const profileInput = document.getElementById('profileInput');
// const profilePic = document.getElementById('profilePic');
const profileInput = document.getElementById('profileInput');
const profileForm = document.getElementById('profileUpload');

const closeModalProfileBtn = document.getElementById('closeProfileModal');
const closeModalProfilePendingBtn = document.getElementById('closeProfilePendingModal');

// Account Settings
const openAccountSettings = document.getElementById('openAccountSettingsBtn');
const accountSettings = document.getElementById('account-settings');

const changePasswordModal = document.getElementById('change-password-modal');
const forgotPinModal = document.getElementById('forgot-pin-modal');


const closeModalAccountBtn = document.getElementById('closeAccountModal');


// layouts feautres verified student
const studentDashboardBtn = document.getElementById("student-dashboard-btn");
const studentTasksBtn = document.getElementById("student-tasks-btn");
const studentDocumentsBtn = document.getElementById("student-documents-btn");

const studentDashboard = document.getElementById("student-dashboard");
const studentTasks = document.getElementById("student-tasks");
const studentDocuments = document.getElementById("student-documents");

// unverified Students

// layouts 
const unverifiedStudentDashboardButton = document.getElementById("unverified-student-dashboard-button");
const unverifiedStudentDocumentsButton = document.getElementById("unverified-student-documents-button");

const unverifiedStudentDashboard = document.getElementById("unverified-student-dashboard");
const unverifiedStudentDocuments = document.getElementById("unverified-student-documents");

// uploads/ preview
const uploadBtnNow = document.getElementById('btn-upload-now');
const cancelUploadNow = document.getElementById('cancelUploadModal');

const changeFilesBtn = document.getElementById('btn-change-files');
const removeFilesBtn = document.getElementById('btn-remove-files');

const unverifiedPreview = document.getElementById("closeUnverifiedImagePreview");
const verifiedPreview = document.getElementById("closeImagePreview");

const uploadModal = document.getElementById('uploadModal');
const closeModalBtn = document.getElementById('closeModal');
const cancelModalBtn = document.getElementById('cancelModal');

const previewFilesBtn = document.getElementById('btn-preview');
const previewFilesModal = document.getElementById('previewFilesModal');
const closePreviewModalBtn = document.getElementById('closePreviewModal');

// tasks
const submitTaskModal = document.getElementById("submit-task-modal");
const submitTaskBtn = document.getElementById("submit-task-btn");
const closeSubmitTaskBtn = document.getElementById("closeSubmitModal");

const fileInput = document.getElementById("submission_file");
const filePreviewContainer = document.getElementById("filePreviewContainer");

// edit info
const editInfo = document.getElementById("btn-edit");
const editModal = document.getElementById('editModal');

const editForm = document.querySelector('#editModal form');

const cancelEditModalBtn = document.getElementById('cancelEditModal');
const closeEditModalBtn = document.getElementById('closeEditModal');

// tasks
const viewTaskDetails = document.getElementById("view-task-container");
const closeViewTaskDetails = document.getElementById("closeTaskViewModal");

const modalContainer = document.getElementById("view-task-container");
const statusMessage = document.getElementById("taskStatusMessage");

// verified dashboard cards
const progressCardBtn = document.getElementById("ojt-progress-btn");
const progressCard = document.getElementById("view-progress-chart");
const closeProgressCard = document.getElementById("closeProgressViewModal");

const notificationCardBtn = document.getElementById("notification-btn");
const notificationCard = document.getElementById("notification-container");
const closeNotificationCard = document.getElementById("closeNotificationViewModal");

const attendanceCardBtn = document.getElementById("attendance-btn");
const attendanceCard = document.getElementById("attendance-container");
const closeAttendanceCard = document.getElementById("closeAttendanceViewModal");


// task array
let allTasks = [];

let selectedFiles = [];

// unverified

// functions

// unverified

window.previewImage = function(src) {
    const modal = document.getElementById("imagePreviewModal");
    const img = document.getElementById("previewImg");

    img.src = src;
    modal.style.display = "flex";
}

function previewImage(src) {
    const modal = document.getElementById("imagePreviewModal");
    const img = document.getElementById("previewImg");

    img.src = src;
    modal.style.display = "flex";
}

function previewImageUnverified(src){
    const modal = document.getElementById("imageUnverifiedPreviewModal");
    const img = document.getElementById("previewUnverifiedImg");

    img.src = src;
    modal.style.display = "flex";
}

function closeEditModal() {
    overlay.classList.remove('show');
    editModal.classList.remove('show');

    editForm.reset();
}

// unverified

function handleFilePreview(inputElem, previewElem) {
    inputElem.addEventListener('change', () => {
        const file = inputElem.files[0];
        previewElem.innerHTML = '';

        if (!file) return;
        
        const allowedTypes = ['image/jpeg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            alert('Only JPG or PNG files are allowed!');
            inputElem.value = '';
            return;
        }

        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.classList.add('preview-img');
        previewElem.appendChild(img);
    });
}

// response modal
function showResponseModal(message, type = 'success') {
    const modal = document.getElementById("responseModal");
    const msg = document.getElementById("responseMessage");

    msg.innerText = message;
    
    modal.classList.remove("show");

    void modal.offsetWidth;

    setTimeout(() => {
        modal.classList.add("show");
    }, 10);

    setTimeout(() => {
        closeResponseModal();
    }, 2000);
}

// task modal


function normalize(str) {
    return (str || "").toUpperCase().trim();
}

function getStatusClass(status) {
    switch (normalize(status)) {
        case "NOT STARTED":
            return "not-started";
        case "IN PROGRESS":
            return "in-progress";
        case "SUBMITTED":
            return "submitted";
        case "APPROVED":
            return "approved";
        case "REJECTED":
            return "rejected";
        default:
            return "";
    }
}

function getProgressText(status) {
    switch (normalize(status)) {
        case "NOT STARTED":
            return "0% started";
        case "IN PROGRESS":
            return "Work in progress";
        case "SUBMITTED":
            return "Waiting for approval";
        case "APPROVED":
            return "Completed ✔";
        case "REJECTED":
            return "Needs revision";
        default:
            return "";
    }
}

function getProgress(status) {
     const normalized = normalize(status);

    const map = {
        "NOT STARTED": 0,
        "IN PROGRESS": 30,
        "SUBMITTED": 70,
        "APPROVED": 100,
        "REJECTED": 0
    };

    return map[normalized] ?? 0;
}

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

function openTaskDetails(taskID) {
    overlay.classList.add("show");
    viewTaskDetails.classList.add("show");

    fetch("student_functions/getTaskDetails.php?taskID=" + taskID)
        .then(res => res.json())
        .then(data => {

            document.getElementById("modalTaskTitle").innerText = data.title;
            document.getElementById("modalTaskDesc").innerText = data.description;
            // document.getElementById("modalTaskStatus").innerText = data.status;
            document.getElementById("modalTaskDue").innerText = data.due_date;
            document.getElementById("modalTaskCompleted").innerText =
                data.completed_at ? data.completed_at : "Not completed yet";
            document.getElementById("modalTaskProgress").innerText =
                data.progress + "%";

            const submitBtn = document.getElementById("submitTaskBtn");
            const reSubmitBtn = document.getElementById("reSubmitTaskBtn");
            submitBtn.dataset.taskid = taskID;
            reSubmitBtn.dataset.taskid = taskID;

            const status = (data.status || "").toUpperCase().trim();
            statusMessage.className = "task-status-message";
            const uploadedFilesCard = document.getElementById("uploadedFilesCard");
            const studentUploadedFiles = document.getElementById("studentUploadedFiles");

            studentUploadedFiles.innerHTML = "";

            modalContainer.classList.remove(
                "task-approved",
                "task-rejected",
                "task-submitted",
                "task-progress",
                "task-notstarted"
            );

            if (
                status === "APPROVED" ||
                status === "REJECTED" ||
                status === "SUBMITTED"
            ) {

                uploadedFilesCard.style.display = "block";

                if (data.submission_file && data.submission_file.trim() !== "") {

                    const files = data.submission_file.split(",");

                    files.forEach(file => {

                        const cleanFile = file.trim();

                        studentUploadedFiles.innerHTML += `
                            <div class="doc-card">

                                <button 
                                    class="btn-preview"
                                    onclick="previewImage('../../uploads/student_tasks/${data.studentID}/${cleanFile}')">
                                    👁 View File
                                </button>

                                <span class="status-badge success">
                                    Uploaded
                                </span>

                            </div>
                        `;
                    });

                } else {

                    studentUploadedFiles.innerHTML = `
                        <span class="status-badge missing">
                            No File Uploaded
                        </span>
                    `;
                }

            } else {

                uploadedFilesCard.style.display = "none";
            }

            if (status === "APPROVED") {

                modalContainer.classList.add("task-approved");

                statusMessage.classList.add("task-message-approved");

                statusMessage.innerText =
                    "✅ This task has been approved by your supervisor.";

            } else if (status === "REJECTED") {

                modalContainer.classList.add("task-rejected");
                 statusMessage.classList.add("task-message-rejected");

                statusMessage.innerText =
                    "❌ This task was rejected. Please review the feedback and resubmit.";

            } else if (status === "SUBMITTED") {

                modalContainer.classList.add("task-submitted");
                statusMessage.classList.add("task-message-submitted");

                statusMessage.innerText =
                    "📤 Your task has been submitted and is awaiting supervisor review.";


            } else if (status === "IN PROGRESS") {

                modalContainer.classList.add("task-progress");
                 statusMessage.classList.add("task-message-progress");

                statusMessage.innerText =
                    "⏳ This task is currently in progress. Complete it before the due date.";

            } else {

                modalContainer.classList.add("task-notstarted");
                statusMessage.classList.add("task-message-notstarted");

                statusMessage.innerText =
                    "📝 This task has not been started yet.";
            }

            if (status === "REJECTED" || status === "SUBMITTED") {
                reSubmitBtn.style.display = "inline-block";
                submitBtn.style.display = "none";
            }else if( status === "APPROVED"){
                submitBtn.style.display = "none";
                reSubmitBtn.style.display = "none";
            } else if(status === "NOT STARTED" || status == "IN PROGRESS") {
                submitBtn.style.display = "inline-block";
                reSubmitBtn.style.display = "none";
            }

            const studentNoteSection = document.getElementById("studentNoteSection");
            const supervisorFeedbackSection = document.getElementById("supervisorFeedbackSection");
            const ratingSection = document.getElementById("ratingSection");

             const notesCard = studentNoteSection.closest(".info-card");

            document.getElementById("modalStudentNote").innerText =
                data.student_note ? data.student_note : "No student note provided";

            document.getElementById("modalSupervisorFeedback").innerText =
                data.supervisor_feedback ? data.supervisor_feedback : "No supervisor feedback";

            document.getElementById("modalSupervisorRating").innerText =
                data.rating ? data.rating : "No rating provided";

           if (status === "APPROVED" || status === "REJECTED") {
                studentNoteSection.style.display = "block";
                supervisorFeedbackSection.style.display = "block";
                ratingSection.style.display = "block";
                notesCard.style.display = "block";
            } 
            else if (status === "SUBMITTED" || status === "IN PROGRESS") {
                studentNoteSection.style.display = "block";
                supervisorFeedbackSection.style.display = "none";
                ratingSection.style.display = "none";
                notesCard.style.display = "block";
            } 
            else if (status === "NOT STARTED") {
                studentNoteSection.style.display = "none";
                supervisorFeedbackSection.style.display = "none";
                ratingSection.style.display = "none";
                notesCard.style.display = "none";
            }
        });
}

// handler alerts
function handleAlert(action, id = null) {

    switch(action) {

        case "viewTask":

            if (id){
                notificationCard.classList.remove("show");
                openTaskDetails(id);
            }
            break;

        case "openAttendance":
            openAttendanceModal();
            break;

        case "viewProgress":
            openProgressModal();
            break;
    }
}

function openProgressModal(){
    notificationCard.classList.remove("show");
    progressCard.classList.add("show");

    loadStudentProgressChart(studentID);
}

function  openAttendanceModal(){
    notificationCard.classList.remove("show");
    attendanceCard.classList.add("show");
}

// attendance card
// function loadStudentAttendanceTable(studentID) {

//     fetch("student_functions/getStudentAttendanceTable.php?studentID=" + studentID, {
//         credentials: "include"
//     })
//     .then(res => res.text())
//     .then(data => {
//         document.getElementById("attendanceReportBody").innerHTML = data;
//     })
//     .catch(err => console.error("Attendance load error:", err));
// }

function renderTasks(filter) {
    const container = document.getElementById("taskList");
    container.innerHTML = "";

    let filtered = allTasks;

    if (filter !== "All") {
        filtered = allTasks.filter(task =>
            normalize(task.status) === normalize(filter)
        );
    }

    if (filtered.length === 0) {

        let message = "No tasks found";

        switch (normalize(filter)) {
            case "IN PROGRESS":
                message = "No In-Progress tasks yet.";
                break;

            case "APPROVED":
                message = "No Approved tasks yet.";
                break;

            case "REJECTED":
                message = "No Rejected tasks yet.";
                break;

            case "NOT STARTED":
                message = "No Not Started tasks yet.";
                break;

            default:
                message = "No Tasks Available.";
        }

        container.innerHTML = `
            <div class="empty-state">
                <p>${message}</p>
            </div>
        `;
        return;
    }

    filtered.forEach(task => {
         const status = normalize(task.status);
        const progress = getProgress(task.status);
        task.statusClass = getStatusClass(task.status);

        let dueDateDisplay = `Due: ${formatDate(task.due_date)}`;

        if (status === "APPROVED") {
            dueDateDisplay = `Completed: ${formatDate(task.completed_at)}`;
        }   

        container.innerHTML += `
            <div class="task-card" data-taskid="${task.taskID}">

                <div class="task-top">
                    <h3>
                        <i class="bi bi-list-check task-icon"></i>
                        ${task.title}
                    </h3>

                    <span class="task-status ${task.statusClass}">
                        ${task.status}
                    </span>
                </div>

                <small class="task-due-date">
                    ${dueDateDisplay}
                </small>

                <div class="task-body">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width:${progress}%"></div>
                    </div>

                    <small>${progress}% Complete</small>
                </div>

            </div>
        `;
    });
}

function loadTasks() {
    fetch("student_functions/getStudentTasks.php")
        .then(res => res.json())
        .then(tasks => {

            allTasks = tasks; 
            renderTasks("All");

//             const container = document.getElementById("taskList");
//             container.innerHTML = "";

//             tasks.forEach(task => {
                
//                 const progress = getProgress(task.status);

//                 task.progressText = getProgressText(task.status);
//                 task.statusClass = getStatusClass(task.status);

//                 container.innerHTML += `
//                     <div class="task-card" data-taskid="${task.taskID}"
//      onclick="openTaskDetails(${task.taskID})">

//     <div class="task-top">
//         <h3> <i class="bi bi-list-check task-icon"></i>
//         ${task.title}</h3>
//         <span class="task-status ${task.statusClass}">
//             ${task.status}
//         </span>
//     </div>

//     <small class="task-due-date">
//     Due: ${formatDate(task.due_date)}
// </small>

//     <div class="task-body">

//         <div class="progress-bar">
//             <div class="progress-fill" style="width:${progress}%%"></div>
//         </div>

//         <small>${progress}% Complete</small>

//     </div>

// </div>
//                 `;
            });
        // });
}


// progress Chart View modal
function loadStudentProgressChart(studentID) {

    fetch("student_functions/getStudentProgress.php?studentID=" + studentID, {
        credentials: "include"
    })
    .then(res => res.json())
    .then(data => {
        
        const ctx = document.getElementById('progressChart');

        if (!ctx) return;

        if (window.progressChartInstance) {
            window.progressChartInstance.destroy();
        }

        const completed = Number(data.completed ?? 0);
        const required = Number(data.required ?? 500);
        const remaining = Math.max(required - completed, 0);

        const percent = required > 0
            ? ((completed / required) * 100).toFixed(1)
            : 0;

        document.getElementById("progressPercent").innerText =
        `${percent}% Completed`;

        window.progressChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Remaining'],
                datasets: [{
                    data: [completed, remaining],
                    backgroundColor: ['#22c55e', '#e5e7eb'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                cutout: '75%',
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.raw} hrs`;
                            }
                        }
                    }
                }
            },
            plugins: [{
                id: 'centerText',
                beforeDraw(chart) {
                    const { width } = chart;
                    const { ctx } = chart;
                    ctx.restore();

                    const fontSize = (width / 10).toFixed(2);
                    ctx.font = fontSize + "px sans-serif";
                    ctx.textBaseline = "middle";

                    const text = percent + "%";
                    const textX = Math.round((width - ctx.measureText(text).width) / 2);
                    const textY = chart.height / 2;

                    ctx.fillStyle = "#111827";
                    ctx.fillText(text, textX, textY);
                    ctx.save();
                }
            }]
        });

    })
    .catch(err => console.error("Progress chart error:", err));
}


function closeResponseModal() {
    const modal = document.getElementById("responseModal");
    modal.classList.remove("show");
}

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

function toggleSection(header) {
    const card = header.parentElement;

    document.querySelectorAll(".info-card").forEach(c => {
        if (c !== card) c.classList.remove("active");
    });

    card.classList.toggle("active");
};

function renderFilePreview() {
    filePreviewContainer.innerHTML = "";

    selectedFiles.forEach((file, index) => {
        filePreviewContainer.innerHTML += `
            <div class="file-chip">
                <span>${file.name}</span>
                <button type="button" onclick="removeFile(${index})">
                    &times;
                </button>
            </div>
        `;
    });
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    renderFilePreview();
}

// Chartss
function loadPieChart() {

    fetch(`../../php/student/student_functions/getStudentTaskChart.php?studentID=${studentID}`)

    .then(res => res.json())

    .then(data => {

        const ctx = document.getElementById('pieChart');

        if (!data.values || data.values.length === 0) {

            data.labels = ["No Tasks Yet"];
            data.values = [1];

            data.empty = true;
        }

        const colors = data.labels.map(status => {

            if (status === "APPROVED") {
                return "#22c55e";
            }

            if (status === "SUBMITTED") {
                return "#3b82f6";
            }

            if (status === "IN PROGRESS") {
                return "#facc15";
            }

            if (status === "REJECTED") {
                return "#ef4444";
            }

            if (status === "NOT STARTED") {
                return "#9ca3af";
            }

            return "#d1d5db";
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
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: "#ffffff"
                }]
            },

            options: {

                responsive: true,

                cutout: '70%',

                plugins: {

                    legend: {
                        position: 'bottom',

                        labels: {
                            padding: 18,
                            usePointStyle: true,
                            font: {
                                size: 11
                            }
                        }
                    },

                    tooltip: {
                        enabled: !data.empty
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
                "No task records available yet.";

            ctx.parentNode.appendChild(message);
        }

    })

    .catch(err => console.error("Pie chart error:", err));
}

function loadLineChart() {
    fetch(`../../php/student/student_functions/getStudentAttendanceTrend.php?studentID=${studentID}`)
        .then(res => res.json())
        .then(data => {

            const ctx = document.getElementById('lineChart');

            if (!data.values || data.values.length === 0) {

                data.labels = ["No Attendance Yet"];
                data.values = [1];

                data.empty = true;
            }   

            if (window.lineChartInstance) {
                window.lineChartInstance.destroy();
            }

            window.lineChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                                    label: 'Attendance Trend',
                                    data: data.values,
                                    fill: true,
                                    tension: 0.4,
                                    borderWidth: 3,
                                    pointRadius: 5,
                                    pointHoverRadius: 7,
                                    pointBorderWidth: 2,
                                    backgroundColor: 'rgba(59, 130, 246, 0.10)',
                                    pointBackgroundColor: data.values.map(value => {

                                        if (value == 1) {
                                            return '#22c55e';
                                        }

                                        if (value == 0.5) {
                                            return '#facc15'; 
                                        }

                                        return '#ef4444'; 
                                    }),

                                    pointBorderColor: '#ffffff',

                                    segment: {
                                        borderColor: ctx => {

                                            const value = ctx.p1.parsed.y;

                                            if (value == 1) {
                                                return '#22c55e'; 
                                            }

                                            if (value == 0.5) {
                                                return '#facc15';
                                            }

                                            return '#ef4444'; 
                                        }
                                    }
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
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 1
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
                "No Attendance records available yet.";

            ctx.parentNode.appendChild(message);
        }

        })
        .catch(err => console.error("Attendance trend error:", err));
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

// Account Settings 

if(openAccountSettings){
openAccountSettings.addEventListener("click", () => {
    overlay.classList.add("show");
    accountSettings.classList.add("show");
});

closeModalAccountBtn.addEventListener('click', () => {
    overlay.classList.remove('show');
    accountSettings.classList.remove('show');
});

document.getElementById('openChangePassword').addEventListener('click', () => {
    accountSettings.classList.remove('show');
    changePasswordModal.classList.add('show');
});

document.getElementById('openForgotPIN').addEventListener('click', () => {
    accountSettings.classList.remove('show');
    forgotPinModal.classList.add('show');
});

document.getElementById('backToAccountSettings1').addEventListener('click', () => {
    changePasswordModal.classList.remove('show');
    accountSettings.classList.add('show');
});

document.getElementById('backToAccountSettings2').addEventListener('click', () => {
    forgotPinModal.classList.remove('show');
    accountSettings.classList.add('show');
});

document.getElementById('closeAccountModal').addEventListener('click', () => {
    accountSettings.classList.remove('show');
});
}

// sideMenu galaw
sideBar.addEventListener('mouseenter', () => {
    mainContent.classList.add('shifted');
    navBar.classList.add('shifted');
});

sideBar.addEventListener('mouseleave', () => {
    mainContent.classList.remove('shifted');
    navBar.classList.remove('shifted');
});

// unverified features

// layouts

if(unverifiedStudentDashboardButton){
    unverifiedStudentDashboardButton.addEventListener("click", () => {

    unverifiedStudentDashboard.style.display = "block";
    unverifiedStudentDocuments.style.display = "none";

    });
}

if(unverifiedStudentDocumentsButton){
    unverifiedStudentDocumentsButton.addEventListener("click", () => {

    unverifiedStudentDashboard.style.display = "none";
    unverifiedStudentDocuments.style.display = "block";

    });
}

// dashboard features
// edit info 

if(editInfo){

editInfo.addEventListener('click', () => {
    overlay.classList.add('show');
    editModal.classList.add('show');

     initialFormData = new FormData(editForm);

});

editForm.addEventListener('submit', function (e) {
    let currentFormData = new FormData(editForm);

    let changed = false;

    for (let [key, value] of currentFormData.entries()) {
        if (value !== initialFormData.get(key)) {
            changed = true;
            break;
        }
    }

    if (!changed) {
        alert("No changes were made.");
        e.preventDefault();
        return;
    }

    const confirmSave = confirm("Are you sure you want to save changes?");
    if (!confirmSave) {
        e.preventDefault();
    }
});

document.addEventListener("DOMContentLoaded", function() {
    const courseSelect = document.getElementById('edit_course');

    if (!courseSelect) return;

    const currentCourse = courseSelect.dataset.current;

    fetch('../../getCourses.php')
        .then(res => res.json())
        .then(data => {

            if (data.acro && data.values) {
                data.acro.forEach((acro, index) => {
                    const option = document.createElement('option');
                    option.value = acro;
                    option.textContent = data.values[index];

                    if (acro === currentCourse) {
                        option.selected = true;
                    }

                    courseSelect.appendChild(option);
                });
            }
        })
        .catch(err => console.error("Error loading courses:", err));
});

closeEditModalBtn.addEventListener('click', closeEditModal);
cancelEditModalBtn.addEventListener('click', closeEditModal);

// edit info
previewFilesBtn?.addEventListener('click', () => {
    overlay.classList.add('show');
    previewFilesModal.classList.add('show');
});

closePreviewModalBtn?.addEventListener('click', () => {
    overlay.classList.remove('show');
    previewFilesModal.classList.remove('show');
});

if (uploadBtnNow) {
    uploadBtnNow.addEventListener('click', () => {
        overlay.classList.add('show');
        uploadModal.classList.add('show');
    });
}

if (cancelUploadNow) {
    cancelUploadNow.addEventListener('click', () => {
        overlay.classList.remove('show');
        uploadModal.classList.remove('show');
    });
}

changeFilesBtn?.addEventListener('click', () => {
    overlay.classList.add('show');
    uploadModal.classList.add('show'); 
    previewFilesModal.classList.remove('show'); 
});

removeFilesBtn?.addEventListener('click', () => {
    const confirmDelete = confirm("Are you sure you want to remove your uploaded documents?");
    if (!confirmDelete) return;

    fetch("delete_documents_action.php", { method: "POST" })
        .then(res => res.text())
        .then(() => window.location.reload())
        .catch(err => console.error(err));
});

closeModalBtn.addEventListener('click', () => {
    overlay.classList.remove('show');
    uploadModal.classList.remove('show');
});
}

if(unverifiedPreview){
    unverifiedPreview.addEventListener("click", () => {
    document.getElementById("imageUnverifiedPreviewModal").style.display = "none";
});
}

// profiel modal

// unverified profile

if (openProfilePendingBtn && profilePendingModal) {
    openProfilePendingBtn.addEventListener("click", () => {
        overlay.classList.add("show");
        profilePendingModal.classList.add("show");
    });
}

if (closeModalProfilePendingBtn && profilePendingModal) {
    closeModalProfilePendingBtn.addEventListener('click', () => {
        overlay.classList.remove('show');
        profilePendingModal.classList.remove('show');
    });
}

// unverified profile

if(openProfileBtn){
openProfileBtn.addEventListener("click", () => {

    overlay.classList.add("show");
    profileModal.classList.add("show");

});

closeModalProfileBtn.addEventListener('click', () => {
    overlay.classList.remove('show');
    profileModal.classList.remove('show');
});

// profileContainer.addEventListener('click', () => {
//     profileInput.click();
// });
}

// not included
// profileInput.addEventListener('change', function (event) {
//     const file = event.target.files[0];

//     if (file) {
//         // Check file type (MIME)
//         if (file.type !== "image/jpeg") {
//             alert("Only JPG images are allowed.");
//             profileInput.value = ""; // reset input
//             return;
//         }

//         const reader = new FileReader();

//         reader.onload = function (e) {
//             profilePic.src = e.target.result;
//         };

//         reader.readAsDataURL(file);
//     }
// });

// function uploadProfilePic() {
//     const input = document.getElementById('profileInput');
//     if (input.files.length === 0) return;

//     const file = input.files[0];

//     // Accept JPG and PNG
//     if (!file.type.match('image/jpeg') && !file.type.match('image/png')) {
//         alert("Only JPG or PNG files are allowed!");
//         input.value = "";
//         return;
//     }

//     const formData = new FormData();
//     formData.append('profileImage', file);

//     fetch('../php/student/student_functions/profile_upload.php', {
//         method: 'POST',
//         body: formData
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.success) {
//             document.getElementById('profilePic').src = data.url + '?' + new Date().getTime();
//         } else {
//             alert(data.message);
//             input.value = ""; // reset input on error
//         }
//     })
//     .catch(err => {
//         console.error(err);
//         alert("Upload failed!");
//         input.value = ""; // reset input on error
//     });
// }

if(profileInput){
profileInput.addEventListener('change', () => {
    if (profileInput.files.length > 0) {
        profileForm.submit();
    }
});
}


// verified students

if(studentDashboardBtn){
    studentDashboardBtn.addEventListener("click", () => {

        studentTasks.style.display = "none";
        studentDocuments.style.display = "none";

        studentDashboard.style.display = "block";

    });
}

if(studentTasksBtn){
    studentTasksBtn.addEventListener("click", () => {

        studentDashboard.style.display = "none";
        studentDocuments.style.display = "none";

        studentTasks.style.display = "block";
        
    });

    document.getElementById("taskList").addEventListener("click", function (e) {
        const card = e.target.closest(".task-card");

        if (!card) return;

        const taskID = card.dataset.taskid;

        openTaskDetails(taskID);
    });

    document.getElementById("submitTaskBtn").addEventListener("click", function () {
        const taskID = this.dataset.taskid;
        
        viewTaskDetails.classList.remove("show");
        submitTaskModal.classList.add("show");

        document.getElementById("submitTaskID").value = taskID;
       
    });

     document.getElementById("reSubmitTaskBtn").addEventListener("click", function () {
        const taskID = this.dataset.taskid;
        
        viewTaskDetails.classList.remove("show");
        submitTaskModal.classList.add("show");

        document.getElementById("submitTaskID").value = taskID;
       
    });

    document.getElementById("submitTaskForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    selectedFiles.forEach(file => {
        formData.append("submission_file[]", file);
    });

    fetch("student_functions/submitTask.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        console.log(data);

        if (data.status === "success") {
            alert(data.message);

            selectedFiles = [];
            renderFilePreview();
            this.reset();

            submitTaskModal.classList.remove("show");
            viewTaskDetails.classList.remove("show");
            overlay.classList.remove("show");

            loadTasks();
        } else {
            alert(data.message);
        }
    });
});

    // document.getElementById("submitTaskForm").addEventListener("submit", function (e) {
    //     e.preventDefault();

    //     const formData = new FormData(this);

    //     fetch("student_functions/submitTask.php", {
    //         method: "POST",
    //         body: formData
    //     })
    //     .then(res => res.json())
    //     .then(data => {
    //         console.log(data);
    //     });
    // });

    closeSubmitTaskBtn.addEventListener("click", () => {
        submitTaskModal.classList.remove("show");
         viewTaskDetails.classList.add("show");
        

    });

    fileInput.addEventListener("change", function () {
    const newFiles = Array.from(this.files);

    newFiles.forEach(file => {
        selectedFiles.push(file);
    });

    renderFilePreview();

    fileInput.value = "";
});
}

if(studentDocumentsBtn){
    studentDocumentsBtn.addEventListener("click", () => {

        studentDashboard.style.display = "none";
        studentTasks.style.display = "none";

        studentDocuments.style.display = "block";
        
    });
}

if(verifiedPreview){
    verifiedPreview.addEventListener("click", () => {
        document.getElementById("imagePreviewModal").style.display = "none";
    });
}

if(viewTaskDetails){
closeViewTaskDetails.addEventListener("click", () => {
    overlay.classList.remove("show");
    viewTaskDetails.classList.remove("show");
});
}

if(overlay){
overlay.addEventListener('click', () => {
    overlay.classList.remove('show');

    uploadModal?.classList.remove('show');
    previewFilesModal?.classList.remove('show');
    editModal?.classList.remove('show');
    profileModal?.classList.remove('show');

    accountSettings?.classList.remove("show");
    changePasswordModal?.classList.remove('show');
    forgotPinModal?.classList.remove('show');

    submitTaskModal?.classList.remove("show");
    viewTaskDetails?.classList.remove("show");
    progressCard?.classList.remove("show");
    notificationCard?.classList.remove("show");
    attendanceCard?.classList.remove("show");

    if (editForm) editForm.reset();
});
};

if(unverifiedStudentDashboard){
    handleFilePreview(document.getElementById('idUpload'), document.getElementById('idPreview'));
    handleFilePreview(document.getElementById('regFormUpload'), document.getElementById('regPreview'));
}


// verified students
if(studentDashboardBtn){
window.addEventListener("DOMContentLoaded", () => {
    loadLineChart();
    loadPieChart();
});

document.addEventListener("DOMContentLoaded", () => {
    const bars = document.querySelectorAll(".progress-bar-fill");
    bars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = "0%";
        setTimeout(() => {
            bar.style.width = width;
        }, 100);
    });
});
};

if(studentTasksBtn){
    window.addEventListener("DOMContentLoaded", () => {
        loadTasks();
    });

    document.querySelectorAll(".task-filters button").forEach(btn => {
    btn.addEventListener("click", function () {

        document.querySelectorAll(".task-filters button")
            .forEach(b => b.classList.remove("active"));

        this.classList.add("active");

        renderTasks(this.dataset.filter);
    });
});
}


if(progressCardBtn){
    progressCardBtn.addEventListener("click", () => {
        progressCard.classList.add("show");
        overlay.classList.add("show");

        loadStudentProgressChart(studentID);
    });

    closeProgressCard.addEventListener("click", () => {
        progressCard.classList.remove("show");
        overlay.classList.remove("show");
    });
}

if(notificationCardBtn){
    notificationCardBtn.addEventListener("click", () => {
        notificationCard.classList.add("show");
        overlay.classList.add("show");
    });

    closeNotificationCard.addEventListener("click", () => {
        notificationCard.classList.remove("show");
        overlay.classList.remove("show");
    });

}

if(attendanceCardBtn){
    attendanceCardBtn.addEventListener("click", () => {
        attendanceCard.classList.add("show");
        overlay.classList.add("show");

        // loadStudentAttendanceTable(studentID);
    })

    closeAttendanceCard.addEventListener("click", () => {
        attendanceCard.classList.remove("show");
        overlay.classList.remove("show");
    })

    document.addEventListener("DOMContentLoaded", function () {

    const moduleFilter = document.getElementById("moduleFilter");
    const tableBody = document.getElementById("attendanceReportBody");

    if (!moduleFilter || !tableBody) return;

    function fetchLogs() {

        const category = moduleFilter.value;

        fetch("student_functions/searchStudentAttendanceTable.php", {
            method: "POST",
            credentials: "include",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "category=" + encodeURIComponent(category)
        })
        .then(res => res.text())
        .then(data => {
            tableBody.innerHTML = data;
        })
        .catch(err => console.error("Fetch error:", err));
    }

    moduleFilter.addEventListener("change", fetchLogs);

    fetchLogs();
});
}


