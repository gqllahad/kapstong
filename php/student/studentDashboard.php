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
        <nav>
            <a href="#">Profile</a>
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
                <li><a href="../logoutPhase.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="content">

            <section class="student-dashboard">
                <section class="page-header">
                    <h2>Welcome, <?php echo htmlspecialchars($studentName); ?>!</h2>
                    <p>Here's your OJT progress and tasks.</p>
                </section>

                <section class="cards">
                    <div class="card">
                        <h3>OJT Status</h3>
                        <p>View your current OJT assignments.</p>
                    </div>
                    <div class="card">
                        <h3>Notifications</h3>
                        <p>Check announcements from your mentor.</p>
                    </div>
                    <div class="card">
                        <h3>Documents</h3>
                        <p>Upload or review submitted reports.</p>
                    </div>
                </section>
            </section>

            <section class="student-documents">
                <section class="cards">
                    <div class="card">
                        <h3>OJT Status</h3>
                        <p>View your current OJT assignments.</p>
                    </div>
                    <div class="card">
                        <h3>Notifications</h3>
                        <p>Check announcements from your mentor.</p>
                    </div>
                    <div class="card">
                        <h3>Documents</h3>
                        <p>Upload or review submitted reports.</p>
                    </div>
                </section>
            </section>


            <section class="content-section">
                <h2>OJT Details</h2>
                <p>Track your tasks, mentors, and schedules here.</p>
            </section>
        </main>
    </div>

    <footer class="footer">
        <p>© 2026 OJT Tracking System</p>
    </footer>

    <script src="../../js/student/studentDashboard.js"></script>
</body>

</html>