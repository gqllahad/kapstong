<?php

session_start();
require_once("../kapstongConnection.php");
require_once("../functions.php");

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");


if ($_SESSION['role'] !== "admin") {
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

    <link rel="stylesheet" href="../css/trackerMain.css">
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

            <ul>
                <li><a href="#"><i class="bi bi-house"></i> Home</a></li>
                <li><a href="#"><i class="bi bi-receipt"></i> Preparations</a></li>
                <li><a href="#"><i class="bi bi-people"></i> Students</a></li>
                <li><a href="logoutPhase.php"><i class="bi bi-people"></i> Log-out</a></li>
            </ul>

        </aside>


        <!-- MAIN -->
        <main class="content">

            <section class="page-header">
                <?php echo ($role); ?>
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

</html>