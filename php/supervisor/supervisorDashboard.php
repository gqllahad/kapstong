<?php

session_start();
require_once("../kapstongConnection.php");
require_once("../functions.php");
require_once("../sessionTimeout.php");

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

if ($_SESSION['role'] !== "supervisor") {
    header("Location: ../trackerMain.php");
    exit();
}

$userID = $_SESSION['user_id'];


$stmtForce = $conn->prepare("SELECT mustChangePassword FROM users WHERE userID = ?");
$stmtForce->bind_param("i", $userID);
$stmtForce->execute();
$resultForce = $stmtForce->get_result();
$rowForce = $resultForce->fetch_assoc();

$forceChange = $rowForce['mustChangePassword'];

$superID = getSupervisorIDByUserID($conn, $userID);

$supervisorName = $_SESSION['name'] ?? 'Supervisor';

$nameParts = explode(' ', trim($supervisorName));

$initial = '';

foreach ($nameParts as $part) {

    $initial .= strtoupper(substr($part, 0, 1));

    if (strlen($initial) >= 2) {
        break;
    }
}

?>


<!DOCTYPE html>
<html lang="en">


<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Supervisor Dashboard</title>

    <link rel="icon" type="image/png" href="../../kapstongImage/logo.jpg">
    <link rel="stylesheet" href="../../css/supervisor/supervisorDashboard2.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">


</head>


