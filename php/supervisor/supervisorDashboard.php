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

            <!-- STATS -->
            <div style="display:flex; gap:20px; flex-wrap:wrap; margin-bottom:30px;">

                <div style="flex:1; min-width:200px; background:white; padding:20px; border-radius:12px;">
                    <h4>Total Interns</h4>
                    <p style="font-size:28px; font-weight:bold;">0</p>
                </div>

                <div style="flex:1; min-width:200px; background:white; padding:20px; border-radius:12px;">
                    <h4>Active Interns</h4>
                    <p style="font-size:28px; font-weight:bold;">0</p>
                </div>

                <div style="flex:1; min-width:200px; background:white; padding:20px; border-radius:12px;">
                    <h4>Completed</h4>
                    <p style="font-size:28px; font-weight:bold;">0</p>
                </div>

                <div style="flex:1; min-width:200px; background:white; padding:20px; border-radius:12px;">
                    <h4>Pending Approvals</h4>
                    <p style="font-size:28px; font-weight:bold;">0</p>
                </div>

            </div>

            <!-- PENDING APPROVALS -->
            <div style="background:white; padding:20px; border-radius:12px; margin-bottom:30px;">
                <h3 style="margin-bottom:15px;">Pending Approvals</h3>

                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f1f5f9;">
                            <th style="padding:12px; text-align:left;">Student</th>
                            <th style="padding:12px; text-align:left;">Type</th>
                            <th style="padding:12px; text-align:left;">Date</th>
                            <th style="padding:12px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <!-- SAMPLE -->
                        <tr>
                            <td style="padding:12px;">Juan Dela Cruz</td>
                            <td style="padding:12px;">Daily Log</td>
                            <td style="padding:12px;">April 15</td>
                            <td style="padding:12px;">
                                <button style="background:#22c55e; color:white; border:none; padding:6px 10px; border-radius:6px;">Approve</button>
                                <button style="background:#ef4444; color:white; border:none; padding:6px 10px; border-radius:6px;">Reject</button>
                            </td>
                        </tr>

                        <!-- EMPTY STATE -->
                        <!--
                <tr>
                    <td colspan="4" style="text-align:center; padding:20px;">No pending approvals</td>
                </tr>
                -->

                    </tbody>
                </table>
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
<script src="../../js/supervisor/supervisorDashboard.js"></script>

</html>