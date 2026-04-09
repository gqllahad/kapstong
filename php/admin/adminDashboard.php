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

                <h2>Approval Dashboard</h2>

                <div class="approval-grid">

                    <!-- Unverified Students -->
                    <div class="approval-card unverified-student">
                        <h3>Unverified Students</h3>
                        <button class="approve-btn" id="viewAllUnverifiedStudents">View All Unverified Students</button>
                    </div>

                    <!-- All Students -->
                    <div class="approval-card student">
                        <h3>All Students</h3>
                        <button class="approve-btn" id="viewAllStudents">View All Studens</button>
                    </div>

                    <!-- All Supervisors -->
                    <div class="approval-card supervisor">
                        <h3>All Supervisors</h3>
                        <button class="approve-btn" id="viewAllSupervisor">View All Supervisors</button>
                    </div>

                </div>

                <div id="approvalTableContainer" style="margin-top:20px;">
                    <?php

                    echo renderApprovalTable($conn, 'student', 'NOT VERIFIED');
                    ?>
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