<body>

    <?php if (isset($_SESSION['success'])): ?>
        <script>
            window.addEventListener("DOMContentLoaded", function() {
                showToast("<?= $_SESSION['success'] ?>", "success");
            });
        </script>
    <?php unset($_SESSION['success']);
    endif; ?>


    <?php if (isset($_SESSION['error'])): ?>
        <script>
            window.addEventListener("DOMContentLoaded", function() {
                showToast("<?= $_SESSION['error'] ?>", "error");
            });
        </script>
    <?php unset($_SESSION['error']);
    endif; ?>

    <!-- navbar -->
    <!-- <header class="navbar">
        <div class="header-title">
            <img src="../../kapstongImage/download (1).jpg" class="logo-img" style="border-radius: 50%;">
            <h1>Supervisor Dashboard</h1>
        </div>

        <button id="menuToggle">☰</button>
        <nav class="profile-menu" id="profileMenu" hidden>
            <a id="openProfileBtn">Profile</a>
            <a id="openAccountSettingsBtn">Account Setting</a>
            <hr style="width: 75%; text-align: left;">
            <a href="../logoutPhase.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </nav>
    </header> -->

    <div id="forcePasswordOverlay" style="display:none;">
        <div class="force-box">
            <div class="force-modal-header">
                <h2><i class="bi bi-exclamation-triangle"></i> Change Password Required</h2>
                <p>You must change your password before continuing.</p>
                <a href="../logoutPhase.php" class="force-modal-close-profile">&times;</a>
            </div>

            <form action="functions/settings.php" method="POST">
                <input type="hidden" name="userID" value="<?= $_SESSION['user_id']; ?>">

                <input type="password" name="newPassword" placeholder="New Password" required>
                <input type="password" name="confirmPassword" placeholder="Confirm Password" required>

                <button type="submit" name="forceChangePasswordSupervisor">
                    Update Password
                </button>
            </form>
        </div>
    </div>


    <div class="layout">
        <!-- SIDEBAR -->
        <aside class="sidebar">

            <ul class="menu">
                <li><button id="supervisor-dashboard-btn"><i class="bi bi-house"></i> Home </button></li>
                <li><button id="supervisor-oversight-btn"><i class="bi bi-check2-square"></i> Task Reviews</button></li>
                <li><button id="supervisor-students-btn"><i class="bi bi-people"></i>Students</button></li>
                <li><button id="supervisor-attendance-btn"><i class="bi bi-calendar-check"></i> Attendance Log</button></li>
                <li><button id="supervisor-evaluation-btn"><i class="bi bi-clipboard-data"></i> Reports / Evaluation</button></li>
                <li><button id="supervisor-activity-btn"> <i class="bi bi-activity"></i> Activity Log</button></li>
            </ul>

            <div class="sidebar-profile">

                <div class="profile-left">
                    <div class="profile-avatar">
                        <?= $initial ?>
                    </div>
                    <div class="profile-info">
                        <span class="profile-name"><?= htmlspecialchars($supervisorName) ?></span>
                        <small>Supervisor</small>
                    </div>
                </div>
                <button id="menuToggle" class="profile-trigger">
                    ☰
                </button>

                <nav class="profile-menu" id="profileMenu" hidden>
                    <a id="openProfileBtn">
                        <i class="bi bi-person"></i>
                        Profile
                    </a>
                    <a id="openAccountSettingsBtn">
                        <i class="bi bi-gear"></i>
                        Account Settings
                    </a>
                    <hr>
                    <a href="../logoutPhase.php">
                        <i class="bi bi-box-arrow-right"></i>
                        Logout
                    </a>
                </nav>
            </div>
        </aside>

        <!-- CONTENT -->
        <main class="content">

            <div id="overlay" class="overlay"></div>
            <div id="toast"></div>

            <!-- modals -->

            <!-- Account settings modal (SUPERVISOR) -->
            <div class="account-settings-modal" id="account-settings-supervisor">
                <div class="modal-header">
                    <h3>Account Settings</h3>
                    <button id="closeAccountModalSupervisor" class="modal-close-profile">&times;</button>
                </div>

                <div class="form-section-account">
                    <button class="account-btn" id="openChangePasswordSupervisor">Change Password</button>
                </div>
            </div>

            <!-- change password -->
            <div class="account-settings-modal" id="change-password-modal-supervisor">
                <div class="modal-header">
                    <h3>Change Password</h3>
                    <button id="backToAccountSettingsSupervisor" class="modal-close-profile-sub">&larr; Back</button>
                </div>

                <form action="functions/settings.php" method="POST" class="modal-form">
                    <div class="form-section-account">

                        <input type="hidden" name="userID" value="<?php echo $_SESSION['user_id']; ?>">

                        <div class="form-group-edit">
                            <label>Old Password</label>
                            <div class="input-wrapper">
                                <input type="password" id="oldPasswordSupervisor" name="oldPassword" placeholder="Enter old password">
                                <i class="toggle-pass" onclick="togglePassword('oldPasswordSupervisor', this)">👁</i>
                            </div>
                        </div>

                        <div class="form-group-edit">
                            <label>New Password</label>
                            <div class="input-wrapper">
                                <input type="password" id="newPasswordSupervisor" name="newPassword" placeholder="Enter new password">
                                <i class="toggle-pass" onclick="togglePassword('newPasswordSupervisor', this)">👁</i>
                            </div>
                        </div>

                        <div class="form-group-edit">
                            <label>Confirm New Password</label>
                            <div class="input-wrapper">
                                <input type="password" id="confirmPasswordSupervisor" name="confirmPassword" placeholder="Confirm new password">
                                <i class="toggle-pass" onclick="togglePassword('confirmPasswordSupervisor', this)">👁</i>
                            </div>
                        </div>

                        <div class="form-group-edit">
                            <button type="submit" class="account-btn" name="changePasswordSupervisor">
                                Save Password
                            </button>
                        </div>

                    </div>
                </form>
            </div>

            <!-- view Evaluation breakdown -->
            <div class="student-breakdown-container" id="student-breakdown-container">
                <div class="modal-header">
                    <h3>Student Evaluation Breakdown</h3>
                    <button id="closeStudentBreakdown" class="modal-close">&times;</button>
                </div>

                <div id="reportSummary"></div>
            </div>

            <!-- final evaluation -->
            <div class="student-final-evaluation" id="student-final-evaluation">
                <div class="final-modal-header">
                    <h3>Final Evaluation</h3>
                    <button id="closeFinalEvaluation" class="modal-close">&times;</button>
                </div>

                <div class="modal-body" id="finalEvaluationContent">

                    <div class="loading-state" id="evalLoading">
                        Loading evaluation...
                    </div>

                    <div class="evaluation-wrapper" id="evaluationWrapper" style="display:none;">

                        <div class="eval-header">
                            <div class="student-header compact">
                                <div class="student-avatar" id="evalAvatar"></div>
                                <div>
                                    <h3 id="evalName"></h3>
                                    <p id="evalID"></p>
                                </div>
                            </div>

                            <div class="final-pill" id="finalScore"></div>
                        </div>

                        <div class="evaluation-grid">


                            <div class="eval-card">
                                <div class="final-section-title">Performance</div>

                                <div class="mini-score-grid">
                                    <div>Attendance <span id="attendanceScore"></span></div>
                                    <div>Reports <span id="progressScore"></span></div>
                                    <div>Tasks <span id="taskScore"></span></div>
                                </div>
                            </div>


                            <div class="eval-card">
                                <div class="final-section-title">
                                    Tasks
                                    <button class="toggle-btn" onclick="toggleTasks()">View</button>
                                </div>

                                <div class="task-list-compact collapsed" id="taskList"></div>
                            </div>

                        </div>

                        <div class="recommendation-box" id="recommendationBox"></div>

                        <div class="final-section-title">Supervisor Evaluation</div>

                        <div class="soft-skill-grid">

                            <div class="skill-card">
                                <div class="skill-header">
                                    <span>Work Ethics</span>
                                    <span id="ethicsValue">0%</span>
                                </div>
                                <input type="range" min="0" max="100" value="0" id="ethicsRating">
                                <div class="skill-labels">
                                    <span>Poor</span>
                                    <span>Excellent</span>
                                </div>
                            </div>

                            <div class="skill-card">
                                <div class="skill-header">
                                    <span>Communication</span>
                                    <span id="communicationValue">0%</span>
                                </div>
                                <input type="range" min="0" max="100" value="0" id="communicationRating">
                                <div class="skill-labels">
                                    <span>Weak</span>
                                    <span>Strong</span>
                                </div>
                            </div>

                            <div class="skill-card">
                                <div class="skill-header">
                                    <span>Initiative</span>
                                    <span id="initiativeValue">0%</span>
                                </div>
                                <input type="range" min="0" max="100" value="0" id="initiativeRating">
                                <div class="skill-labels">
                                    <span>Passive</span>
                                    <span>Proactive</span>
                                </div>
                            </div>

                            <div class="skill-card">
                                <div class="skill-header">
                                    <span>Discipline</span>
                                    <span id="disciplineValue">0%</span>
                                </div>
                                <input type="range" min="0" max="100" value="0" id="disciplineRating">
                                <div class="skill-labels">
                                    <span>Low</span>
                                    <span>High</span>
                                </div>
                            </div>

                        </div>

                        <div class="final-section-title">Supervisor Final Evaluation</div>

                        <div class="final-eval-card">

                            <div class="eval-field">
                                <label>Overall Remarks</label>
                                <textarea id="finalRemarks" placeholder="Write detailed evaluation of student's performance..."></textarea>
                            </div>

                            <div class="eval-field">
                                <label>Final Recommendation</label>
                                <select id="finalRecommendation">
                                    <option value="EXCELLENT">Excellent - Recommended for employment</option>
                                    <option value="VERY GOOD">Very Good - Approved</option>
                                    <option value="SATISFACTORY">Satisfactory - Passed</option>
                                    <option value="NEEDS IMPROVEMENT">Needs Improvement</option>
                                    <option value="FAILED">Failed</option>
                                </select>
                            </div>

                        </div>



                        <div class="evaluation-actions">
                            <button class="save-btn" onclick="saveFinalEvaluation()">
                                Finalize Evaluation
                            </button>
                        </div>

                    </div>

                </div>

            </div>

            <!-- view final evaluation -->
            <div class="final-evaluation-view" id="final-evaluation-view">
                <div class="evaluation-view-header">
                    <h3>Evaluation Report</h3>
                    <div class="evaluation-header-actions">
                        <button class="download-report-btn" onclick="downloadEvaluationReport()">
                            <i class="bi bi-download"></i>
                        </button>

                        <button id="closeFinalEvaluationView" class="modal-close">&times;</button>
                    </div>
                </div>

                <div class="evaluation-view-body" id="finalEvaluationReportBody">


                    <div class="evaluation-hero-view">

                        <div class="evaluation-hero-info-view">
                            <h2 id="reportStudentName">Student Name</h2>
                            <p id="reportStudentID">Student ID</p>
                        </div>

                        <div class="evaluation-score-badge-view" id="reportFinalScore">
                            0%
                        </div>

                    </div>

                    <div class="evaluation-metrics-view">

                        <div class="metric-card-view">
                            <span class="metric-label-view">Attendance</span>
                            <span class="metric-value-view" id="reportAttendance">0%</span>
                        </div>

                        <div class="metric-card-view">
                            <span class="metric-label-view">Reports</span>
                            <span class="metric-value-view" id="reportProgress">0%</span>
                        </div>

                        <div class="metric-card-view">
                            <span class="metric-label-view">Tasks</span>
                            <span class="metric-value-view" id="reportTasks">0%</span>
                        </div>

                    </div>

                    <div class="section-title-view">Supervisor Ratings</div>

                    <div class="ratings-grid-view">

                        <div class="rating-item-view">
                            <span>Work Ethics</span>
                            <strong id="rEthics">0</strong>
                        </div>

                        <div class="rating-item-view">
                            <span>Communication</span>
                            <strong id="rCommunication">0</strong>
                        </div>

                        <div class="rating-item-view">
                            <span>Initiative</span>
                            <strong id="rInitiative">0</strong>
                        </div>

                        <div class="rating-item-view">
                            <span>Discipline</span>
                            <strong id="rDiscipline">0</strong>
                        </div>

                    </div>

                    <div class="section-title-view">Final Recommendation</div>

                    <div class="card-view">
                        <h4 id="rRecommendationTitle"></h4>
                        <p id="rRecommendationText"></p>
                    </div>

                    <div class="section-title-view">Remarks</div>

                    <div class="card-view">
                        <p id="rRemarks"></p>
                    </div>

                </div>
            </div>

            <!-- download final evaluation -->
            <!-- <div class="download-final-evaluation" id="download-final-evaluation">

             </div> -->

            <!-- view approval tasks -->
            <div class="student-application-approve" id="student-application-approve">
            </div>

            <!-- view approved/rejected tasks -->
            <div class="task-view" id="task-view">
                <div class="modal-header">
                    <h3>Task Details</h3>
                    <button id="closeTaskViewModal" class="modal-close-profile">&times;</button>
                </div>

                <div id="taskDeadlineWarning" class="task-deadline-warning" style="display:none;">
                    <span id="taskDeadlineWarningText"></span>
                </div>
                <div class="task-modal-content">


                    <div class="task-modal-body">

                        <div class="task-info-grid">

                            <div class="info-item">
                                <label>Title</label>
                                <p id="modalTaskTitle"></p>
                            </div>

                            <div class="info-card">

                                <div class="card-header" onclick="toggleSection(this)">
                                    <h4>Descriptions </h4>
                                    <span class="arrow">▼</span>
                                </div>

                                <div class="card-body">
                                    <div class="info-item">
                                        <label>Description</label>
                                        <p id="modalTaskDesc"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="info-card">

                                <div class="card-header" onclick="toggleSection(this)">
                                    <h4>Progress </h4>
                                    <span class="arrow">▼</span>
                                </div>

                                <div class="card-body">
                                    <div class="info-item">
                                        <label>Status</label>
                                        <span id="modalTaskStatus" class="status-badge"></span>
                                    </div>

                                    <div class="info-item">
                                        <label>Due Date</label>
                                        <p id="modalTaskDue"></p>
                                    </div>

                                    <div class="info-item">
                                        <label>Completed Date</label>
                                        <p id="modalTaskCompleted"></p>
                                    </div>

                                    <div class="info-item">
                                        <label>Progress</label>
                                        <p id="modalTaskProgress"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="divider"></div>

                            <div class="info-card" id="uploadedFileSection">

                                <div class="card-header" onclick="toggleSection(this)">
                                    <h4>Uploaded Files</h4>
                                    <span class="arrow">▼</span>
                                </div>

                                <div class="card-body">
                                    <div class="documents-grid">

                                        <div class="doc-card">

                                            <button
                                                id="viewUploadedFileBtn"
                                                class="btn-preview"
                                                style="display:none;"
                                                onclick="previewFile(document.getElementById('viewUploadedFileBtn').dataset.file)">
                                                👁 View File
                                            </button>

                                            <span
                                                id="uploadedFileStatus"
                                                class="status-badge missing">
                                                No File Uploaded
                                            </span>

                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="info-card">

                                <div class="card-header" onclick="toggleSection(this)">
                                    <h4>Notes </h4>
                                    <span class="arrow">▼</span>
                                </div>

                                <div class="card-body">
                                    <div class="info-item" id="studentNoteSection" style="display:none;">
                                        <label>Student Note</label>
                                        <p id="modalStudentNote"></p>
                                    </div>

                                    <div class="info-item" id="supervisorFeedbackSection" style="display:none;">
                                        <label>Supervisor Feedback</label>
                                        <p id="modalSupervisorFeedback"></p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- view student charts -->
            <div id="student-progress-container" class="student-progress-container">
                <div class="modal-header">
                    <h3>Student Progression</h3>
                    <button id="closeStudentProgress" class="modal-close">&times;</button>
                </div>

                <div class="chart-toggle-buttons">
                    <button class="view-student-btn active" data-view="hours">Completed Hours</button>
                    <button class="view-student-btn" data-view="attendance">Attendance</button>
                    <button class="view-student-btn" data-view="tasks">Tasks</button>
                </div>

                <div class="chart-content-wrapper">
                    <div id="hoursView" class="view-section">
                        <div style="height:300px;">
                            <canvas id="progressChart"></canvas>
                        </div>
                    </div>

                    <div id="attendanceView" class="view-section" style="display:none;">

                        <div class="chart-container">
                            <canvas id="attendanceChart"></canvas>
                        </div>

                        <div class="table-container-attendance">

                            <table>
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Time In</th>
                                        <th>Time Out</th>
                                        <th>Status</th>
                                        <th>Total Hours</th>
                                    </tr>
                                </thead>

                                <tbody id="attendanceReportBody">

                                </tbody>

                            </table>

                        </div>

                    </div>

                    <div id="tasksView" class="view-section" style="display:none;">
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Task Title</th>
                                        <th>Description</th>
                                        <th>Deadline</th>
                                        <th>Status</th>
                                        <th>Date Created</th>
                                    </tr>
                                </thead>

                                <tbody id="assignedStudentTaskBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


            </div>

            <!-- create task -->
            <div class="create-task-container" id="create-task-container">
                <div class="modal-header">
                    <h3><i class="bi bi-plus-circle"></i> Create Task</h3>
                    <button id="closeCreateTaskModal" class="modal-close">&times;</button>
                </div>

                <div class="info-card active">

                    <div class="card-header" onclick="toggleSection(this)">
                        <h4>Task Guidelines</h4>
                        <span class="arrow">▼</span>
                    </div>

                    <div class="card-body">
                        <div class="task-guidelines">
                            <h4><i class="bi bi-pin-angle"></i> Task Creation Guidelines</h4>
                            <ul>
                                <li>✔ Be specific — clearly state what the student must submit</li>
                                <li>✔ Include expected output (PDF, report, screenshot, etc.)</li>
                                <li>✔ Set realistic deadlines based on workload</li>
                                <li>✔ Use action words like “Create”, “Submit”, “Prepare”, “Develop”</li>
                                <li>✔ Avoid vague titles like “Documentation Task”</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                <form id="createTaskForm">

                    <div class="info-card">

                        <div class="card-header" onclick="toggleSection(this)">
                            <h4>Task Information</h4>
                            <span class="arrow">▼</span>
                        </div>

                        <div class="card-body">
                            <div class="form-group">
                                <label>Task Title</label>
                                <input
                                    type="text"
                                    name="title"
                                    placeholder="Example: Submit Weekly Progress Report"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>Task Description</label>
                                <textarea
                                    name="description"
                                    placeholder="Describe clearly what the student needs to complete and submit..."></textarea>
                            </div>

                            <div class="form-group">
                                <label>Due Date</label>
                                <input
                                    type="date"
                                    name="due_date"
                                    id="due_date"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="info-card">

                        <div class="card-header" onclick="toggleSection(this)">
                            <h4>Assignment</h4>
                            <span class="arrow">▼</span>
                        </div>

                        <div class="card-body">
                            <div class="assign-section">
                                <h4><i class="bi bi-people"></i> Assign Students</h4>
                                <p>Select one or multiple students for this task</p>

                                <input
                                    type="text"
                                    id="taskStudentSearch"
                                    placeholder="🔍 Search student by name, ID, Course or Yearlevel...">

                                <div class="list-box" id="taskStudentList">
                                    <?php
                                    $search =  $_POST['search'] ?? '';
                                    echo renderTaskAssignStudentList($conn, $superID, $search);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="superID" value="<?= $superID ?>">

                    <button type="submit" class="submit-btn">
                        Create Task & Assign
                    </button>

                </form>
            </div>

            <!-- Edit  -->
            <div class="task-edit-container" id="task-edit-container">
                <div class="modal-header">
                    <h3><i class="bi bi-pencil-square"></i> Edit Task</h3>
                    <button id="closeEditTaskModal" class="modal-close">&times;</button>
                </div>

                <form id="editTaskForm">

                    <input type="hidden" id="editTaskID" name="taskID">

                    <div class="form-group">
                        <label>Task Title</label>
                        <input type="text" id="editTitle" name="title" required>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea id="editDescription" name="description"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Due Date</label>
                        <input type="date" id="editDueDate" name="due_date" required>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select id="editStatus" name="status" required>
                            <option value="NOT STARTED">NOT STARTED</option>
                            <option value="IN PROGRESS">IN PROGRESS</option>
                            <option value="SUBMITTED">SUBMITTED</option>
                            <option value="APPROVED">APPROVED</option>
                            <option value="REJECTED">REJECTED</option>
                        </select>
                    </div>

                    <button type="submit" class="submit-btn">
                        Update Task
                    </button>

                </form>
            </div>

            <!-- delete modal -->
            <div id="deleteModal" class="modal-overlay">
                <div class="modal-box">

                    <h3>Delete Task</h3>

                    <p>Are you sure you want to delete this task? This action cannot be undone.</p>

                    <div class="modal-actions">
                        <button id="cancelDeleteBtn" class="btn-cancel">Cancel</button>
                        <button id="confirmDeleteBtn" class="btn-danger">Delete</button>
                    </div>

                </div>
            </div>

            <!-- supervisor dashboard -->
            <section class="supervisor-dashboard" id="supervisor-dashboard">
                <!-- <div class="title-block-bot">
                    <h2>Supervisor Dashboard</h2>
                    <p>Monitor student OJT progress and pending tasks</p>
                </div> -->

                <!-- cards -->
                <section class="cards">
                    <div class="card total-student">

                        <?php
                        $totalAssignedStudents = countTotalAssignedStudents($conn, $superID);
                        $totalAssignedTrend = getSupervisorTrend($conn, 'student_supervisor', 'superID', $superID, 'date_assigned');
                        $totalAssignedBadge = getBadge($totalAssignedStudents);
                        ?>

                        <span class="card-badge"><?= $totalAssignedBadge ?></span>

                        <div class="card-content">
                            <div>
                                <h3>Total Assigned Students</h3>
                                <p>Students currently handled by supervisor</p>

                                <span class="trend">
                                    ▲ <?= $totalAssignedTrend ?> this week
                                </span>
                            </div>

                            <h2><?= $totalAssignedStudents ?></h2>
                        </div>
                    </div>

                    <div class="card active-student">

                        <?php
                        $totalActiveStudents = countTotalActiveStudents($conn, $superID);
                        $activeTrend = getSupervisorTrend($conn, 'student_supervisor', 'superID', $superID, 'date_assigned');
                        $activeBadge = getBadge($totalActiveStudents);
                        ?>

                        <span class="card-badge"> <?= $activeBadge ?></span>

                        <div class="card-content">
                            <div>
                                <h3>Active Students</h3>
                                <p>Students with ongoing supervision and tasks</p>

                                <span class="trend">
                                    ▲ <?= $activeTrend ?> this week
                                </span>
                            </div>

                            <h2 class="white-h2"><?= $totalActiveStudents ?></h2>
                        </div>
                    </div>

                    <div class="card completed-student">

                        <?php
                        $totalCompletedStudents = countTotalCompletedStudents($conn, $superID);
                        $completedTrend = getSupervisorTrend($conn, 'student_progress', 'completion_status', 'COMPLETED', 'last_updated');
                        $completedBadge = getBadge($totalCompletedStudents);
                        ?>

                        <span class="card-badge"><?= $completedBadge ?></span>

                        <div class="card-content">
                            <div>
                                <h3>Completed Students</h3>
                                <p>Students who have finished required OJT tasks</p>

                                <span class="trend">
                                    ▲ <?= $completedTrend ?> this week
                                </span>
                            </div>

                            <h2><?= $totalCompletedStudents ?></h2>
                        </div>

                    </div>

                    <div class="card pending-task">
                        <?php
                        $pendingReports = countPendingTasks($conn, $superID);
                        $pendingTrend = getSupervisorTrend($conn, 'student_tasks', 'status', 'SUBMITTED', 'date_created');
                        $pendingBadge = getBadge($pendingReports);
                        ?>

                        <span class="card-badge"><?= $pendingBadge ?></span>

                        <div class="card-content">
                            <div>
                                <h3>Pending Task Approvals</h3>
                                <p>Submitted tasks waiting for supervisor review</p>

                                <span class="trend">
                                    ▲ <?= $pendingTrend ?> this week
                                </span>
                            </div>

                            <h2 class="white-h2"><?= $pendingReports ?></h2>
                        </div>
                    </div>

                </section>

                <section class="dashboard-charts">

                    <section class="wrapper line-chart">

                        <div class="chart-header">

                            <div class="chart-title">
                                <h2>Attendance Trends</h2>
                                <p>Track student attendance performance</p>
                            </div>

                            <div class="chart-actions">

                                <select id="monthSelector"></select>

                                <button id="downloadChartBtn">
                                    <i class="bi bi-download"></i>
                                </button>

                            </div>

                        </div>

                        <div class="chart-container">
                            <canvas id="lineChart"></canvas>
                        </div>

                    </section>

                    <section class="wrapper pie-chart">
                        <div class="chart-header">
                            <div class="chart-title">
                                <div>
                                    <h2>Overall Attendance</h2>
                                    <p>Distribution across all assigned students</p>
                                </div>
                            </div>
                        </div>

                        <div class="pie-container">
                            <canvas id="pieChart"></canvas>
                        </div>

                    </section>

                    <section class="quick-action-container">
                        <div class="quick-action-header">
                            <div class="quick-action-title">
                                <h2>Quick Actions</h2>
                                <p>Instant system tools & shortcuts</p>
                            </div>
                        </div>

                        <div class="quick-action-grid">
                            <button onclick="openCreateTask()">
                                <i class="bi bi-person-plus-fill"></i>
                                <span>Create Task</span>
                            </button>

                            <button onclick="openRfid()">
                                <i class="bi bi-broadcast-pin"></i>
                                <span>RFID</span>
                            </button>

                            <button id="darkModeToggle" class="dark-toggle">
                                <i class="bi bi-moon-fill"></i>
                                <span>Darkmode</span>
                            </button>

                            <button onclick="generateReport()">
                                <i class="bi bi-bar-chart-fill"></i>
                                <span>Reports</span>
                            </button>

                            <button onclick="openStudents()">
                                <i class="bi bi-people-fill"></i>
                                <span>Students</span>
                            </button>

                            <button onclick="openLogs()">
                                <i class="bi bi-journal-text"></i>
                                <span>Logs</span>
                            </button>

                        </div>
                    </section>

                    <section class="wrapper bar-chart">
                        <div class="chart-header">
                            <div class="chart-title">
                                <h2>Task Completion Monitoring</h2>
                                <p>Tracking progress of all assigned students</p>
                            </div>
                        </div>
                        <canvas id="barChart"></canvas>
                    </section>
                </section>


                <div class="dashboard-bottom">
                    <!-- ALERTS -->
                    <div class="alerts-container">
                        <h3>Alerts</h3>

                        <ul class="alerts-list scrollable">

                            <?php
                            $alerts = getSupervisorAlerts($conn, $superID);

                            if (count($alerts) > 0):

                                foreach ($alerts as $alert):
                            ?>

                                    <li class="alert-item <?= $alert['type'] ?>">

                                        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($alert['message']) ?>

                                        <button class="alert-btn"
                                            onclick="<?= $alert['action'] ? $alert['action'] . '(' . $alert['id'] . ')' : '' ?>">
                                            View
                                        </button>

                                    </li>

                                <?php
                                endforeach;
                            else:
                                ?>

                                <li class="alert-item success">
                                    <i class="bi bi-check-circle"></i> No issues detected. All students are active.
                                </li>

                            <?php endif; ?>

                        </ul>
                    </div>

                    <!-- activity logs (recent) -->
                    <div class="activity-container">
                        <h3>Recent Activity</h3>

                        <ul class="activity-list scrollable">

                            <?php
                            $activities = getSupervisorActivities($conn, $superID);

                            if (count($activities) > 0):
                                foreach ($activities as $act):
                            ?>

                                    <li class="activity-item <?= $act['type'] ?>">
                                        <i class="bi bi-app-indicator"></i> <?= htmlspecialchars($act['message']) ?>
                                        <small><?= $act['time'] ?></small>
                                    </li>

                                <?php endforeach;
                            else: ?>

                                <li class="activity-item success">
                                    <i class="bi bi-check-circle"></i> No recent activity
                                </li>

                            <?php endif; ?>

                        </ul>
                    </div>
                </div>

            </section>

            <!-- supervisor oversight -->
            <section class="supervisor-oversight" id="supervisor-oversight">
                <div class="user-management">
                    <div class="title-block">
                        <h2>Oversight</h2>
                        <p>Monitor student OJT progress and pending tasks</p>
                    </div>

                    <div class="create-section-container">
                        <button class="create-btn" id="create-task-btn">
                            <span class="icon"><i class="bi bi-calendar-week"></i></span>
                            <span class="text">Create Task</span>
                        </button>
                    </div>
                </div>

                <!-- PENDING APPROVALS -->
                <div class="table-view" id="task-submit-table">
                    <div class="table-switcher">
                        <button class="tab-btn active" data-tab="manage" onclick="showManageTable()">
                            Task Management
                        </button>

                        <button class="tab-btn" data-tab="submitted" onclick="showSubmittedTable()">
                            Student Submission
                        </button>
                    </div>
                    <div class="top-bar">

                        <div class="top-header">
                            <h3 class="table-title">Submissions</h3>
                            <p>Search and review students waiting for approval.</p>
                        </div>

                        <div class="search-filter">
                            <div class="search-container">
                                <i class="bi bi-search search-icon"></i>
                                <input type="text" id="reportApprovalSearch"
                                    placeholder="Search by ID, Name, OR Email">
                            </div>
                        </div>

                    </div>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Date Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody id="approvalReportBody">
                                <?php
                                $search = $_POST['search'] ?? '';
                                echo renderApprovalReportList($conn, $superID, $search);
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>


                <div class="table-view show" id="task-manage-table">
                    <div class="table-switcher">
                        <button class="tab-btn active" data-tab="manage" onclick="showManageTable()">
                            Task Management
                        </button>

                        <button class="tab-btn" data-tab="submitted" onclick="showSubmittedTable()">
                            Student Submission
                        </button>
                    </div>
                    <div class="top-bar">

                        <div class="top-header">
                            <h3 class="table-title">Tasks</h3>
                            <p>Search and review task created.</p>
                        </div>

                        <div class="search-filter">
                            <div class="search-container">
                                <i class="bi bi-search search-icon"></i>
                                <input type="text" id="assignedTaskSearch"
                                    placeholder="Search by Title, ID or Name...">
                            </div>

                            <div class="filter-group">

                                <select id="taskStatusFilter">
                                    <option value="">All Status</option>
                                    <option value="NOT STARTED">Not started</option>
                                    <option value="IN PROGRESS">In progress</option>
                                    <option value="SUBMITTED">Submitted</option>
                                    <option value="REJECTED">Rejected</option>
                                    <option value="APPROVED">Approved</option>
                                </select>

                            </div>

                            <div class="filter-group">
                                <input type="date" id="dateDeadline" title="From date">
                            </div>

                        </div>

                    </div>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Task Title</th>
                                    <th>Student Assigned</th>
                                    <th>Deadline</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody id="assignedTaskBody">
                                <?php
                                $search = $_POST['search'] ?? '';
                                $status = $_POST['status'] ?? '';
                                $deadline = $_POST['deadline'] ?? '';
                                echo renderTaskManagementList($conn, $superID, $search, $status, $deadline);
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </section>

            <section class="supervisor-students" id="supervisor-students">
                <div class="title-block-bot">
                    <h2>Students</h2>
                    <p>Monitor Students performance and attendance.</p>
                </div>

                <!-- STUDENT PROGRESS -->
                <div>
                    <div class="top-bar">

                        <div class="top-header">
                            <h3 class="table-title">Student Progress</h3>
                            <p>Search and review students process.</p>
                        </div>

                        <div class="search-filter">
                            <div class="search-container">
                                <i class="bi bi-search search-icon"></i>
                                <input type="text" id="studentProcessSearch"
                                    placeholder="Search by ID or Name">
                            </div>
                        </div>

                    </div>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Completed Hours</th>
                                    <th>Required Hours</th>
                                    <th>Remaining Hours</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody id="studentProgressBody">
                                <?php
                                $search = $_POST['search'] ?? '';
                                echo renderStudentProgressList($conn, $superID, $search);
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </section>

            <!-- student attendances -->
            <section class="supervisor-attendance" id="supervisor-attendance">
                <div class="title-block-bot">
                    <h2>Student Attendance</h2>
                    <p>Monitor student attendance records and time logs.</p>
                </div>
                <div class="top-bar">

                    <div class="top-header">
                        <h3 class="table-title">Attendance</h3>
                        <p>Search and review student attendance records.</p>
                    </div>

                    <div class="search-filter">

                        <div class="search-container">
                            <i class="bi bi-search search-icon"></i>
                            <input
                                type="text"
                                id="studentAttendanceSearch"
                                placeholder="Search by Student ID, Name, or RFID">
                        </div>

                        <div class="filter-group">

                            <select id="attendanceStatusFilter">
                                <option value="">All Status</option>
                                <option value="present">Present</option>
                                <option value="late">Late</option>
                                <option value="absent">Absent</option>
                                <option value="excused">Excused</option>
                            </select>

                        </div>

                        <div class="filter-group">
                            <input type="date" id="attendanceDateFrom" title="From date">
                            <input type="date" id="attendanceDateTo" title="To date">
                        </div>

                    </div>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>RFID</th>
                                <th>Date</th>
                                <th>Time in</th>
                                <th>Time out</th>
                                <th>Status</th>
                                <th>Total hours</th>
                                <th>Remarks</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody id="studentAttendanceBody">
                            <?php
                            $search = $_POST['search'] ?? '';
                            $dateFromAttendance = $_POST['dateFromAttendance'] ?? '';
                            $dateToAttendance = $_POST['dateToAttendance'] ?? '';
                            echo renderStudentMainAttendance($conn, $superID, $search, $dateFromAttendance, $dateToAttendance);
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- reports evaluation -->
            <section class="supervisor-evaluation" id="supervisor-evaluation">
                <div class="title-block-bot">
                    <h2>Reports & Evaluation</h2>
                    <p>Monitor Students performance and attendance.</p>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Attendance</th>
                                <th>Reports</th>
                                <th>Tasks</th>
                                <th>Final Grade</th>
                                <th>Remarks</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody id="evaluationBody">
                            <?php
                            echo renderEvaluationList($conn, $superID);
                            ?>
                        </tbody>
                    </table>
                </div>

            </section>

            <!-- activity logs -->
            <div class="supervisor-activity" id="supervisor-activity">
                <div class="top-bar">

                    <div class="top-header">
                        <h3 class="table-title">Activity Logs</h3>
                        <p>Monitor system actions and user activities.</p>
                    </div>

                    <div class="search-filter">
                        <div class="search-container">
                            <i class="bi bi-search search-icon"></i>
                            <input type="text" id="superActivityLogSearch"
                                placeholder="Search by target ID..">
                        </div>


                        <div class="filter-group">

                            <select id="moduleFilter">
                                <option value="">All Modules</option>
                                <option value="TASK">Task</option>
                                <option value="ATTENDANCE">Attendance</option>
                                <option value="ASSIGNMENT">ASSIGNMENT</option>
                                <option value="DOCUMENT">Document</option>
                                <option value="SYSTEM">System</option>
                            </select>

                        </div>

                        <div class="filter-group">
                            <input type="date" id="dateFrom" title="From date">
                            <input type="date" id="dateTo" title="To date">
                        </div>
                    </div>

                </div>

                <div class="activity-log-container">

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Activity Type</th>
                                    <th>Module</th>
                                    <th>Target Type</th>
                                    <th>Target ID</th>
                                    <th>IP Address</th>
                                    <th>Date</th>
                                </tr>
                            </thead>

                            <tbody id="activityLogTableBody">
                                <?php
                                $search = $_POST['search'] ?? '';
                                $module = $_POST['module'] ?? '';
                                $dateFrom = $_POST['dateFrom'] ?? '';
                                $dateTo = $_POST['dateTo'] ?? '';
                                echo renderSupervisorActivityLogTable($conn, $superID, $search, $module, $dateFrom, $dateTo);
                                ?>
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>

            <!-- <div id="imagePreviewModal" class="image-modal">
                <span id="closeImagePreview">&times;</span>
                <img id="previewImg">
            </div> -->

            <div id="imagePreviewModal" class="image-modal">
                <span id="closeImagePreview">&times;</span>

                <div id="previewContainer"></div>
            </div>

            <div id="inactivityModal" class="inactivity-modal">
                <div class="inactivity-box">

                    <div class="inactivity-header">
                        <div class="warning-icon"><i class="bi bi-hourglass-bottom"></i></div>
                        <h3>Session Timeout Warning</h3>
                    </div>

                    <p class="inactive-text">
                        You have been inactive for a while. For your security, the system will automatically log you out.
                    </p>

                    <div class="countdown-wrapper">
                        <span class="countdown-label">Logging out in</span>
                        <span id="countdown" class="countdown-number">60</span>
                        <span class="countdown-label">seconds</span>
                    </div>

                    <button onclick="stayLoggedIn()" class="stay-btn">
                        Stay Logged In
                    </button>

                </div>
            </div>

            <hr>

            <!-- FOOTER -->
            <footer class="footer">
                <p>© 2026 OJT Tracking System</p>
            </footer>

        </main>

    </div>

