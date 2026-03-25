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

$studentName = $_SESSION['name'];

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
            <a href="#">Profile</a>
            <hr style="width: 75%; text-align: left;">
            <a href="../logoutPhase.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </nav>
    </header>

    <div class="layout">
        <aside class="sidebar">
            <ul class="menu">
                <li class="active">
                    <button><i class="bi bi-house"></i> Home</button>
                </li>
                <li>
                    <button><i class="bi bi-journal-text"></i> My Tasks</button>
                </li>
                <li><button><i class="bi bi-file-earmark-text"></i>Submissions</button></li>
                <li><button><i class="bi bi-chat-left-text"></i> Messages</button></li>
            </ul>
        </aside>

        <main class="content">

            <section class="student-dashboard">
                <section class="page-header">
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

        </main>
    </div>

    <footer class="footer">
        <p>© 2026 OJT Tracking System</p>
    </footer>

</body>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../../js/student/studentDashboard.js"></script>

</html>