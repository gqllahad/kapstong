<?php

session_start();
require_once("../kapstongConnection.php");
require_once("../functions.php");

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");


if ($_SESSION['role'] !== "supervisor") {
    header("Location: ../trackerMain.php");
    exit();
}

$userID = $_SESSION['user_id'];
$superID = getSupervisorIDByUserID($conn, $userID);

?>


<!DOCTYPE html>
<html lang="en">


<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>OJT MAIN PAGE</title>

    <link rel="stylesheet" href="../../css/supervisor/supervisorDashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">


</head>


<body>
    <!-- navbar -->
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
        <!-- SIDEBAR -->
        <aside class="sidebar">

            <ul class="menu">
                <li><button id="supervisor-dashboard-btn"><i class="bi bi-house"></i> Home </button></li>
                <li><button><i class="bi bi-journal-text"></i> Preparations</button></li>
                <li><button id="supervisor-btn"><i class="bi bi-file-earmark-text"></i>Students</button></li>
            </ul>

        </aside>

        <!-- CONTENT -->
        <main class="content">
            <!-- PAGE HEADER -->
            <div class="page-header">
                <h2>Supervisor Dashboard</h2>
                <p>Monitor student OJT progress and pending tasks</p>
            </div>

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
                            <p>Students currently handled by this supervisor</p>

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
                    $completedTrend = getSupervisorTrend($conn, 'student_tasks', 'status', 'APPROVED', 'date_updated');
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
                    <h2>Line Chart (Users per Role)</h2>
                    <canvas id="lineChart"></canvas>
                </section>

                <section class="wrapper pie-chart">
                    <h2>Attendance Evaluation</h2>
                    <canvas id="pieChart"></canvas>
                </section>
            </section>

            <!-- PENDING APPROVALS -->
            <div style="background:white; padding:20px; border-radius:12px; margin-bottom:30px;">
                <div class="top-bar">

                    <div class="top-header">
                        <h3 class="table-title">Pending Report Approval</h3>
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

            <!-- STUDENT PROGRESS -->
            <div style="background:white; padding:20px; border-radius:12px; margin-bottom:30px;">
                <h3 style="margin-bottom:15px;">Student Progress</h3>

                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f1f5f9;">
                            <th style="padding:12px; text-align:left;">Student</th>
                            <th style="padding:12px; text-align:left;">Company</th>
                            <th style="padding:12px; text-align:left;">Hours</th>
                            <th style="padding:12px; text-align:left;">Status</th>
                            <th style="padding:12px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <!-- SAMPLE -->
                        <tr>
                            <td style="padding:12px;">Juan Dela Cruz</td>
                            <td style="padding:12px;">ABC Company</td>
                            <td style="padding:12px;">120 / 300 hrs</td>
                            <td style="padding:12px; color:green;">On Track</td>
                            <td style="padding:12px;">
                                <button style="background:#2563EB; color:white; border:none; padding:6px 10px; border-radius:6px;">
                                    View
                                </button>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <!-- ALERTS -->
            <div style="background:white; padding:20px; border-radius:12px;">
                <h3 style="margin-bottom:15px;">Alerts</h3>

                <ul style="list-style:none; padding:0;">

                    <li style="padding:10px; background:#fff3cd; margin-bottom:10px; border-radius:6px;">
                        ⚠️ Juan Dela Cruz has not submitted logs for 3 days
                    </li>

                    <li style="padding:10px; background:#ffe4e6; margin-bottom:10px; border-radius:6px;">
                        ⚠️ Maria Santos is behind required hours
                    </li>

                </ul>
            </div>

            <hr>

            <!-- FOOTER -->
            <footer class="footer">
                <p>© 2026 OJT Tracking System</p>
            </footer>

        </main>


    </div>

</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../../js/supervisor/supervisorDashboard.js"></script>

</html>