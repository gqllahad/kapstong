<?php

session_start();
require_once("../Shared/kapstongConnection.php");
require_once("../auth/admin_auth.php");
require_once("../Shared/functions.php");
require_once("../Session/sessionTimeout.php");

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");


if ($_SESSION['role'] !== "ADMIN") {
    header("Location: ../Session/trackerMain.php");
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard</title>
    <link rel="icon" type="image/png" href="../../public/kapstongImage/logo.jpg">

    <link rel="stylesheet" href="../../public/css/admin/adminDashboard3.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

</head>


<body>
    <!--  MAIN LAYOUT -->
    <div class="layout">

        <!-- SIDEBAR -->
        <aside class="sidebar">

            <ul class="menu">
                <li><button id="admin-dashboard-btn"><i class="bi bi-house"></i> Home</button></li>
                <li><button id="admin-preparation-btn"> <i class="bi bi-gear"></i> System Configuration</button></li>
                <li><button id="admin-approval-btn"><i class="bi bi-people"></i>User Management</button></li>
                <li><button id="admin-attendance-btn"><i class="bi bi-calendar-check"></i>Attendance Logs</button></li>
                <li><button id="admin-reports-btn"> <i class="bi bi-graph-up"></i>Reports</button></li>
            </ul>

            <div class="sidebar-profile">

                <div class="profile-left">
                    <div class="profile-avatar">
                        A
                    </div>
                    <div class="profile-info">
                        <span class="profile-name">Admin</span>
                        <small>Administrator</small>
                    </div>
                </div>
                <button id="menuToggle" class="profile-trigger">
                    ☰
                </button>

                <nav class="profile-menu" id="profileMenu" hidden>
                    <a id="openAccountSettingsBtn">
                        <i class="bi bi-gear"></i>
                        Account Settings
                    </a>
                    <a id="darkModeToggle" class="dark-toggle">
                        <i class="bi bi-moon-fill"></i>
                        Dark Mode
                    </a>
                    <hr>
                    <a href="../Session/logoutPhase.php" onclick="localStorage.setItem('logout-event', Date.now())">
                        <i class="bi bi-box-arrow-right"></i>
                        Logout
                    </a>
                </nav>
            </div>

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

                        <div class="download-title">

                            <div class="icon-wrapper">
                                <i class="bi bi-person-badge"></i>
                            </div>

                            <div class="title-text">
                                <h2>Download Supervisor PDF</h2>
                                <p>Export supervisor records and assigned data into PDF format</p>
                            </div>

                        </div>

                        <button class="modal-close" id="closeAllSupervisorDownloadModal">
                            <i class="bi bi-x-lg"></i>
                        </button>

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

                        <div class="download-footer">
                            <button type="submit" class="confirm-btn">
                                Generate PDF
                            </button>
                        </div>

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

            <!-- reregister student rfid -->
            <div class="lost-rfid-register-modal" id="lost-rfid-register-modal">
                <div class="rfid-register-content">

                    <div class="modal-header">
                        <h3>Register RFID</h3>

                        <button id="closeLostRfidRegisterModal" onclick="closeRfidRegisterModal()"
                            class="modal-close">
                            &times;
                        </button>
                    </div>

                    <div class="rfid-register-body">

                        <input type="hidden" id="lostRfidStudentID">

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
                                id="lost_rfid_uid"
                                class="rfid-input"
                                placeholder="Tap RFID Card..."
                                autocomplete="off">

                        </div>

                        <button class="register-rfid-btn"
                            onclick="reRegisterRfid()">
                            Register RFID
                        </button>

                    </div>

                </div>
            </div>

            <!-- download students data -->
            <div class="download-all-student-modal" id="download-all-student-modal">

                <div class="download-content">

                    <div class="download-header">

                        <div class="download-title">

                            <div class="icon-wrapper">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </div>

                            <div class="title-text">
                                <h2>Download Student PDF</h2>
                                <p>Export selected student records into PDF format</p>
                            </div>

                        </div>

                        <button class="modal-close" id="closeAllStudentDownloadModal">
                            <i class="bi bi-x-lg"></i>
                        </button>

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

                        <div class="download-footer">
                            <button type="submit" class="confirm-btn">
                                Generate PDF
                            </button>
                        </div>

                    </form>

                </div>

            </div>

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

                        <div class="form-grid">
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
                        </div>
                    </form>
                </div>
            </div>

            <!-- supervisor view -->
            <div class="supervisor-view" id="supervisor-view">
            </div>

            <!-- supervisor assigned students -->
            <div class="supervisor-assigned-view" id="supervisor-assigned-view">
            </div>

            <!-- assign student-supervisor -->
            <div class="assign-student-container" id="assign-student-container">
                <div class="modal-header">
                    <h3>👥 Student–Supervisor Assignment</h3>
                    <div class="assign-footer">
                    <button class="assign-btn" id="assign-btn"> <i class="bi bi-person-plus"></i> Assign Student</button>
                    <button id="closeAssignModal" class="modal-close">&times;</button>
                </div>
                    
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

            </div>

            <!-- systemconfig modals -->

            <!-- ojt setup  inigo -->

            <div class="ojt-program-container" id="ojt-program-container">
                <div class="modal-header">
                    <h3>OJT Program Setup</h3>
                    <button id="closeOjtProgramModal" class="modal-close">&times;</button>
                </div>

                <div class="settings-management">
                    <div class="search-filter">

                        <div class="search-container">

                            <i class="bi bi-funnel search-icon"></i>

                            <select id="settingsFilter" onchange="filterSettingsPrograms()">
                                <?php echo renderDepartmentOptions($conn); ?>
                            </select>

                        </div>

                    </div>
                    <button class="settings-btn" onclick="openCreateSettingsModal()">
                        <span class="icon">+</span>
                        <span class="text">Add settings</span>
                    </button>
                </div>

                <div class="ojt-program-body">

                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Program</th>
                                        <th>Academic Year</th>
                                        <th>Semester</th>
                                        <th>Required Hours</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody id="filterSettingsBody">
                                    <?php echo renderOJTSettingsTable($conn); ?>
                                </tbody>
                            </table>
                        </div>
                </div>
            </div>

            <!-- edit ojt settings modal -->
            <div class="create-settings-modal" id="edit-settings-modal">
                <div class="modal-header">
                    <h3>Edit OJT Settings</h3>
                    <button id="closeEditSettingsModal" class="modal-close">&times;</button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="edit_settingID">

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Program</label>
                            <select id="edit_programID">
                                <?php echo renderProgramOptions($conn); ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Academic Year</label>
                            <input type="text" id="edit_academic_year" placeholder="Auto-filled from start date" readonly>
                        </div>

                        <div class="form-group">
                            <label>Semester</label>
                            <select id="edit_semester">
                                <option value="">Select semester</option>
                                <option value="1st Semester">1st Semester</option>
                                <option value="2nd Semester">2nd Semester</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Required OJT Hours</label>
                            <input type="number" id="edit_required_hours" placeholder="e.g. 600">
                        </div>

                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" id="edit_start_date">
                        </div>

                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" id="edit_end_date">
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select id="edit_status">
                                <option value="ACTIVE">Active</option>
                                <option value="INACTIVE">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="ojt-actions">
                        <button type="button" class="save-btn" onclick="updateOJTSettings()">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>

            <!-- ojt settings modal -->
            <div class="create-settings-modal" id="create-settings-modal">
                <div class="modal-header">
                    <h3>Create New Settings</h3>
                    <button id="closeCreateSettingsModal" class="modal-close">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Program</label>
                            <select id="settingProgram">
                                <?php echo renderProgramOptions($conn); ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Academic Year</label>
                            <input type="text" id="academicYear" placeholder="Auto-filled from start date" readonly>
                        </div>

                        <div class="form-group">
                            <label>Semester</label>
                            <select id="semester">
                                <option value="">Select semester</option>
                                <option value="1st Semester">1st Semester</option>
                                <option value="2nd Semester">2nd Semester</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Required OJT Hours</label>
                            <input type="number" id="requiredHours" placeholder="e.g. 600" min="1">
                        </div>

                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" id="settingStart">
                        </div>

                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" id="settingEnd">
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select id="settingStatus">
                                <option value="ACTIVE">Active</option>
                                <option value="INACTIVE">Inactive</option>
                            </select>
                        </div>
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
                    <div class="form-grid">
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
                    <div class="form-grid">
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
                                name="morning_time_out">
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


                    <div class="time-row">
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
                                min="0">
                        </div>
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


            <!-- evaluation modal -->
             <div id="evalPreviewModal" class="eval-preview-modal">
                <div class="modal-header">
                <h3>Final Evaluation</h3>
                <button id="closeEvalPreview" class="modal-close">&times;</button>
            </div>
                <div class="modal-box">
                    <div id="evalPreviewBody" class="eval-preview-body"></div>
                </div>
            </div>

            <!-- completion certificate -->
            <div class="certificate-complete" id="certificate-complete">

            <div class="modal-header">
                <h3>Certificate of Completion</h3>
                <button id="closeCertificateOfCompletionModal" class="modal-close">&times;</button>
            </div>

            <div class="cert-upload-wrap">

                <div class="cert-dropzone" id="certDropzone">
                    <input type="file" id="certFileInput" accept=".pdf,.png,.jpg,.jpeg" hidden>

                    <div class="cert-dropzone-empty" id="certEmptyState">
                        <i class="bi bi-file-earmark-arrow-up cert-upload-icon"></i>
                        <p class="cert-upload-title">Drop your certificate template here</p>
                        <p class="cert-upload-sub">PDF, PNG or JPG, up to 10MB</p>
                        <button type="button" class="cert-browse-btn" id="certBrowseBtn">Browse file</button>
                    </div>

                    <div class="cert-dropzone-preview" id="certPreviewState" style="display:none;">
                        <i class="bi bi-file-earmark-check cert-preview-icon"></i>
                        <div class="cert-preview-info">
                            <p class="cert-preview-name" id="certFileName"></p>
                            <p class="cert-preview-size" id="certFileSize"></p>
                        </div>
                        <div class="cert-preview-actions">
                            <button type="button" class="cert-replace-btn" id="certReplaceBtn">Replace</button>
                            <button type="button" class="cert-remove-btn" id="certRemoveBtn">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="cert-upload-footer">
                    <span class="cert-status" id="certStatus"></span>
                    <button type="button" class="cert-save-btn" id="certSaveBtn" disabled>Save Template</button>
                </div>

            </div>
            </div>

            <!-- delete template -->
             <div class="delete-cert-modal" id="certConfirmModal" style="display:none;">
                <div class="delete-cert-box">
                    <div class="delete-cert-icon">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <h3 class="delete-cert-title">Remove certificate template?</h3>
                    <p class="delete-cert-message">This will permanently delete the saved template. This can't be undone.</p>
                    <div class="modal-actions">
                        <button type="button" class="delete-cert-cancel" id="certModalCancel">Cancel</button>
                        <button type="button" class="delete-cert-confirm" id="certModalConfirm">Remove</button>
                    </div>
                </div>
            </div>

            <!-- viewall -->
            <div class="view-all-modal" id="view-all-modal">
                <div class="modal-header">
                    <h3>Student Progress</h3>
                    <button id="closeViewAllModal" class="modal-close">&times;</button>
                </div>

                 <div class="risk-toolbar">
                    <input type="text" id="riskSearch" placeholder="Search name, ID, or course…">
                    <select id="riskFilter" autocomplete="off">
                        <option value="">All students</option>
                        <option value="flagged">Flagged only</option>
                        <option value="BEHIND">Behind schedule</option>
                        <option value="AT RISK">At risk</option>
                        <option value="Not progressing">Not progressing</option>
                        <option value="ON TRACK">On track</option>
                    </select>
                </div>

                <div id="all-risk-list" class="risk-list"></div>

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


                    <div class="card for-review">

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

                    <!-- unassigned students-->
                    <div class="card unassigned-students">

                         <?php
                            $unassigned = countUnassignedStudents($conn);
                            $trend = getUnassignedTrend($conn);
                            ?>

                            <span class="card-badge attention">ATTENTION</span>

                            <div class="card-content">
                                <div class="card-left">

                                    <div class="card-top">
                                        <div class="card-icon">
                                            <i class="bi bi-person-fill-slash"></i>
                                        </div>

                                        <div>
                                            <h3>UNASSIGNED STUDENTS</h3>
                                            <p>Verified students with no supervisor assigned yet.</p>
                                        </div>
                                    </div>

                                    <span class="trend">
                                        <?= $trend ?> this week
                                    </span>

                                </div>

                                <h2><?= $unassigned ?></h2>
                            </div>

                    </div>

                    <div class="card all-students">

                        <?php
                        $students = countStudents($conn);
                        ?>

                        <span class="card-badge active">ACTIVE</span>

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

                            </div>

                            <h2><?= $students ?></h2>
                        </div>

                    </div>

                    <div class="card supervisors">
                        <?php
                        $supervisor = countSupervisors($conn);
                        ?>

                        <span class="card-badge verified">VERIFIED</span>

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

                            </div>

                            <h2><?= $supervisor ?></h2>
                        </div>
                    </div>

                    <!-- rfid manuals -->
                    <div class="card rfid-entry">

                        <?php
                        $rfidFlags = countRfidManualFlags($conn);
                        ?>

                        <span class="card-badge monitor">MONITOR</span>

                        <div class="card-content">
                            <div class="card-left">

                                <div class="card-top">
                                    <div class="card-icon">
                                        <i class="bi bi-credit-card-2-front-fill"></i>
                                    </div>

                                    <div>
                                        <h3>RFID MANUAL ENTRY</h3>
                                        <p>Students frequently logging in without RFID tap.</p>
                                    </div>
                                </div>


                            </div>

                            <h2><?= $rfidFlags ?></h2>
                        </div>

                    </div>

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
                                <p class="deadline-title">OJT Student Progress</p>
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

                            <button onclick="openEvaluationDownloadModal()">
                                <i class="bi bi-bar-chart-fill"></i>
                                <span>Download Reports</span>
                            </button>

                            <button onclick="openStudentAssign()">
                                <i class="bi bi-people-fill"></i>
                                <span>Assign Students</span>
                            </button>

                            <button onclick="openDownloadModal()">
                                <i class="bi bi-journal-text"></i>
                                <span>Attendance</span>
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
                <!-- <div id="imagePreviewModal" class="image-modal">
                    <span id="closeImagePreview">&times;</span>
                    <img id="previewImg">
                </div> -->

                <div id="imagePreviewModal" class="image-modal">
                    <span id="closeImagePreview">&times;</span>

                    <div id="previewContainer"></div>
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
                        <button class="create-btn" id="certificate-settings-btn">
                            <i class="bi bi-patch-check-fill icon"></i>
                            <span class="text">Certificate of Completion</span>
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

            <!-- Reports  -->
            <section class="admin-reports" id="admin-reports">
                <div class="user-management">
                    <div class="title-block">
                        <h2>Student Reports Management</h2>
                        <p>Monitor, review, and manage attendance records of all OJT students.</p>
                    </div>
                    <div class="user-action-btn">
                        <button class="download-btn" onclick="openEvaluationDownloadModal()">
                            <i class="bi bi-download"></i>
                            Download Evaluation
                        </button>
                    </div>
                </div>

                <div class="top-bar">

                    <div class="top-header">
                        <h3 class="table-title">Final Evaluation Records</h3>
                        <p>Search, filter, and review supervisor evaluations.</p>
                    </div>

                    <div class="search-filter">

                        <div class="search-container">
                            <i class="bi bi-search search-icon"></i>

                            <input
                                type="text"
                                id="evaluationSearch"
                                placeholder="Search by Student ID, Name, or Supervisor">
                        </div>

                        <div class="filter-group">
                            <select id="evaluationCourseFilter">
                                <?php echo getProgramOptions($conn); ?>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="table-container">

                    <table>

                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Supervisor</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody id="evaluationTableBody"></tbody>
                        <?php
                        $search = $_POST['search'] ?? '';
                        $course = $_POST['course'] ?? '';
                        ?>
                    </table>

                </div>

            </section>


            <!-- download evaluation  -->
            <div id="evaluation-download-modal" class="evaluation-download-modal">

                <div class="download-box">

                    <div class="download-header">

                        <div class="download-title">

                            <div class="icon-wrapper">
                                <i class="bi bi-clipboard-data"></i>
                            </div>

                            <div class="title-text">
                                <h2>Download Evaluation Report</h2>
                                <p>Export final evaluation records of OJT students</p>
                            </div>

                        </div>

                        <button class="modal-close"
                            onclick="closeEvaluationDownloadModal()">
                            <i class="bi bi-x-lg"></i>
                        </button>

                    </div>

                    <div class="download-body">

                        <div class="form-group">

                            <label>Course</label>
                            <select id="downloadCourseEvaluation">
                                <?php echo getProgramOptions($conn); ?>
                            </select>

                        </div>


                        <div class="form-group">

                            <label>Supervisor</label>
                            <select id="downloadSupervisorEvaluation">
                                <?php echo getSupervisorOptions($conn); ?>
                            </select>
                        </div>

                        <div class="modal-actions">
                            <button class="confirm-btn" onclick="downloadEvaluationPDF()">Download PDF</button>
                        </div>
                    </div>
                </div>

            </div>


            <!-- attendance logs -->
            <section class="admin-attendance" id="admin-attendance">
                <div class="user-management">
                    <div class="title-block">
                        <h2>Student Attendance Management</h2>
                        <p>Monitor, review, and manage attendance records of all OJT students.</p>
                    </div>

                    <div class="user-action-btn">
                        <button class="download-btn" onclick="openDownloadModal()">
                            <i class="bi bi-download"></i>
                            Download Attendance Report
                        </button>
                    </div>

                </div>

            <div class="table-view show" id="attendance-records">

                <div class="table-switcher">
                        <button type="button" class="tab-btn active" data-tab="attendance" onclick="showAttendanceRecords()" >
                            Attendance Records
                        </button>

                        <button type="button" class="tab-btn" data-tab="manual" onclick="showManualAttendance()">
                            Lost Card Alerts
                        </button>
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
                                placeholder="Search by Student ID, Name, Course, or RFID">
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
                                <?php echo getProgramOptions($conn); ?>
                            </select>

                        </div>

                        <div class="filter-group">

                            <select id="entryMethodFilter">
                                <option value="">All Method</option>
                                <option value="RFID">RFID</option>
                                <option value="MANUAL">MANUAL</option>
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
                                <th>Attendance Method</th>
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
                            $entry = $_POST['entry'] ?? '';
                            echo renderAdminStudentAttendance($conn, $search, $status, $dateFromAttendance, $dateToAttendance, $entry);
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>


            <!-- manuals -->
             <div class="table-view" id="manual-attention">
                 <div class="table-switcher">
                        <button type="button" class="tab-btn" data-tab="attendance" onclick="showAttendanceRecords()" >
                            Attendance Records
                        </button>

                        <button type="button" class="tab-btn active" data-tab="manual" onclick="showManualAttendance()">
                            Lost Card Alerts
                        </button>
                </div>

                <div id="manualWatchlistBody">
                    <?php
                        echo renderManualAttendanceWatchlist($conn);
                    ?>
                </div>
             </div>

            </section>

            <!-- download attendance modal -->
            <div id="attendance-download-modal" class="attendance-download-modal">

                <div class="download-box">

                    <div class="download-header">

                        <div class="download-title">

                            <div class="icon-wrapper">
                                <i class="bi bi-download"></i>
                            </div>

                            <div class="title-text">
                                <h2>Download Attendance Report</h2>
                                <p>Generate and export attendance data in PDF format</p>
                            </div>

                        </div>

                        <button class="modal-close" onclick="closeDownloadModal()">
                            <i class="bi bi-x-lg"></i>
                        </button>

                    </div>

                    <div class="download-body">
                        <div class="form-group">

                            <label>Supervisor</label>

                            <select id="downloadSupervisor">

                                <!-- <option value="">All Supervisors</option> -->

                               <?php echo getSupervisorOptions($conn); ?>
                            </select>

                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Course</label>

                                <select id="downloadCourse">

                                    <?php echo getProgramOptions($conn); ?>

                                </select>
                            </div>

                            <div class="form-group">

                                <label>Status</label>

                                <select id="downloadStatus">

                                    <option value="">All Status</option>
                                    <option value="present">Present</option>
                                    <option value="late">Late</option>
                                    <option value="excused">Excused</option>
                                    <option value="absent">Absent</option>

                                </select>

                            </div>



                            <div class="form-group">
                                <label>Date From</label>

                                <input type="date" id="downloadDateFrom">
                            </div>

                            <div class="form-group">
                                <label>Date To</label>

                                <input type="date" id="downloadDateTo">
                            </div>

                        </div>

                    </div>

                    <div class="download-footer">

                        <button class="confirm-btn"
                            onclick="downloadAttendanceReport()">

                            Download PDF
                        </button>

                    </div>

                </div>

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

    <hr>

    <!-- FOOTER -->
    <footer class="footer">
        <p>© 2026 OJT Tracking System</p>
    </footer>

</body>
<script>
    let inactivityTimer;
    let countdownTimer;
    let countdownValue = 60;
    let warningActive = false;

    const modal = document.getElementById("inactivityModal");
    const countdownEl = document.getElementById("countdown");

    function startCountdown() {

        countdownValue = 60;
        countdownEl.innerText = countdownValue;
        warningActive = false;

        modal.style.display = "flex";

        countdownTimer = setInterval(() => {
            warningActive = true;

            countdownValue--;
            countdownEl.innerText = countdownValue;

            if (countdownValue <= 10) {
                countdownEl.style.color = "#dc2626";
                countdownEl.style.transform = "scale(1.2)";
            }

            if (countdownValue <= 0) {
                clearInterval(countdownTimer);

                localStorage.setItem('logout-event', Date.now());
                window.location.href = "../Session/logoutPhase.php";
            }

        }, 1000);
    }

    function resetTimer() {
        if (warningActive) return;

        clearTimeout(inactivityTimer);
        clearInterval(countdownTimer);

        modal.style.display = "none";

        inactivityTimer = setTimeout(() => {
            startCountdown();
        }, 600000);
    }

    function stayLoggedIn() {
        warningActive = false;

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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- <script src="https://cdn.plot.ly/plotly-latest.min.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../../public/js/admin/adminDashboard.js"></script>


</html>