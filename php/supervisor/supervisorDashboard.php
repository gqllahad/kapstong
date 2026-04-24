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
        <h1>Supervisor Dashboard</h1>
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
                <li><button id="supervisor-oversight-btn"><i class="bi bi-journal-text"></i> Task Reviews</button></li>
                <li><button id="supervisor-students-btn"><i class="bi bi-file-earmark-text"></i>Students</button></li>
                <li><button id="supervisor-evaluation-btn"><i class="bi bi-bar-chart-line"></i> Reports / Evaluation</button></li>
            </ul>

        </aside>

        <!-- CONTENT -->
        <main class="content">
            <div id="overlay" class="overlay"></div>

            <!-- modals -->

            <!-- view Evaluation breakdown -->
            <div class="student-breakdown-container" id="student-breakdown-container">
                <div class="modal-header">
                    <h3>Student Evaluation Breakdown</h3>
                    <button id="closeStudentBreakdown" class="modal-close">&times;</button>
                </div>

                <div id="reportSummary"></div>
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

                        <!-- 🍩 CHART SECTION -->
                        <div class="chart-container">
                            <canvas id="attendanceChart"></canvas>
                        </div>

                        <div class="table-container-attendance" style="max-height: 175px; overflow-y: auto; margin-top: 15px;">

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
                            <h4>📌 Task Creation Guidelines</h4>
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
                                <h4>👨‍🎓 Assign Students</h4>
                                <p>Select one or multiple students for this task</p>

                                <input
                                    type="text"
                                    id="taskStudentSearch"
                                    placeholder="🔍 Search student by name, ID, Course or Yearlevel...">

                                <div class="list-box" id="taskStudentList">
                                    <?php echo renderTaskAssignStudentList($conn); ?>
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

            <!-- supervisor dashboard -->
            <section class="supervisor-dashboard" id="supervisor-dashboard">
                <div class="title-block-bot">
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
                            <span class="icon">📅</span>
                            <span class="text">Create Task</span>
                        </button>
                    </div>
                </div>

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


                <div style="background:white; padding:20px; border-radius:12px; margin-bottom:30px;">
                    <div class="top-bar">

                        <div class="top-header">
                            <h3 class="table-title">Tasks</h3>
                            <p>Search and review task created.</p>
                        </div>

                        <div class="search-filter">
                            <div class="search-container">
                                <i class="bi bi-search search-icon"></i>
                                <input type="text" id="assignedTaskSearch"
                                    placeholder="Search by ID, Name, OR Email">
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
                                echo renderTaskManagementList($conn, $superID, $search);
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
                <div style="background:white; padding:20px; border-radius:12px; margin-bottom:30px;">
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
                                <th>Progress</th>
                                <th>Tasks</th>
                                <th>Final Grade</th>
                                <th>Remarks</th>
                                <th>Date Generated</th>
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