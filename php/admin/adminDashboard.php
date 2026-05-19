<?php

session_start();
require_once("../kapstongConnection.php");
require_once("../functions.php");

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");


if ($_SESSION['role'] !== "ADMIN") {
    header("Location: ../trackerMain.php");
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">


<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard</title>
    <link rel="icon" type="image/png" href="../../kapstongImage/logo.jpg">

    <link rel="stylesheet" href="../../css/admin/adminDashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">


</head>


<body>
    <header class="navbar">

        <div class="logo">
            <h2>OJT SYSTEM</h2>
        </div>

        <button id="menuToggle">☰</button>
        <nav class="profile-menu" id="profileMenu" hidden>
            <a href="#">Profile</a>
            <hr style="width: 75%; text-align: left;">
            <a href="../logoutPhase.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </nav>

    </header>

    <!--  MAIN LAYOUT -->
    <div class="layout">

        <!-- SIDEBAR -->
        <aside class="sidebar">

            <ul class="menu">
                <li><button id="admin-dashboard-btn"><i class="bi bi-house"></i> Home</button></li>
                <li><button id="admin-preparation-btn"><i class="bi bi-journal-text"></i> System Configuration</button></li>
                <li><button id="admin-approval-btn"><i class="bi bi-file-earmark-text"></i>User Management</button></li>
                <li><button id="admin-attendance-btn"><i class="bi bi-file-earmark-text"></i>Attendance Logs</button></li>
            </ul>

        </aside>


        <!-- MAIN -->
        <main class="content">
            <div id="toast"></div>

            <div id="overlay" class="overlay"></div>

            <!-- unverified accounts -->
            <div class="all-unverified-modal" id="all-unverified-modal">
                <div class="modal-header">
                    <h3>Unverified Students</h3>
                    <button id="closeAllUnverifiedModal" class="modal-close">&times;</button>
                </div>

                <div class="search-container">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" id="allUnverifiedSearch"
                        placeholder="Search by ID, Name, OR Email">
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Year</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody id="unverifiedTableBody">
                            <?php
                            $search = $_POST['search'] ?? '';
                            echo renderStudentTable($conn, 'student', 'NOT VERIFIED', $search);
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- supervisor accounts -->
            <div class="all-supervisor-modal" id="all-supervisor-modal">
                <div class="modal-header">
                    <h3>Supervisors</h3>

                    <div class="header-actions">
                        <button id="downloadAllSupervisorBtn" class="download-btn">
                            <i class="bi bi-download"></i>
                            Download All
                        </button>

                        <button id="closeAllSupervisorModal" class="modal-close">&times;</button>
                    </div>
                </div>

                <div class="search-container">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" id="allSupervisorSearch"
                        placeholder="Search by ID, Name, OR Email">
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Supervisor ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile Number</th>
                                <th>Handled Course</th>
                                <th>Company Name</th>
                                <th>Position</th>
                                <th>Date Joined</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody id="allSupervisorBody">
                            <?php
                            $search = $_POST['search'] ?? '';
                            echo renderSupervisorTable($conn, $search);
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- download supervisor data -->
             <div class="download-all-supervisor-modal" id="download-all-supervisor-modal">

                <div class="download-content">

                    <div class="download-header">
                        <h3>Download Supervisor PDF</h3>
                        <button class="modal-close" id="closeAllSupervisorDownloadModal">&times;</button>
                    </div>

                    <form action="functions/download_all_supervisor_pdf.php" method="GET">

                        <div class="form-group">
                            <label>Department</label>

                            <select name="department">
                                <option value="">All Departments</option>
                                <option value="COI">College of Informations</option>
                                <option value="CCJ">College of Criminal Justice</option>
                                <option value="CoI">College of Informatics</option>
                            </select>

                        </div>

                        <div class="form-group">
                            <label>Status</label>

                            <select name="status">
                                <option value="">All</option>
                                <option value="ACTIVE">Active</option>
                                <option value="COMPLETED">Completed</option>
                            </select>

                        </div>

                        <button type="submit" class="generate-btn">
                            Generate PDF
                        </button>

                    </form>

                </div>

            </div>

            <!-- student accounts -->
            <div class="all-student-modal" id="all-student-modal">
                <div class="modal-header">
                    <h3>Students</h3>

                    <div class="header-actions">
                        <button id="downloadAllStudentBtn" class="download-btn">
                            <i class="bi bi-download"></i>
                            Download All
                        </button>
                    <button id="closeAllStudentModal" class="modal-close">&times;</button>
                    </div>
                </div>

                <div class="search-container">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" id="allStudentSearch"
                        placeholder="Search by ID, Name, OR Email">
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Year</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody id="allStudentBody">
                            <?php
                            $search = $_GET['approval_search'] ?? '';
                            echo renderStudentTable($conn, 'student', 'VERIFIED', $search);
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>


            <!-- student rfid register -->
             <div class="rfid-register-modal" id="rfid-register-modal">
                <div class="rfid-register-content">

                    <div class="modal-header">
                        <h3>Register RFID</h3>

                        <button id="closeRfidRegisterModal"
                            class="modal-close">
                            &times;
                        </button>
                    </div>

                    <div class="rfid-register-body">

                        <input type="hidden" id="rfidStudentID">

                        <div class="rfid-icon">
                             <i class="bi bi-person-vcard"></i>
                        </div>

                        <h2 class="rfid-title">
                            Scan RFID Card
                        </h2>

                        <p class="rfid-subtitle">
                            Place the RFID card near the scanner
                        </p>

                        <div class="rfid-input-container">

                            <input type="text"
                                id="rfid_uid"
                                class="rfid-input"
                                placeholder="Tap RFID Card..."
                                autocomplete="off">

                        </div>

                        <button class="register-rfid-btn"
                            onclick="submitRFIDRegister()">
                            Register RFID
                        </button>

                    </div>

                </div>
             </div>

            <!-- download students data -->
             <div class="download-all-student-modal" id="download-all-student-modal">

                <div class="download-content">

                    <div class="download-header">
                        <h3>Download Student PDF</h3>
                        <button class="modal-close" id="closeAllStudentDownloadModal">&times;</button>
                    </div>

                    <form action="functions/download_all_students_pdf.php" method="GET">

                        <div class="form-group">
                            <label>Course</label>

                            <select name="course">
                                 <?php echo renderSelectOptions($conn, 'program'); ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Year Level</label>

                            <select name="year">
                               <?php echo renderSelectOptions($conn, 'year'); ?>
                            </select>
                        </div>

                        <button type="submit" class="generate-btn">
                            Generate PDF
                        </button>

                    </form>

                </div>

            </div>`

            <!-- student application modal -->
            <div class="student-application-view" id="student-application-view">
            </div>

            <!-- approve application modal -->
            <div class="student-application-approve" id="student-application-approve">
            </div>

            <!-- reject application modal -->
            <div class="student-application-reject" id="student-application-reject">
            </div>

            <!-- create supervisor modal -->
            <div class="supervisor-container" id="supervisor-container">
                <div class="modal-header">
                    <h3>➕ Create Supervisor</h3>
                    <button id="closeCreateSupervisorModal" class="modal-close">&times;</button>
                </div>

                <div class="supervisor-body">

                    <p class="modal-subtitle">
                        Add a new supervisor account. Fill in the details below.
                    </p>

                    <div id="formMessage" style="margin-bottom:10px; font-weight:bold;"></div>
                    <form id="createSupervisorForm" method="POST">

                        <div class="input-group">
                            <label>Full Name</label>
                            <input type="text" name="name" placeholder="Enter full name" required>
                        </div>

                        <div class="input-group">
                            <label>Email Address</label>
                            <input type="email" name="email" placeholder="Enter email address" required>
                        </div>

                        <div class="input-group">
                            <label>Mobile Number</label>
                            <input type="text" name="mobile" placeholder="09XXXXXXXXX" maxlength="11" required>
                        </div>

                        <div class="input-group">
                            <label>Department</label>
                            <select name="department" required>
                                <?php echo renderSelectOptions($conn, 'department'); ?>
                            </select>
                        </div>

                        <div class="input-group">
                            <label>Company Name</label>
                            <input type="text" name="company" placeholder="Enter company name" required>
                        </div>

                        <div class="input-group">
                            <label>Position</label>
                            <input type="text" name="position" placeholder="e.g. HR Manager / Team Lead">
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" id="confirmCreateSupervisor">
                            <label for="confirmCreateSupervisor">
                                I confirm that this supervisor will be added to the system.
                            </label>
                        </div>

                        <button type="submit" name="createSupervisor" id="createSupervisorBtn" disabled class="primary-btn">
                            Create Supervisor
                        </button>

                    </form>
                </div>
            </div>

            <!-- supervisor view -->
            <div class="supervisor-view" id="supervisor-view">
            </div>

            <!-- assign student-supervisor -->
            <div class="assign-student-container" id="assign-student-container">
                <div class="modal-header">
                    <h3>👥 Student–Supervisor Assignment</h3>
                    <button id="closeAssignModal" class="modal-close">&times;</button>
                </div>

                <div class="assign-body">

                    <div class="assign-column">
                        <h4>Students</h4>
                        <input type="text" id="studentAssignSearch" placeholder="Search student...">
                        <div class="list-box" id="studentList">
                            <?php echo renderAssignStudentList($conn); ?>
                        </div>
                    </div>

                    <div class="assign-column">
                        <h4>Supervisors</h4>
                        <input type="text" id="supervisorAssignSearch" placeholder="Search supervisor...">
                        <div class="list-box" id="supervisorList">
                            <?php echo renderAssignSupervisorList($conn); ?>
                        </div>
                    </div>

                </div>

                <div class="assign-footer">
                    <button class="assign-btn" id="assign-btn">Assign Student</button>
                </div>

            </div>

            <!-- systemconfig modals -->

            <!-- ojt setup -->
            <div class="ojt-program-container" id="ojt-program-container">
                <div class="modal-header">
                    <h3>OJT Program Setup</h3>
                    <button id="closeOjtProgramModal" class="modal-close">&times;</button>
                </div>

                <div class="ojt-program-body">

                    <div class="current-ojt-card" id="activeOJTContainer">
                        <?php echo renderActiveOJTCard($conn); ?>
                    </div>

                    <div class="form-group">
                        <label>Academic Year</label>
                        <input type="text" id="academicYear" placeholder="e.g. 2026 - 2027"  pattern="\d{4}\s*-\s*\d{4}">
                    </div>

                    <div class="form-group">
                        <label>Required OJT Hours</label>
                        <input type="number" id="requiredHours" placeholder="e.g. 600">
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select id="status">
                            <option value="ACTIVE">Active</option>
                            <option value="INACTIVE">Inactive</option>
                        </select>
                    </div>

                    <div class="ojt-actions">
                        <button type="button" onclick="saveOJTSettings()" class="save-btn">
                            Save Setup
                        </button>
                    </div>

                </div>
            </div>

            <!-- deparment management -->

            <div class="department-management-modal" id="department-management-modal">
                <div class="modal-header">
                    <h3>Department Management</h3>
                    <button id="closeDepartmentManagementModal" class="modal-close">&times;</button>
                </div>

                <div class="department-body">

                    <input type="hidden" id="program_id">

                    <div class="form-group">
                        <label>Program Name</label>
                        <input
                            type="text"
                            id="prg_name"
                            placeholder="e.g. Bachelor of Science in Information Technology">
                    </div>

                    <div class="form-group">
                        <label>Program Code</label>
                        <input
                            type="text"
                            id="prg_acro"
                            placeholder="e.g. BSIT">
                    </div>

                    <div class="form-group">
                        <label>Department Name</label>
                        <select id="prg_department" onchange="updateDepartmentCode()">
                            <?php echo renderDepartmentOptions($conn); ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Department Code</label>
                        <input
                            type="text"
                            id="prg_department_code"
                            placeholder="e.g. CCS"
                            readonly>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select id="prg_status">
                            <option value="ACTIVE">Active</option>
                            <option value="INACTIVE">Inactive</option>
                        </select>
                    </div>

                    <div class="department-actions">
                        <button class="save-btn" onclick="updateProgram()">
                            Save Program
                        </button>
                    </div>

                </div>
            </div>


            <div class="department-management-container" id="department-management-container">
                <div class="modal-header">
                    <h3>Department Management</h3>
                    <button id="closeDepartmentManagement" class="modal-close">&times;</button>
                </div>


            <div class="user-management">
                <div class="search-filter">

                    <div class="search-container">

                        <i class="bi bi-funnel search-icon"></i>

                        <select id="departmentFilter" onchange="filterPrograms(this.value)">
                            <?php echo renderDepartmentOptions($conn); ?>
                        </select>

                    </div>

                </div>
                    <button class="assign-student-btn" onclick="openCreateCourseModal()">
                        <span class="icon">+</span>
                        <span class="text">Create Course</span>
                    </button>
                </div>
                

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Program Name</th>
                                <th>Code</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody id="filterProgramsBody">
                            <?php
                            echo renderDepartmentManagementTable($conn);
                            ?>
                        </tbody>
                    </table>
                </div>
                <!-- Dept	Code	Head	Programs	Status	Action -->
            </div>

            <div class="create-course-modal" id="create-course-modal">
                <div class="modal-header">
                    <h3>Create Course</h3>
                    <button id="closeCreateCourseModal" class="modal-close">&times;</button>
                </div>

                 <div class="modal-body">

                    <div class="form-group">
                        <label>Program Name</label>
                        <input type="text" id="prg_name_create" placeholder="e.g. Bachelor of Science in Information Technology">
                    </div>

                    <div class="form-group">
                        <label>Program Acronym</label>
                        <input type="text" id="prg_acro_create" placeholder="e.g. BSIT">
                    </div>

                    <div class="form-group">
                        <label>Department</label>
                        <input type="text" id="prg_department_create" placeholder="e.g. College of Computing">
                    </div>

                    <div class="form-group">
                        <label>Department Code</label>
                        <input type="text" id="prg_department_code_create" placeholder="e.g. CCIS">
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select id="prg_status_create">
                            <option value="ACTIVE">ACTIVE</option>
                            <option value="INACTIVE">INACTIVE</option>
                        </select>
                    </div>

                    <div class="modal-actions">
                        <button type="button" onclick="saveCourse()" class="save-btn">
                            Save Course
                        </button>
                    </div>

                </div>

            </div>

            <!-- rfid attendance -->
            <div class="rfid-attendance-container" id="rfid-attendance-container">
                <div class="modal-header">
                    <h3>RFID Attendance</h3>
                    <button id="closeRfidAttendanceModal" class="modal-close">&times;</button>
                </div>

                <div class="rfid-body">

                    <div class="form-group">
                        <label for="rfid_enabled">Enable RFID Attendance</label>

                        <select id="rfid_enabled" name="rfid_enabled">
                            <option value="1">Enabled</option>
                            <option value="0">Disabled</option>
                        </select>
                    </div>

                     <div class="time-row">
                        <div class="form-group">
                            <label>Morning Time In</label>
                            <input type="time" id="morning_time_in" name="morning_time_in">
                        </div>

                        <div class="form-group">
                            <label for="morning_time_out">
                                Morning Time Out
                            </label>

                            <input
                                type="time"
                                id="morning_time_out"
                                name="morning_time_out"
                            >
                        </div>
                    </div>

                     <div class="time-row">

                        <div class="form-group">
                            <label for="afternoon_time_in">Afternoon Time In</label>
                            <input type="time" id="afternoon_time_in" name="afternoon_time_in">
                        </div>

                        <div class="form-group">
                            <label for="afternoon_time_out">Afternoon Time Out</label>
                            <input type="time" id="afternoon_time_out" name="afternoon_time_out">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="late_time">Late threshold</label>
                        <input type="time" id="late_time" name="late_time">
                    </div>

                     <div class="form-group">
                        <label for="allowed_late_minutes">
                            Allowed Late Minutes
                        </label>

                        <input
                            type="number"
                            id="allowed_late_minutes"
                            name="allowed_late_minutes"
                            placeholder="e.g. 15"
                            min="0"
                        >

                        <small style="margin:10px;">
                            Students arriving after the allowed minutes
                            will automatically be marked late.
                        </small>
                    </div>

                   <div class="rfid-actions">
                        <button type="button" class="save-btn" onclick="saveRfidSettings()">
                            Save Settings
                        </button>
                    </div>

                </div>

                <!-- | Device ID | Location | Status | Last Sync | -->
            </div>

            <!-- evaluation  -->
            <div class="evaluation-settings-container" id="evaluation-settings-container">
                <div class="modal-header">
                    <h3>Evaluation Settings</h3>
                    <button id="closeEvaluationSettingsModal" class="modal-close">&times;</button>
                </div>

                <div class="evaluation-body">
                    <div class="current-eval-card">
                        <?php echo renderActiveEvaluationCard($conn); ?>
                    </div>

                    <div class="form-group">
                        <label>Evaluation Criteria (%)</label>

                        <div class="criteria-box">

                            <div class="criteria-item">
                                <label>Attendance (%)</label>
                                <input type="number" id="attendanceWeight" placeholder="e.g. 20">
                            </div>

                            <div class="criteria-item">
                                <label>Performance (%)</label>
                                <input type="number" id="progressWeight" placeholder="e.g. 30">
                            </div>

                            <div class="criteria-item">
                                <label>Task Completion (%)</label>
                                <input type="number" id="taskWeight" placeholder="e.g. 50">
                            </div>

                        </div>
                    </div>

                    <div class="evaluation-actions">
                        <button class="save-btn" onclick="saveEvaluationSettings()">Save Settings</button>
                    </div>

                </div>
            </div>

            <!-- requirements setup -->
            <div class="requirements-setup-container" id="requirements-setup-container">
                <div class="modal-header">
                    <h3>Requirements Setup</h3>
                    <button id="closeRequirementsSetupModal" class="modal-close">&times;</button>
                </div>

                <div class="requirements-body">

                    <div class="form-group">
                        <label>Requirement Name</label>
                        <input type="text" placeholder="e.g. Resume / CV">
                    </div>

                    <div class="form-group">
                        <label>Requirement Type</label>
                        <select>
                            <option value="DOCUMENT">Document</option>
                            <option value="FORM">Form</option>
                            <option value="REPORT">Report</option>
                            <option value="OTHER">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Submission Stage</label>
                        <select>
                            <option value="PRE-OJT">Pre-OJT</option>
                            <option value="DURING-OJT">During OJT</option>
                            <option value="POST-OJT">Post-OJT</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Required?</label>
                        <select>
                            <option value="YES">Yes (Required)</option>
                            <option value="NO">No (Optional)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Description / Instructions</label>
                        <textarea placeholder="Provide instructions for students..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Deadline (if applicable)</label>
                        <input type="date">
                    </div>

                    <div class="requirements-actions">
                        <button class="save-btn">Add Requirement</button>
                        <button class="cancel-btn">Cancel</button>
                    </div>

                </div>
            </div>

            <!-- viewall -->
            <div class="view-all-modal" id="view-all-modal">
                <div class="modal-header">
                    <h3>Intern Progress</h3>
                    <button id="closeViewAllModal" class="modal-close">&times;</button>
                </div>

                <div class="view-all-container">
                        <div id="all-risk-list" class="risk-list"></div>
                </div>
            

            </div>




            <!-- dashboard -->
            <section class="admin-dashboard" id="admin-dashboard">

                <section class="cards">
                    <div class="card unverified-students">

                        <?php
                        $unverifiedStudents = countUnverifiedStudents($conn);
                        $unTrend = getTrend($conn, 'student', 'NOT VERIFIED');
                        $unBadge = getBadge($unverifiedStudents);
                        ?>

                        <span class="card-badge"><?= $unBadge ?></span>

                        <div class="card-content">

                        <div class="card-left">

                                <div class="card-top">
                                     <div class="card-icon">
                                        <i class="bi bi-person-badge-fill"></i>
                                    </div>

                                    <div>
                                         <h3>UNVERIFIED STUDENT</h3>
                                         <p>Awaiting verification</p>
                                    </div>
                                </div>

                                <span class="trend">
                                    <?= $unTrend ?> this week
                                </span>

                            </div>

                            <h2><?= $unverifiedStudents ?></h2>
                        </div>
                    </div>

                
                    <div class="card students">

                        <?php
                            $pendingStudents = countPendingStudents($conn);
                            $pendingTrend = getTrend($conn, 'student', 'PENDING');
                            $pendingBadge = getBadge($pendingStudents);
                            ?>

                        <span class="card-badge"><?= $pendingBadge ?></span>

                        <div class="card-content">

                            <div class="card-left">

                                <div class="card-top">
                                    <div class="card-icon">
                                        <i class="bi bi-hourglass-split"></i>
                                    </div>

                                    <div>
                                        <h3>FOR REVIEW</h3>
                                        <p>Students awaiting approval and verification.</p>
                                    </div>
                                </div>

                                <span class="trend">
                                    <?= $pendingTrend ?> this week
                                </span>

                            </div>

                            <h2><?= $pendingStudents ?></h2>

                        </div>
                    </div>

                    <div class="card unverified-supervisors">

                        <?php
                        $students = countStudents($conn);
                        $trend = getTrend($conn, 'student', 'VERIFIED');
                        $badge = getBadge($students);
                        ?>

                        <span class="card-badge"><?= $badge ?></span>

                        <div class="card-content">
                            <div class="card-left">

                                <div class="card-top">
                                    <div class="card-icon">
                                        <i class="bi bi-mortarboard-fill"></i>
                                    </div>

                                    <div>
                                         <h3>STUDENTS</h3>
                                         <p>Total number of verified and active students.</p>
                                    </div>
                                </div>

                                <span class="trend">
                                    <?= $trend ?> this week
                                </span>

                            </div>

                            <h2><?= $students ?></h2>
                        </div>

                    </div>

                    <div class="card supervisors">
                        <?php
                        $supervisor = countSupervisors($conn);
                        $superTrend = getTrend($conn, 'supervisor', 'VERIFIED');
                        $superBadge = getBadge($supervisor);
                        ?>

                        <span class="card-badge"><?= $superBadge ?></span>

                        <div class="card-content">
                            <div class="card-left">

                                <div class="card-top">
                                    <div class="card-icon">
                                        <i class="bi bi-briefcase-fill"></i>
                                    </div>

                                    <div>
                                         <h3>SUPERVISORS</h3>
                                        <p>Total number of verified supervisors.</p>
                                    </div>
                                </div>

                                <span class="trend">
                                    <?= $superTrend ?> this week
                                </span>

                            </div>

                            <h2><?= $supervisor ?></h2>
                        </div>
                    </div>

                    <!-- <div class="card rfid-card" onclick="openRfid()">

                        <div class="card-content">

                            <div class="card-left">

                                <div class="card-top">

                                    

                                    <div class="rfid-text">
                                        <h3>RFID Attendance</h3>
                                        <p>Start real-time scanning for student attendance tracking</p>
                                    </div>
                                </div>

                                <span class="trend">
                                    Live scanning mode
                                </span>

                            </div>
                        </div>

                    </div> -->

                </section>

                <section class="dashboard-layout">

                    <section class="wrapper line-chart">

                        <div class="chart-header">

                            <div class="chart-title">
                                <h2>Attendance trend</h2>
                                <p>Monitor attendance records by month</p>
                            </div>

                            <div class="chart-actions">

                                <select id="monthSelector">
                                </select>

                                <button id="downloadChartBtn">
                                    <i class="bi bi-download"></i>
                                    Download CSV
                                </button>

                            </div>

                        </div>

                        <canvas id="lineChart"></canvas>

                    </section>

                    <section class="wrapper pie-chart-card">

                        <div class="pie-header">

                            <div class="pie-title">
                                <h2>Attendance Overview</h2>
                                <p>Weekly attendance performance summary</p>
                            </div>

                            <div class="attendance-badge">
                                Last 7 Days
                            </div>

                        </div>

                        <div class="pie-content">

                            <div class="pie-canvas-wrapper">
                                <canvas id="pieChart"></canvas>
                            </div>

                            <div class="pie-summary">

                                <div class="summary-card">
                                    <span class="summary-label">Attendance Health</span>
                                    <h3 id="health-score">--%</h3>
                                </div>

                                <div class="summary-card">
                                    <span class="summary-label">Total Attendance</span>
                                    <h4 id="total-attendance">--</h4>
                                </div>

                                <div class="attendance-legend">

                                    <div class="legend-item">
                                        <span class="legend-dot present"></span>
                                        Present
                                    </div>

                                    <div class="legend-item">
                                        <span class="legend-dot late"></span>
                                        Late
                                    </div>

                                    <div class="legend-item">
                                        <span class="legend-dot absent"></span>
                                        Absent
                                    </div>

                                    <div class="legend-item">
                                        <span class="legend-dot excused"></span>
                                        Excused
                                    </div>

                                </div>

                            </div>

                        </div>

                    </section>

                    <section class="deadline-container">

                        <div class="deadline-header">
                            <div>
                                <p class="deadline-title">Intern Progress</p>
                                <span class="deadline-subtitle">
                                    Monitor intern completion status and risk levels
                                </span>
                            </div>

                            <button class="view-all-btn" id="view-all-btn">
                                View All Students
                            </button>
                        </div>

                        <div id="risk-list" class="risk-list"></div>

                    </section>

                    <section class="quick-action-container">
                        <div class="quick-action-header">
                            <div class="quick-action-title">
                                <h2>Quick Actions</h2>
                                <p>Instant system tools & shortcuts</p>
                            </div>
                        </div>

                         <div class="quick-action-grid">
                            <button onclick="openAddSupervisor()">
                                <i class="bi bi-person-plus-fill"></i>
                                <span>Add Supervisor</span>
                            </button>

                            <button onclick="openRfid()">
                               <i class="bi bi-broadcast-pin"></i>
                                <span>RFID</span>
                            </button>

                            <button onclick="openRFIDSettings()">
                                <i class="bi bi-gear-fill"></i>
                                <span>Settings</span>
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

                </section>
                 
            </section>

            <!-- application approvals -->
            <section class="admin-approval" id="admin-approval">

                <div class="user-management">
                    <div class="title-block">
                        <h2>User Management</h2>
                        <p>Manage supervisors and user accounts in the system</p>
                    </div>

                    <div class="user-action-btn">
                        <button class="assign-student-btn" id="assign-student-btn">
                            <span class="icon">
                                <i class="bi bi-arrow-right-circle"></i>
                            </span>
                            <span class="text">Assign Student to Supervisor</span>
                        </button>

                        <button class="supervisor-btn" id="supervisor-btn">
                            <span class="icon">
                                <i class="bi bi-person-plus-fill"></i>
                            </span>
                            <span class="text">Create Supervisor</span>
                        </button>
                    </div>
                </div>


                <div class="approval-grid">

                    <!-- Unverified Students -->
                    <div class="approval-card unverified-student">
                        <h3>Unverified Students</h3>
                        <button class="view-btn" id="viewAllUnverifiedBtn">View All Unverified Students</button>
                    </div>

                    <!-- All Students -->
                    <div class="approval-card student">
                        <h3>All Students</h3>
                        <button class="view-btn" id="viewAllStudentsBtn">View All Students</button>
                    </div>

                    <!-- All Supervisors -->
                    <div class="approval-card supervisor">
                        <h3>All Supervisors</h3>
                        <button class="view-btn" id="viewAllSupervisorBtn">View All Supervisors</button>
                    </div>

                </div>

                <!-- pendings -->
                <div id="approvalTableContainer" style="margin-top:20px;">

                    <div class="top-bar">

                        <div class="top-header">
                            <h3 class="table-title">Pending Student Applications</h3>
                            <p>Search and review students waiting for approval.</p>
                        </div>

                        <div class="search-filter">
                            <div class="search-container">
                                <i class="bi bi-search search-icon"></i>
                                <input type="text" id="approvalSearch"
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
                                    <th>Email</th>
                                    <th>Course</th>
                                    <th>Year</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody id="approvalTableBody">
                                <?php
                                $search = $_POST['search'] ?? '';
                                echo renderApprovalTable($conn, 'student', 'PENDING', $search);
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- image preview -->
                <div id="imagePreviewModal" class="image-modal">
                    <span id="closeImagePreview">&times;</span>
                    <img id="previewImg">
                </div>

            </section>

            <!-- System Configuration -->
            <section class="admin-preparation" id="admin-preparation">
                <div class="title-block-bot">
                    <h2>System Configuration</h2>
                    <p>Manage system settings, courses, batches, and rules</p>

                </div>
                <div class="preparation-grid">
                    <div class="create-section-container">
                        <button class="create-btn" id="ojt-program-btn">
                            <i class="bi bi-gear-fill icon"></i>
                            <span class="text">OJT Program
                                Setup</span>
                        </button>
                    </div>

                    <div class="create-section-container">
                        <button class="create-btn" id="department-management-btn">
                             <i class="bi bi-building icon"></i>
                            <span class="text">Department Management</span>
                        </button>
                    </div>

                    <div class="create-section-container">
                        <button class="create-btn" id="rfid-attendance-btn">
                            <i class="bi bi-wifi icon"></i>
                            <span class="text">RFID Attendance Configuration</span>
                        </button>
                    </div>

                    <div class="create-section-container">
                        <button class="create-btn" id="evaluation-settings-btn">
                            <i class="bi bi-star-fill icon"></i>
                            <span class="text">Evaluation Settings</span>
                        </button>
                    </div>

                    <div class="create-section-container">
                        <button class="create-btn" id="requirements-setup-btn">
                            <i class="bi bi-file-earmark-text icon"></i>
                            <span class="text">Requirements Setup</span>
                        </button>
                    </div>

                </div>

                <div class="top-bar">

                    <div class="top-header">
                        <h3 class="table-title">Activity Logs</h3>
                        <p>Monitor system actions and user activities.</p>
                    </div>

                    <div class="search-filter">
                        <div class="search-container">
                            <i class="bi bi-search search-icon"></i>
                            <input type="text" id="activityLogSearch"
                                placeholder="Search by user, action or target">
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
                                    <th>Action</th>
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
                                echo renderActivityLogTable($conn, $search, $module, $dateFrom, $dateTo);
                                ?>
                            </tbody>
                        </table>
                    </div>

                </div>

            </section>


            <!-- attendance logs -->
             <section class="admin-attendance" id="admin-attendance">
               <div class="title-block-bot">
                    <h2>Student Attendance Management</h2>
                    <p>Monitor, review, and manage attendance records of all OJT students.</p>
                </div>

                <div class="top-bar">

                    <div class="top-header">
                        <h3 class="table-title">Attendance Records</h3>
                        <p>Search, filter, and review attendance logs across all students.</p>
                    </div>

                    <div class="search-filter">

                        <div class="search-container">
                            <i class="bi bi-search search-icon"></i>

                            <input 
                                type="text" 
                                id="studentAttendanceSearch"
                                placeholder="Search by Student ID, Name, Course, or RFID"
                            >
                        </div>

                        <div class="filter-group">

                            <select id="attendanceStatusFilter">
                                <option value="">All Status</option>
                                <option value="PRESENT">Present</option>
                                <option value="LATE">Late</option>
                                <option value="ABSENT">Absent</option>
                                <option value="EXCUSED">Excused</option>
                            </select>

                        </div>

                        <div class="filter-group">

                            <select id="attendanceCourseFilter">
                                <option value="">All Courses</option>
                                <option value="BSCS">BSCS</option>
                                <option value="BSIT">BSIT</option>
                                <option value="BSA">BSA</option>
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
                                <th>Student</th>
                                <th>Course</th>
                                <th>RFID</th>
                                <th>Date</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Status</th>
                                <th>Total Hours</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>

                        <tbody id="studentAttendanceBody">
                                <?php
                                $search = $_POST['search'] ?? '';
                                $status = $_POST['status'] ?? '';
                                $course = $_POST['course'] ?? '';
                                $dateFromAttendance = $_POST['dateFromAttendance'] ?? '';
                                $dateToAttendance = $_POST['dateToAttendance'] ?? '';
                                echo renderAdminStudentAttendance($conn, $search, $status, $dateFromAttendance, $dateToAttendance);
                                ?>
                            </tbody>
                        </table>
                    </div>

             </section>

        </main>

    </div>

    <hr>

    <!-- FOOTER -->
    <footer class="footer">
        <p>© 2026 OJT Tracking System</p>
    </footer>

</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- <script src="https://cdn.plot.ly/plotly-latest.min.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../../js/admin/adminDashboard.js"></script>


</html>