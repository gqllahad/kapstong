<?php

session_start();
require_once("../kapstongConnection.php");
require_once("../functions.php");
require_once("../sessionTimeout.php");

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");


if ($_SESSION['role'] !== "student") {
    header("Location: ../trackerMain.php");
    exit();
}

if ($_SESSION['isVerified'] !== "VERIFIED") {
    header("Location: ../trackerMain.php");
    exit();
}

$studentID = $_SESSION['studentID'];
$studentName = $_SESSION['name'];

if ($studentID) {
    $studentInfo = getStudentInfo($conn, $studentID);
}

$documents = getStudentDocuments($conn, $studentID);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="icon" type="image/png" href="../../kapstongImage/logo.jpg">
    <link rel="stylesheet" href="../../css/student/studentDashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body data-student-id="<?= $studentID ?>">

    <header class="navbar">
        <div class="header-title">
            <img src="../../kapstongImage/download (1).jpg" class="logo-img" style="border-radius: 50%;">
            <h1>Student Dashboard</h1>
        </div>

        <button id="menuToggle">☰</button>
        <nav class="profile-menu" id="profileMenu" hidden>
            <a id="openProfileBtn">Profile</a>
            <a id="openAccountSettingsBtn">Account Setting</a>
            <hr style="width: 75%; text-align: left;">
            <a href="../logoutPhase.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </nav>
    </header>

    <div class="layout">
        <aside class="sidebar">
            <ul class="menu">
                <li class="active">
                    <button id="student-dashboard-btn"><i class="bi bi-house"></i> Home Page</button>
                </li>
                <li>
                    <button id="student-tasks-btn"> <i class="bi bi-list-check"></i> My Tasks</button>
                </li>
                <li><button id="student-documents-btn"> <i class="bi bi-folder2-open"></i>My Documents</button></li>
                <li><button id="student-attendance-btn"><i class="bi bi-calendar-check"></i> Attendance logs</button></li>
                <li><button id="student-activity-btn"><i class="bi bi-activity"></i> Activity logs</button></li>
            </ul>

            <!-- <div class="sidebar-profile">

                <div class="profile-left">
                    <div class="profile-avatar">
                        M
                    </div>
                    <div class="profile-info">
                        <span class="profile-name">MADRIGAL</span>
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
            </div> -->
        </aside>

        <main class="content">

            <div id="overlay" class="overlay"></div>
            <div id="toast"></div>

            <!-- profile modal -->
            <div class="profile-modal" id="profileModal">
                <div class="modal-header">
                    <h3>Profile</h3>
                    <button id="closeProfileModal" class="modal-close-profile">&times;</button>
                </div>


                <form id="profileUpload" action="./student_functions/profile_upload.php" method="POST" enctype="multipart/form-data">
                    <input type="file" id="profileInput" name="profilePicture" accept=".jpg, .jpeg, .png" style="display:none;">
                </form>

                <div class="profile-picture-container" onclick="document.getElementById('profileInput').click();">
                    <img id="profilePic" src="<?php
                                                echo isset($documents['profilePicture']) && !empty($documents['profilePicture'])
                                                    ? '../../uploads/student_uploads/' . $studentID . '/' . $documents['profilePicture']
                                                    : '../../uploads/default.jpg';
                                                ?>">
                    <div class="change-overlay">Change Profile</div>
                </div>

                <div class="form-section-profile">

                    <div class="form-group-edit duos">
                        <label>Full Name</label>
                        <input type="text" name="fullName" id="fullName"
                            value="<?php echo htmlspecialchars($studentName ?? ''); ?>" readonly>
                    </div>

                    <div class="form-group-edit duos">
                        <label>Email Address</label>
                        <input type="text" name="email" id="email"
                            value="<?php echo htmlspecialchars($studentInfo['email'] ?? ''); ?>" readonly>
                    </div>

                    <div class="form-group-edit duos">
                        <label>Course</label>
                        <input type="text" value="<?php echo htmlspecialchars(
                                                        ($studentInfo['course'] ?? '') . ' - ' . ($studentInfo['course_name'] ?? '')
                                                    );  ?>" readonly>
                    </div>

                    <div class="form-group-edit duos">
                        <label>Department</label>
                        <input type="text" value="<?php echo htmlspecialchars(
                                                        ($studentInfo['course_dpt'] ?? '')
                                                    );  ?>" readonly>
                    </div>

                </div>
            </div>

            <!-- Account settings modal -->
            <div class="account-settings-modal" id="account-settings">
                <div class="modal-header">
                    <h3>Account Settings</h3>
                    <button id="closeAccountModal" class="modal-close-profile">&times;</button>
                </div>

                <div class="form-section-account">
                    <button class="account-btn" id="openChangePassword">Change Password</button>
                    <button class="account-btn" id="openForgotPIN">Forgot Password PIN</button>
                </div>

            </div>

            <!-- change password -->
            <div class="account-settings-modal" id="change-password-modal">
                <div class="modal-header">
                    <h3>Change Password</h3>
                    <button id="backToAccountSettings1" class="modal-close-profile-sub">&larr; Back</button>
                </div>
                <form action="student_functions/settings.php" method="POST" class="modal-form">
                    <div class="form-section-account">

                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($studentInfo['email'] ?? ''); ?>">

                        <div class="form-group-edit">
                            <label>Old Password</label>
                            <div class="input-wrapper">
                                <input type="password" id="oldPassword" name="oldPassword" placeholder="Enter old password">
                                <i class="toggle-pass" onclick="togglePassword('oldPassword', this)">👁</i>
                            </div>
                        </div>

                        <div class="form-group-edit">
                            <label>New Password</label>
                            <div class="input-wrapper">
                                <input type="password" id="newPassword" name="newPassword" placeholder="Enter new password">
                                <i class="toggle-pass" onclick="togglePassword('newPassword', this)">👁</i>
                            </div>
                        </div>

                        <div class="form-group-edit">
                            <label>Confirm New Password</label>
                            <div class="input-wrapper">
                                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm new password">
                                <i class="toggle-pass" onclick="togglePassword('confirmPassword', this)">👁</i>
                            </div>
                        </div>

                        <div class="form-group-edit">
                            <button type="submit" id="savePassword" class="account-btn" name="changePasswordStudent">Save Password</button>
                        </div>
                    </div>
                </form>
            </div>

            <!--  pin -->
            <div class="account-settings-modal" id="forgot-pin-modal">
                <div class="modal-header">
                    <h3>Forgot Password PIN</h3>
                    <button id="backToAccountSettings2" class="modal-close-profile-sub">&larr; Back</button>
                </div>

                <div class="form-section-account">
                    <div class="form-group-edit">
                        <label>Set 4-digit PIN</label>
                        <input type="number" id="forgotPin" placeholder="Enter 4-digit PIN">
                    </div>

                    <div class="form-group-edit">
                        <button id="savePin" class="account-btn">Save PIN</button>
                    </div>
                </div>
            </div>

            <!-- submit task modal -->
            <div class="submit-task-modal" id="submit-task-modal">
                <div class="modal-header">
                    <h3>Submit Task</h3>
                    <button id="closeSubmitModal" class="modal-close-profile">&times;</button>
                </div>

                <form id="submitTaskForm" enctype="multipart/form-data" class="submit-task-form">

                    <input type="hidden" id="submitTaskID" name="taskID">

                    <div class="form-group-task">
                        <label for="student_note">
                            <i class="bi bi-pencil-square"></i>
                            Submission Notes
                        </label>

                        <textarea
                            id="student_note"
                            name="student_note"
                            placeholder="Add a short explanation about your submission..."></textarea>
                    </div>

                    <div class="form-group-task">
                        <label for="submission_file">
                            <i class="bi bi-cloud-upload"></i>
                            Upload Files
                        </label>

                        <input
                            type="file"
                            id="submission_file"
                            name="submission_file[]"
                            multiple
                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                            hidden>

                        <button type="button" class="upload-trigger-btn" onclick="document.getElementById('submission_file').click()">
                            + Choose Files
                        </button>

                        <div id="filePreviewContainer" class="file-preview-container"></div>

                        <small class="upload-hint">
                            Accepted files: PDF, DOCX, JPG, PNG
                        </small>
                    </div>

                    <div class="form-actions-task">
                        <button type="submit" class="form-task-btn">
                            <i class="bi bi-send-check"></i>
                            Submit Task
                        </button>
                    </div>

                </form>

            </div>

            <!-- task modal card view -->
            <div class="view-task-container" id="view-task-container">

                <div class="modal-header">
                    <h3>Task Details</h3>
                    <button id="closeTaskViewModal" class="modal-close-profile">&times;</button>
                </div>
                <div class="task-status-message" id="taskStatusMessage"></div>
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
                                    <!-- <div class="info-item">
                                        <label>Status</label>
                                        <span id="modalTaskStatus" class="status-badge"></span>
                                    </div> -->

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

                            <div class="info-card" id="uploadedFilesCard" style="display:none;">

                                <div class="card-header" onclick="toggleSection(this)">
                                    <h4>Uploaded Files</h4>
                                    <span class="arrow">▼</span>
                                </div>

                                <div class="card-body">

                                    <div class="documents-grid" id="studentUploadedFiles">
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

                                    <div class="info-item" id="ratingSection" style="display:none;">
                                        <label>Supervisor Rating</label>
                                        <p id="modalSupervisorRating"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="task-modal-actions">
                        <button id="submitTaskBtn" class="submit-task-btn">
                            Submit Task
                        </button>
                        <button id="reSubmitTaskBtn" class="resubmit-task-btn">
                            Resubmit Task
                        </button>
                    </div>

                </div>
            </div>


            <!-- ojt progress modal -->
            <div class="view-progress-chart" id="view-progress-chart">
                <div class="modal-header">
                    <h3>OJT Progress</h3>
                    <button id="closeProgressViewModal" class="modal-close-profile">&times;</button>
                </div>


                <div id="hoursView" class="view-section">

                    <div class="progress-card">

                        <h3>OJT Progress</h3>

                        <div class="chart-wrapper">
                            <canvas id="progressChart"></canvas>
                        </div>

                        <div class="progress-summary">
                            <p class="progress-percent" id="progressPercent">
                                0% Completed
                            </p>

                            <p class="progress-text">
                                You're steadily progressing through your OJT requirements.
                            </p>
                        </div>

                    </div>

                </div>
            </div>

            <!-- notification modal -->
            <div class="notification-container" id="notification-container">
                <div class="modal-header">
                    <h3>Notifications</h3>

                    <button id="closeNotificationViewModal"
                        class="modal-close-profile">
                        &times;
                    </button>
                </div>

                <div class="notification-modal-content">

                    <?php
                    $alerts = getStudentAlerts($conn, $studentID);
                    ?>

                    <ul class="alerts-list scrollable">

                        <?php if (count($alerts) > 0): ?>

                            <?php foreach ($alerts as $alert): ?>

                                <li class="alert-item <?= htmlspecialchars($alert['type']) ?>">

                                    <div class="alert-content">

                                        <span class="alert-icon">
                                            <?php
                                            if ($alert['type'] === 'warning') {
                                                echo "⚠️";
                                            } elseif ($alert['type'] === 'danger') {
                                                echo "⏰";
                                            } elseif ($alert['type'] === 'critical') {
                                                echo "🚨";
                                            } else {
                                                echo "ℹ️";
                                            }
                                            ?>
                                        </span>

                                        <span class="alert-message">
                                            <?= htmlspecialchars($alert['message']) ?>
                                        </span>

                                    </div>

                                    <?php if (!empty($alert['action'])): ?>

                                        <button class="alert-btn"
                                            onclick="handleAlert('<?= $alert['action'] ?>','<?= $alert['id'] ?? '' ?>')">
                                            View
                                        </button>

                                    <?php endif; ?>

                                </li>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <li class="alert-item success">
                                🎉 No notifications right now.
                            </li>

                        <?php endif; ?>

                    </ul>

                </div>
            </div>

            <!-- attendance modal -->
            <!-- <div class="attendance-container" id="attendance-container">
                <div class="modal-header">
                    <h3>Attendance logs</h3>
                    <button id="closeAttendanceViewModal"
                        class="modal-close-profile">
                        &times;
                    </button>
                </div>

                <div class="top-bar">

                    <div class="top-header">
                        <h3 class="table-title"></h3>
                        <p>Monitor system actions and user activities.</p>
                    </div>

                    <div class="search-filter">
                        <i class="bi bi-search search-icon"></i>
                        <div class="filter-group">

                            <select id="moduleFilter">
                                <option value="">All Attendance</option>
                                <option value="present">Present</option>
                                <option value="late">Late</option>
                                <option value="excused">Excused</option>
                                <option value="absent">Absent</option>
                            </select>

                        </div>
                    </div>

                </div>

              <div class="table-container-attendance">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Status</th>
                                <th>Total Hours</th>
                                <th>State</th>
                            </tr>
                        </thead>

                        <tbody id="attendanceReportBody"> -->
            <?php
            // $category = $_POST['category'] ?? '';
            // echo renderStudentAttendanceTable($conn, $studentID, $category);
            ?>
            <!-- </tbody>

                    </table>

                 </div>

             </div> -->

            <!-- reports -->
            <div class="reports-container" id="reports-container">
                <div class="modal-header">
                    <h3>Internship Reports</h3>

                    <button id="closeReportsModal"
                        class="modal-close-profile">
                        &times;
                    </button>
                </div>



                <?php
                echo renderProgressHeader($conn, $studentID);
                echo renderReports($conn, $studentID);
                ?>

            </div>

            <!-- REPORT FORM PANEL -->
            <div class="report-form-panel" id="reportFormPanel">

                <div class="form-header">
                    <div>
                        <h4 id="formTitle">Submit Report</h4>
                        <small id="formSubtitle">Fill out the required details below</small>
                    </div>

                    <button onclick="closeReportForm()" class="close-btn">&times;</button>
                </div>

                <div class="form-body" id="reportForm">
                    <input type="hidden" id="report_type" name="report_type">
                    <input type="hidden" id="stageID" name="stageID">

                    <div class="context-box">

                        <div class="context-item">
                            <label>Report Type</label>
                            <span id="reportTypeLabel">-</span>
                        </div>

                        <div class="context-item">
                            <label>Stage</label>
                            <span id="stageLabel">-</span>
                        </div>

                    </div>

                    <div class="field">
                        <label>Report Title</label>
                        <input type="text"
                            id="reportTitle"
                            placeholder="e.g. Weekly Training Report - Week 1">
                    </div>

                    <div class="hint-box" id="reportHints">
                        <strong>What should you include?</strong>
                        <ul>
                            <li>Tasks you accomplished</li>
                            <li>Skills learned during this period</li>
                            <li>Challenges encountered</li>
                        </ul>
                    </div>

                    <div class="field">
                        <label>Report Content</label>
                        <textarea id="reportContent"
                            placeholder="Write your report in detail..."></textarea>
                    </div>

                    <div class="field">
                        <label>Attachment (optional)</label>

                        <input type="file" id="reportFile" hidden>

                        <button type="button"
                            class="upload-btn"
                            onclick="document.getElementById('reportFile').click()">
                            + Upload File
                        </button>

                        <div id="reportFilePreview" class="file-preview-container"></div>

                        <small class="upload-note">
                            PDF, DOCX, JPG, PNG allowed
                        </small>
                    </div>

                    <button class="submit-btn" id="submit-report">
                        Submit Report
                    </button>

                </div>

            </div>




            <!-- dashboard -->
            <section class="student-dashboard" id="student-dashboard">
                <section class="title-header">
                    <h2>Welcome, <?php echo htmlspecialchars($studentName); ?>!</h2>
                    <p>Here's your OJT progress and tasks.</p>
                </section>

                <section class="cards">
                    <div class="card pending-task" id="ojt-progress-btn">
                        <h3>OJT Progress</h3>
                        <p>Track your hours and training progress.</p>
                    </div>
                    <div class="card notification" id="notification-btn">
                        <h3>Notifications</h3>
                        <p>Check announcements from your mentor.</p>
                    </div>

                    <!-- <div class="card attendance" id="attendance-btn">
                        <h3>Attendance</h3>
                        <p>View your daily time-in and time-out logs.</p>
                    </div> -->

                    <div class="card submitted-task" id="reports-btn">
                        <h3>Reports</h3>
                        <p>Upload or review submitted reports.</p>
                    </div>
                </section>


                <section class="dashboard-charts">
                    <section class="wrapper line-chart">
                        <h2>Your Attendance Progress (Last 30 Days)</h2>
                        <p class="chart-subtitle">Track your daily attendance consistency</p>
                        <div class="attendance-legend">
                            <span class="present">
                                <i></i>
                                1.0 = Present
                            </span>
                            <span class="late">
                                <i></i>
                                0.5 = Late
                            </span>
                            <span class="absent">
                                <i></i>
                                0 = Absent
                            </span>
                        </div>
                        <canvas id="lineChart"></canvas>
                    </section>

                    <section class="wrapper pie-chart">
                        <h2>Task Completion Overview</h2>
                        <p class="chart-subtitle">
                            Summary of your task progress and status.
                        </p>
                        <canvas id="pieChart"></canvas>
                    </section>
                </section>
            </section>

            <!-- Tasks -->
            <section class="student-tasks" id="student-tasks">

                <div class="task-header">
                    <div class="title-header">
                        <h2>Your Tasks</h2>
                        <p>Submit your work and track approval status</p>
                    </div>

                    <!-- <div class="create-btn">
                        <button class="add-task-btn" id="submit-task-btn">
                            + Submit Task
                        </button>
                    </div> -->

                </div>

                <div class="task-filters">
                    <button class="active" data-filter="All">All</button>
                    <button data-filter="NOT STARTED">Not Started</button>
                    <button data-filter="SUBMITTED">In Progress</button>
                    <button data-filter="APPROVED">Approved</button>
                    <button data-filter="REJECTED">Rejected</button>
                </div>

                <div class="tasks-container" id="taskList">

                    <div class="task-card" data-taskid="1">

                    </div>

                </div>

            </section>

            <!-- student documents -->
            <section class="student-documents" id="student-documents">
                <div class="documents-wrapper">

                    <h2>Documents</h2>

                    <div class="info-section">
                        <h3>Personal Information</h3>

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Student ID</label>
                                <input type="text" value="<?php echo htmlspecialchars($studentInfo['studentID'] ?? ''); ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" value="<?php echo htmlspecialchars($studentInfo['name'] ?? ''); ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" value="<?php echo htmlspecialchars($studentInfo['email'] ?? ''); ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label>Birth Date</label>
                                <input type="text" value="<?php echo htmlspecialchars($studentInfo['birthDate'] ?? ''); ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label>Mobile Number</label>
                                <input type="text" value="<?php echo htmlspecialchars($studentInfo['mobileNumber'] ?? ''); ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label>Gender</label>
                                <input type="text" value="<?php echo htmlspecialchars($studentInfo['gender'] ?? ''); ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="info-section">
                        <h3>Academic Information</h3>

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Course</label>
                                <input type="text" value="<?php echo htmlspecialchars(
                                                                ($studentInfo['course'] ?? '') . ' - ' . ($studentInfo['course_name'] ?? '')
                                                            );  ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label>Year Level</label>
                                <input type="text" value="<?php echo htmlspecialchars($studentInfo['yearLevel'] ?? ''); ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label>Semester</label>
                                <input type="text" value="<?php echo htmlspecialchars($studentInfo['semester'] ?? ''); ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label>School Year</label>
                                <input type="text" value="<?php echo htmlspecialchars($studentInfo['schoolYear'] ?? ''); ?>" readonly>
                            </div>

                        </div>
                    </div>

                    <div class="info-section">
                        <h3>Address</h3>

                        <div class="form-group">
                            <label>Full Address</label>
                            <input type="text" value="<?php echo htmlspecialchars($studentInfo['address'] ?? ''); ?>" readonly>
                        </div>
                    </div>

                    <div class="info-section">
                        <h3>OJT Information</h3>

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Company Name</label>
                                <input type="text"
                                    value="<?php echo htmlspecialchars($studentInfo['company_name'] ?? 'Not Assigned'); ?>"
                                    readonly>
                            </div>

                            <div class="form-group">
                                <label>Assigned Supervisor</label>
                                <input type="text"
                                    value="<?php echo htmlspecialchars($studentInfo['supervisor_name'] ?? 'Not Assigned'); ?>"
                                    readonly>
                            </div>

                            <div class="form-group">
                                <label>Assigned Supervisor Position</label>
                                <input type="text"
                                    value="<?php echo htmlspecialchars($studentInfo['supervisor_position'] ?? 'Not Assigned'); ?>"
                                    readonly>
                            </div>

                            <div class="form-group">
                                <label>Supervisor Contact</label>
                                <input type="text"
                                    value="<?php echo htmlspecialchars($studentInfo['supervisor_number'] ?? 'Not Available'); ?>"
                                    readonly>
                            </div>

                            <div class="form-group">
                                <label>Internship Status</label>
                                <input type="text"
                                    value="<?php echo htmlspecialchars($studentInfo['assignment_status'] ?? 'Ongoing'); ?>"
                                    readonly>
                            </div>
                        </div>
                    </div>

                    <div class="info-section">
                        <h3>Uploaded Documents</h3>

                        <div class="documents-grid">

                            <div class="doc-card">
                                <p><strong>Student ID</strong></p>

                                <?php if (!empty($documents['idUpload'])): ?>
                                    <button class="btn-preview"
                                        onclick="previewFile('<?php echo '../../uploads/student_uploads/' . $studentID . '/' . $documents['idUpload']; ?>')">
                                        View ID
                                    </button>
                                <?php else: ?>
                                    <span class="status missing">No file uploaded</span>
                                <?php endif; ?>
                            </div>

                            <div class="doc-card">
                                <p><strong>Registration Form</strong></p>

                                <?php if (!empty($documents['regFormUpload'])): ?>
                                    <button class="btn-preview"
                                        onclick="previewFile('<?php echo '../../uploads/student_uploads/' . $studentID . '/' . $documents['regFormUpload']; ?>')">
                                        View Form
                                    </button>
                                <?php else: ?>
                                    <span class="status missing">No file uploaded</span>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>

                </div>
            </section>

            <section class="student-activity" id="student-activity">


                <div class="top-bar">

                    <div class="top-header">
                        <h3 class="table-title">Activity Logs</h3>
                        <p>Monitor system actions and user activities.</p>
                    </div>

                    <div class="search-filter">
                        <div class="search-container">
                            <i class="bi bi-search search-icon"></i>
                            <input type="text" id="studentActivityLogSearch"
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
                                echo renderStudentActivityLogTable($conn, $studentID, $search, $module, $dateFrom, $dateTo);
                                ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </section>

            <section class="student-attendance" id="student-attendance">

                <div class="top-bar">

                    <div class="top-header">
                        <h3 class="table-title">Attendance Logs</h3>
                        <p>Monitor and review your daily attendance records.</p>
                    </div>

                    <div class="search-filter">

                        <div class="filter-group">
                            <i class="bi bi-funnel"></i>
                            <select id="statusFilter">
                                <option value="">All Status</option>
                                <option value="present">Present</option>
                                <option value="late">Late</option>
                                <option value="excused">Excused</option>
                                <option value="absent">Absent</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <input type="date" id="attendanceDateFrom" title="From date">
                            <input type="date" id="attendanceDateTo" title="To date">
                        </div>

                        <div class="filter-group">
                            <button class="download-btn" onclick="openAttendanceDownloadModal()">
                                <i class="bi bi-download"></i>
                                Download Report
                            </button>
                        </div>

                    </div>

                </div>

                <div class="table-container-attendance">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time In</th>
                                <th>Breaks</th>
                                <th>Time Out</th>
                                <th>Status</th>
                                <th>Hours</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>

                        <tbody id="attendanceReportBody">
                            <?php
                            $category = $_POST['category'] ?? '';
                            $dateFromAttendance = $_POST['dateFromAttendance'] ?? '';
                            $dateToAttendance = $_POST['dateToAttendance'] ?? '';
                            echo renderStudentAttendanceTable($conn, $studentID, $category, $dateFromAttendance, $dateToAttendance);
                            ?>
                        </tbody>
                    </table>
                </div>

            </section>

            <!-- download attendance -->
            <div class="download-attendance-modal" id="download-attendance-modal">
                <div class="download-box">

                    <div class="download-header">

                        <div class="download-title">

                            <div class="icon-wrapper">
                                <i class="bi bi-calendar2-check"></i>
                            </div>

                            <div class="title-text">
                                <h2>Download Attendance Report</h2>
                                <p>Filter and export attendance records as PDF</p>
                            </div>

                        </div>

                        <button class="modal-close" onclick="closeAttendanceDownloadModal()">
                            <i class="bi bi-x-lg"></i>
                        </button>

                    </div>

                    <div class="download-body">

                        <div class="form-group">
                            <label><i class="bi bi-funnel"></i> Status</label>
                            <select id="downloadStatus">
                                <option value="">All Status</option>
                                <option value="present">Present</option>
                                <option value="late">Late</option>
                                <option value="excused">Excused</option>
                                <option value="absent">Absent</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label><i class="bi bi-calendar-event"></i> Date From</label>
                            <input type="date" id="downloadDateFrom">
                        </div>

                        <div class="form-group">
                            <label><i class="bi bi-calendar-event-fill"></i> Date To</label>
                            <input type="date" id="downloadDateTo">
                        </div>

                    </div>

                    <div class="download-footer">

                        <button class="confirm-btn" onclick="downloadAttendanceReport()">
                            <i class="bi bi-download"></i>
                            Download PDF
                        </button>

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
                        <div class="warning-icon">⏳</div>
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

        </main>
    </div>

    <footer class="footer">
        <p>© 2026 OJT Tracking System</p>
    </footer>

</body>
<script>
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

    window.onload = resetTimer;
    document.onmousemove = resetTimer;
    document.onkeypress = resetTimer;
    document.onclick = resetTimer;
    document.onscroll = resetTimer;
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../../js/student/studentDashboard.js"></script>

</html>