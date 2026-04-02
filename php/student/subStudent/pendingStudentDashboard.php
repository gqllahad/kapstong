<?php

session_start();
require_once("../../kapstongConnection.php");
require_once("../../functions.php");

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");


if ($_SESSION['role'] !== "student") {
    header("Location: ../trackerMain.php");
    exit();
}

if ($_SESSION['isVerified'] !== "NOT VERIFIED" && $_SESSION['isVerified'] !== "PENDING") {
    header("Location: ../trackerMain.php");
    exit();
}

$studentID = $_SESSION['studentID'];
$documents = getStudentDocuments($conn, $studentID);
$studentName = $_SESSION['name'];
$studentStatus = $_SESSION['isVerified'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OJT Student Dashboard</title>
    <link rel="stylesheet" href="../../../css/student/studentDashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body>

    <header class="navbar">
        <h1>OJT Student Dashboard</h1>
        <button id="menuToggle">☰</button>
        <nav class="profile-menu" id="profileMenu" hidden>
            <a href="#">Profile</a>
            <hr style="width: 75%; text-align: left;">
            <a href="../../logoutPhase.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </nav>
    </header>

    <div class="layout">
        <aside class="sidebar">
            <ul class="menu">
                <li class="active">
                    <button><i class="bi bi-house"></i>Home</button>
                </li>
                <li>
                    <button><i class="bi bi-journal-text"></i>My Documents</button>
                </li>
                <li><button><i class="bi bi-chat-left-text"></i> Messages</button></li>
            </ul>
        </aside>

        <main class="content">

            <div id="overlay" class="overlay"></div>

            <section class="student-dashboard">
                <section class="page-header">
                    <h2>Welcome! <?php echo htmlspecialchars($studentName); ?>.</h2>
                    <p>Get started by verifying your account.</p>
                </section>

                <?php if ($studentStatus === 'NOT VERIFIED'): ?>
                    <div class="verify-banner" id="verify-banner">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div>
                            <strong>Verify Your Account</strong>
                            <p>You need to upload required documents and wait for admin approval to access all features.</p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($studentStatus === 'PENDING'): ?>
                    <div class="pending-banner" id="pending-banner">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div>
                            <strong>Your account is under review.</strong>
                            <p>Your documents have been submitted. Please wait for admin approval to access all features.</p>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="unverified-dashboard-cards">
                    <div class="unverified-card profile-card">
                        <h3>Profile Info</h3>
                        <p>Course: BSIT</p>
                        <p>Year: 3rd Year</p>
                        <button class="btn-edit" id="btn-edit">Edit Info</button>
                    </div>

                    <div class="unverified-card unverified-documents-card">
                        <h3>Documents</h3>

                        <?php if (!$documents): ?>
                            <p>No documents uploaded yet.</p>
                            <button class="btn-upload" id="btn-upload-now">Upload Now</button>
                        <?php else: ?>

                            <div class="document-item">
                                <div class="doc-preview">
                                    <img src="../../../uploads/student_uploads/<?php echo $studentID . '/' . $documents['idUpload']; ?>" class="preview-img">
                                </div>
                                <div class="doc-info">
                                    <p><strong>ID:</strong></p>
                                    <?php if ($documents['status'] === 'PENDING'): ?>
                                        <span class="status pending">Waiting for Approval</span>
                                    <?php elseif ($documents['status'] === 'APPROVED'): ?>
                                        <span class="status approved">Approved</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="document-item">
                                <div class="doc-preview">
                                    <img src="../../../uploads/student_uploads/<?php echo $studentID . '/' . $documents['regFormUpload']; ?>" class="preview-img">
                                </div>
                                <div class="doc-info">
                                    <p><strong>Registration Form:</strong></p>
                                    <?php if ($documents['status'] === 'PENDING'): ?>
                                        <span class="status pending">Waiting for Approval</span>
                                    <?php elseif ($documents['status'] === 'APPROVED'): ?>
                                        <span class="status approved">Approved</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Allow replace if not approved -->
                            <?php if ($documents['status'] !== 'APPROVED'): ?>
                                <button class="btn-upload" id="btn-upload-now">Change Files</button>
                            <?php endif; ?>

                        <?php endif; ?>
                    </div>

                    <div class="unverified-card unverified-notifications-card">
                        <h3>Announcements</h3>
                        <p>No new messages.</p>
                    </div>

                    <div class="unverified-card tips-card">
                        <h3>Need Help?</h3>
                        <p>If you need assistance, contact your OJT coordinator or click <a href="support.php">here</a>.</p>
                    </div>
                </div>


                <!-- <section class="cards">
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
            </section> -->

                <!-- <section class="dashboard-charts">
                <section class="wrapper line-chart">
                    <h2>Line Chart (Users per Role)</h2>
                    <canvas id="lineChart"></canvas>
                </section>

                <section class="wrapper pie-chart">
                    <h2>Attendance Evaluation</h2>
                    <canvas id="pieChart"></canvas>
                </section>
            </section> -->

                <!-- edit info modal  -->
                <div id="editModal" class="edit-modal">
                    <div class="modal-header">
                        <h3>Edit your Info</h3>
                        <button id="closeEditModal" class="modal-close">&times;</button>
                    </div>
                    <form action="edit_student_action.php" method="post" class="modal-form">

                        <!-- Personal Info -->
                        <div class="form-section">
                            <h4>Personal Information</h4>

                            <div class="form-group-edit">
                                <label for="fullName">Full Name</label>
                                <input type="text" name="fullName" id="fullName"
                                    value="<?php echo htmlspecialchars($studentName ?? ''); ?>" required>
                            </div>

                            <div class="form-group-edit">
                                <label for="email">Email Address</label>
                                <input type="email" name="email" id="email"
                                    value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>" readonly>
                            </div>

                            <div class="form-group-edit">
                                <label for="mobile">Mobile Number</label>
                                <input type="text" name="mobile" id="mobile"
                                    value="<?php echo htmlspecialchars($_SESSION['mobileNumber'] ?? ''); ?>" required>
                            </div>

                            <div class="form-group-edit">
                                <label for="birthDate">Birth Date</label>
                                <input type="date" name="birthDate" id="birthDate"
                                    value="<?php echo htmlspecialchars($student['birthDate'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <!-- Academic Info -->
                        <div class="form-section">
                            <h4>Academic Information</h4>

                            <div class="form-group-edit">
                                <label for="course">Course</label>
                                <input type="text" name="course" id="course"
                                    value="<?php echo htmlspecialchars($student['course'] ?? ''); ?>" required>
                            </div>

                            <div class="form-group-edit">
                                <label for="yearLevel">Year Level</label>
                                <select name="yearLevel" id="yearLevel" required>
                                    <option value="1st Year">1st Year</option>
                                    <option value="2nd Year">2nd Year</option>
                                    <option value="3rd Year">3rd Year</option>
                                    <option value="4th Year">4th Year</option>
                                </select>
                            </div>

                            <div class="form-group-edit">
                                <label for="gender">Gender</label>
                                <select name="gender" id="gender" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="form-section">
                            <h4>Address</h4>

                            <div class="form-group-edit">
                                <label for="address">Full Address</label>
                                <input type="text" name="address" id="address"
                                    value="<?php echo htmlspecialchars($student['address'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="modal-actions">
                            <button type="submit" class="btn-upload" name="editInfoStudent">Save Changes</button>
                            <button type="button" id="cancelEditModal" class="btn-edit">Cancel</button>
                        </div>
                    </form>
                </div>

                <!-- uploads modal -->
                <div id="uploadModal" class="upload-modal">
                    <div class="modal-header">
                        <h3>Upload Your Documents</h3>
                        <button id="closeModal" class="modal-close">&times;</button>
                    </div>
                    <form action="upload_documents_action.php" method="post" enctype="multipart/form-data" class="modal-form">
                        <div class="form-group">
                            <label for="idUpload">Student ID</label>
                            <input type="file" name="idUpload" id="idUpload" required>
                            <div class="file-preview" id="idPreview"></div>
                        </div>
                        <div class="form-group">
                            <label for="regFormUpload">Registration Form</label>
                            <input type="file" name="regFormUpload" id="regFormUpload" required>
                            <div class="file-preview" id="regPreview"></div>
                        </div>
                        <div class="modal-actions">
                            <button type="submit" class="btn-upload" name="submitDocuments">Upload</button>
                            <button type="button" id="cancelModal" class="btn-edit">Cancel</button>
                        </div>
                    </form>
                </div>



        </main>
    </div>

    <footer class="footer">
        <p>© 2026 OJT Tracking System</p>
    </footer>

</body>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../../../js/student/studentDashboard.js"></script>

</html>