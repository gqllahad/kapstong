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

        <nav class="nav-links">
            <a href="#">Home</a>
            <a href="#">Dashboard</a>
            <a href="#">Students</a>
            <a href="#">Profile</a>
        </nav>

    </header>

    <!--  MAIN LAYOUT -->
    <div class="layout">

        <!-- SIDEBAR -->
        <aside class="sidebar">

            <ul class="menu">
                <li><button><i class="bi bi-house"></i> Home</button></li>
                <li><button><i class="bi bi-journal-text"></i> Preparations</button></li>
                <li><button><i class="bi bi-file-earmark-text"></i>Students</button></li>
                <li><a href="../logoutPhase.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>

        </aside>


        <!-- MAIN -->
        <main class="content">

            <section class="page-header">
                <h1>Admin Dashboard</h1>
                <p>Welcome Inigo Joints</p>
            </section>


            <section class="cards">

                <div class="card">
                    <h3>INIGO</h3>
                    <p>WOWERs</p>
                </div>

                <div class="card">
                    <h3>INIGO</h3>
                    <p>WOWERs</p>
                </div>

                <div class="card">
                    <h3>INIGO</h3>
                    <p>WOWERs</p>
                </div>

            </section>


            <section class="content-section">

                <h2>Content Section</h2>
                <p>tables, charts, or components</p>

            </section>

        </main>

    </div>

    <!-- FOOTER -->
    <footer class="footer">
        <p>© 2026 OJT Tracking System</p>
    </footer>

</body>
<script src="../../js/admin/adminDashboard.js"></script>

</html>