</body>
<script>
    window.forceChangePassword = <?= json_encode($forceChange) ?>;
    window.currentSuperID = <?= json_encode($superID); ?>;

    let inactivityTimer;
    let countdownTimer;
    let countdownValue = 60;

    const modal = document.getElementById("inactivityModal");
    const countdownEl = document.getElementById("countdown");

    function startCountdown() {

        countdownValue = 60;
        countdownEl.innerText = countdownValue;

        modal.style.display = "flex";

        countdownTimer = setInterval(() => {

            countdownValue--;
            countdownEl.innerText = countdownValue;

            if (countdownValue <= 10) {
                countdownEl.style.color = "#dc2626";
                countdownEl.style.transform = "scale(1.2)";
            }

            if (countdownValue <= 0) {
                clearInterval(countdownTimer);
                window.location.href = "../logoutPhase.php";
            }

        }, 1000);
    }

    function resetTimer() {

        clearTimeout(inactivityTimer);
        clearInterval(countdownTimer);

        modal.style.display = "none";

        inactivityTimer = setTimeout(() => {
            startCountdown();
        }, 600000);
    }

    function stayLoggedIn() {

        clearTimeout(inactivityTimer);
        clearInterval(countdownTimer);

        modal.style.display = "none";

        resetTimer();
    }

    window.onload = resetTimer;
    document.onmousemove = resetTimer;
    document.onkeypress = resetTimer;
    document.onclick = resetTimer;
    document.onscroll = resetTimer;
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../../js/supervisor/supervisorDashboard.js"></script>

</html>