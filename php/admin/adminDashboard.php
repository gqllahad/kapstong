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
                <li><button><i class="bi bi-journal-text"></i> Preparations</button></li>
                <li><button id="admin-approval-btn"><i class="bi bi-file-earmark-text"></i>Students</button></li>
            </ul>

        </aside>


        <!-- MAIN -->
        <main class="content">
            <div id="overlay" class="overlay"></div>

            <!-- student accounts -->
            <div class="all-student-modal" id="all-student-modal">
                <div class="modal-header">
                    <h3>Students</h3>
                    <button id="closeAllStudentModal" class="modal-close">&times;</button>
                </div>

                <div class="search-container">
                    <form method="GET" action="" autocomplete="off">
                        <input type="text" id="allStudentSearch"
                            placeholder="Search by ID, Name, OR Email">
                    </form>
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

            <!-- create supervisor modal -->
            <div class="supervisor-container" id="supervisor-container">
                <div class="modal-header">
                    <h3>➕ Create Supervisor</h3>
                    <button id="closeCreateSupervisorModal" class="modal-close">&times;</button>
                </div>

                <!-- BODY -->
                <div class="supervisor-body">

                    <p class="modal-subtitle">
                        Add a new supervisor account. Fill in the details below.
                    </p>

                    <form id="createSupervisorForm">

                        <!-- NAME -->
                        <div class="input-group">
                            <label>Full Name</label>
                            <input type="text" name="name" placeholder="Enter full name" required>
                        </div>

                        <!-- EMAIL -->
                        <div class="input-group">
                            <label>Email Address</label>
                            <input type="email" name="email" placeholder="Enter email address" required>
                        </div>

                        <!-- PHONE -->
                        <div class="input-group">
                            <label>Mobile Number</label>
                            <input type="text" name="mobile" placeholder="09XXXXXXXXX" required>
                        </div>

                        <!-- DEPARTMENT -->
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

                        <!-- PASSWORD -->
                        <div class="input-group">
                            <label>Temporary Password</label>
                            <input type="password" name="password" placeholder="Create temporary password" required>
                        </div>

                        <!-- CHECKBOX -->
                        <div class="checkbox-group">
                            <input type="checkbox" id="confirmCreateSupervisor">
                            <label for="confirmCreateSupervisor">
                                I confirm that this supervisor will be added to the system.
                            </label>
                        </div>

                        <!-- BUTTON -->
                        <button type="submit" id="createSupervisorBtn" disabled class="primary-btn">
                            Create Supervisor
                        </button>

                    </form>
                </div>
            </div>

            <!-- dashboard -->
            <section class="admin-dashboard" id="admin-dashboard">
                <section class="cards">

                    <div class="card unverified-student">
                        <h3>UNVERIFIED STUDENTS</h3>
                        <p>Students awaiting account verification.</p>
                    </div>

                    <div class="card student">
                        <h3>STUDENTS</h3>
                        <p>Total number of verified and active students.</p>
                    </div>

                    <div class="card unverified-supervisor">
                        <h3>UNVERIFIED SUPERVISORS</h3>
                        <p>Supervisors pending approval and verification.</p>
                    </div>

                    <div class="card supervisor">
                        <h3>SUPERVISORS</h3>
                        <p>Total number of verified supervisors.</p>
                    </div>

                </section>

                <!-- dashboard -->
                <section class="dashboard-layout">
                    <section class="wrapper bar-chart">
                        <h2>bar Chart (Users per Role)</h2>
                        <canvas id="barChart"></canvas>
                    </section>

                    <section class="recent-logs-container">
                        <p>Recent Logs</p>
                        <div class="task-item">
                            Lorem, ipsum dolor sit amet consectetur adipisicing elit. Tenetur excepturi iure velit laboriosam natus asperiores reiciendis ipsa? Temporibus aspernatur ducimus nihil in hic, sed fugit obcaecati ad nostrum, minima repellat.
                        </div>
                    </section>

                    <section class="pie-charts full">
                        <section class="wrapper-pie pie-chart-plain">
                            <h2>Pie Chart (Users per Role)</h2>
                            <canvas id="pieChart2"></canvas>
                        </section>

                        <section class="process-task">
                            <h2>Process Tasks</h2>
                            <div class="task-item">
                                <p>Takss</p>
                                <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Illo voluptatem temporibus earum saepe voluptatibus. Perspiciatis tempore excepturi numquam nostrum id rerum culpa soluta a ducimus in nam cum, voluptates quae.</p>
                            </div>
                            <div class="task-item">
                                <p>Takss</p>
                                <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Illo voluptatem temporibus earum saepe voluptatibus. Perspiciatis tempore excepturi numquam nostrum id rerum culpa soluta a ducimus in nam cum, voluptates quae.</p>
                            </div>
                        </section>
                    </section>

                    <section class="deadline-container">
                        <p>attendance</p>
                        <div class="task-item">
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Harum, tempore. Inventore dignissimos, a sed iusto odit officiis nihil aliquam, in molestiae reiciendis quae ducimus numquam expedita rem necessitatibus magni commodi?</p>
                        </div>
                    </section>

                </section>
            </section>

            <!-- application approvals -->
            <section class="admin-approval" id="admin-approval">

                <div class="approval-grid">

                    <!-- Unverified Students -->
                    <div class="approval-card unverified-student">
                        <h3>Unverified Students</h3>
                        <button class="view-btn" id="viewAllUnverifiedStudentsBtn">View All Unverified Students</button>
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

                <div id="approvalTableContainer" style="margin-top:20px;">
                    <h3 class="table-title">Pending Application</h3>

                    <div class="top-bar">
                        <div class="search-container">
                            <form method="GET" action="" autocomplete="off">
                                <input type="text" id="approvalSearch"
                                    placeholder="Search by ID, Name, OR Email">
                            </form>
                        </div>

                        <div class="create-supervisor-btn">
                            <button class="supervisor-btn" id="supervisor-btn">
                                <span class="icon">+</span>
                                <span class="text">Create Supervisor</span>
                            </button>
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
                                $search = $_GET['approval_search'] ?? '';
                                echo renderApprovalTable($conn, 'student', 'NOT VERIFIED', $search);
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

        </main>

    </div>

    <hr>

    <!-- FOOTER -->
    <footer class="footer">
        <p>© 2026 OJT Tracking System</p>
    </footer>

</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../../js/admin/adminDashboard.js"></script>


</html>