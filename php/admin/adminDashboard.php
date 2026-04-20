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

    <title>OJT MAIN PAGE</title>

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
            </ul>

        </aside>


        <!-- MAIN -->
        <main class="content">
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
                    <button id="closeAllSupervisorModal" class="modal-close">&times;</button>
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
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Year</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody id="allSupervisorBody">
                            <?php
                            $search = $_POST['search'] ?? '';
                            echo renderStudentTable($conn, 'supervisor', 'VERIFIED', $search);
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- student accounts -->
            <div class="all-student-modal" id="all-student-modal">
                <div class="modal-header">
                    <h3>Students</h3>
                    <button id="closeAllStudentModal" class="modal-close">&times;</button>
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
                            <input type="text" name="mobile" placeholder="09XXXXXXXXX" required>
                        </div>

                        <div class="input-group">
                            <label>Department</label>
                            <select name="department" required>
                                <option value="">Select Department</option>
                                <option value="IT">Information Technology</option>
                                <option value="CS">Computer Science</option>
                                <option value="BUSINESS">Business</option>
                                <option value="ENGINEERING">Engineering</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label>Temporary Password</label>
                            <input type="password" name="password" placeholder="Create temporary password" required>
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

            <!-- dashboard -->
            <section class="admin-dashboard" id="admin-dashboard">


                <section class="cards">
                    <div class="card unverified-student">

                        <?php
                        $unverifiedStudents = countUnverifiedStudents($conn);
                        $unTrend = getTrend($conn, 'student', 'NOT VERIFIED');
                        $unBadge = getBadge($unverifiedStudents);
                        ?>

                        <span class="card-badge"><?= $unBadge ?></span>

                        <div class="card-content">
                            <div>
                                <h3>UNVERIFIED STUDENT</h3>
                                <p>Awaiting verification</p>

                                <span class="trend">
                                    ▲ <?= $unTrend ?> this week
                                </span>
                            </div>

                            <h2><?= $unverifiedStudents ?></h2>
                        </div>
                    </div>

                    <div class="card student">

                        <?php
                        $pendingStudents = countPendingStudents($conn);
                        $pendingTrend = getTrend($conn, 'student', 'PENDING');
                        $pendingBadge = getBadge($pendingStudents);
                        ?>

                        <span class="card-badge"><?= $pendingBadge ?></span>

                        <div class="card-content">
                            <div>
                                <h3>FOR REVIEW</h3>
                                <p>Students awaiting approval and verification.</p>

                                <span class="trend">
                                    ▲ <?= $pendingTrend ?> this week
                                </span>
                            </div>

                            <h2 class="white-h2"><?= $pendingStudents ?></h2>
                        </div>
                    </div>

                    <div class="card unverified-supervisor">

                        <?php
                        $students = countStudents($conn);
                        $trend = getTrend($conn, 'student', 'PENDING');
                        $badge = getBadge($students);
                        ?>

                        <span class="card-badge"><?= $badge ?></span>

                        <div class="card-content">
                            <div>
                                <h3>STUDENTS</h3>
                                <p>Total number of verified and active students.</p>

                                <span class="trend">
                                    ▲ <?= $trend ?> this week
                                </span>
                            </div>

                            <h2><?= $students ?></h2>
                        </div>

                    </div>

                    <div class="card supervisor">
                        <?php
                        $supervisor = countSupervisors($conn);
                        $superTrend = getTrend($conn, 'supervisor', 'PENDING');
                        $superBadge = getBadge($supervisor);
                        ?>

                        <span class="card-badge"><?= $superBadge ?></span>

                        <div class="card-content">
                            <div>
                                <h3>SUPERVISORS</h3>
                                <p>Total number of verified supervisors.</p>

                                <span class="trend">
                                    ▲ <?= $superTrend ?> this week
                                </span>
                            </div>

                            <h2 class="white-h2"><?= $supervisor ?></h2>
                        </div>
                    </div>

                </section>

                <div class="title-block-bot">
                    <h2>Dashboard</h2>
                    <p>Manage supervisors and user accounts in the system</p>
                </div>

                <section class="dashboard-layout">


                    <section class="wrapper bar-chart">
                        <h2>Bar Chart</h2>
                        <canvas id="barChart"></canvas>
                    </section>

                    <section class="deadline-container">
                        <p>Deadlines</p>
                        <div class="task-item">
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Harum, tempore. Inventore dignissimos, a sed iusto odit officiis nihil aliquam, in molestiae reiciendis quae ducimus numquam expedita rem necessitatibus magni commodi?</p>
                        </div>
                    </section>

                    <section class="wrapper line-chart">
                        <h2>Line Chart</h2>
                        <canvas id="lineChart"></canvas>
                    </section>

                    <section class="wrapper-pie pie-chart">
                        <h2>Pie Chart </h2>
                        <canvas id="pieChart"></canvas>
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
                            <span class="icon">→</span>
                            <span class="text">Assign Student to Supervisor</span>
                        </button>

                        <button class="supervisor-btn" id="supervisor-btn">
                            <span class="icon">+</span>
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
                        <button class="create-btn">
                            <span class="icon">📅</span>
                            <span class="text">OJT Batch Setup</span>
                        </button>
                    </div>

                    <div class="create-section-container">
                        <button class="create-btn">
                            <span class="icon">🏫</span>
                            <span class="text">Course Management</span>
                        </button>
                    </div>

                    <div class="create-section-container">
                        <button class="create-btn">
                            <span class="icon">+</span>
                            <span class="text">RFID Setup</span>
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
                                placeholder="Search by user, action, module, or target">
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
                                echo renderActivityLogTable($conn, $search);
                                ?>
                            </tbody>
                        </table>
                    </div>

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
<!-- <script src="https://cdn.plot.ly/plotly-latest.min.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../../js/admin/adminDashboard.js"></script>


</html>