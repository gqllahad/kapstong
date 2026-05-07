<?php

session_start();
require_once("../kapstongConnection.php");
require_once("../functions.php");

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
    <title>OJT Student Dashboard</title>
    <link rel="stylesheet" href="../../css/student/studentDashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body>

    <header class="navbar">
        <h1>OJT Student Dashboard</h1>
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
                    <button id="student-tasks-btn"><i class="bi bi-journal-text"></i> My Tasks</button>
                </li>
                <li><button id="student-documents-btn"><i class="bi bi-file-earmark-text"></i>My Documents</button></li>
                <li><button><i class="bi bi-chat-left-text"></i> Messages</button></li>
            </ul>
        </aside>

        <main class="content">

            <div id="overlay" class="overlay"></div>

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
                                                    : '../../../uploads/default.jpg';
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

            <!-- dashboard -->
            <section class="student-dashboard" id="student-dashboard">
                <section class="title-header">
                    <h2>Welcome, <?php echo htmlspecialchars($studentName); ?>!</h2>
                    <p>Here's your OJT progress and tasks.</p>
                </section>

                <section class="cards">
                    <div class="card pending-task">
                        <h3>OJT Status</h3>
                        <p>View your current OJT assignments.</p>
                    </div>
                    <div class="card notification">
                        <h3>Notifications</h3>
                        <p>Check announcements from your mentor.</p>
                    </div>
                    <div class="card submitted-task">
                        <h3>Documents</h3>
                        <p>Upload or review submitted reports.</p>
                    </div>
                </section>


                <section class="dashboard-charts">
                    <section class="wrapper line-chart">
                        <h2>Line Chart (Users per Role)</h2>
                        <canvas id="lineChart"></canvas>
                    </section>

                    <section class="wrapper pie-chart">
                        <h2>Attendance Evaluation</h2>
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
                        <h3>Uploaded Documents</h3>

                        <div class="documents-grid">

                            <div class="doc-card">
                                <p><strong>Student ID</strong></p>

                                <?php if (!empty($documents['idUpload'])): ?>
                                    <button class="btn-preview"
                                        onclick="previewImage('<?php echo '../../uploads/student_uploads/' . $studentID . '/' . $documents['idUpload']; ?>')">
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
                                        onclick="previewImage('<?php echo '../../uploads/student_uploads/' . $studentID . '/' . $documents['regFormUpload']; ?>')">
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
            <div id="imagePreviewModal" class="image-modal">
                    <span id="closeImagePreview">&times;</span>
                    <img id="previewImg">
                </div>

        </main>
    </div>

    <footer class="footer">
        <p>© 2026 OJT Tracking System</p>
    </footer>

</body>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../../js/student/studentDashboard.js"></script>

</html>