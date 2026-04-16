<?php

session_start();
require_once("kapstongConnection.php");
require_once("functions.php");

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");


if (!isset($_SESSION['role'])) {
    header("Location: loginPhase.php?error=login+required!");
    exit();
}


$role = $_SESSION['role'] ?? null;
$isVerified = $_SESSION['isVerified'] ?? null;

switch ($role) {

    case "ADMIN":
        header("Location: admin/adminDashboard.php");
        exit();

    case "student":
        if ($isVerified === 'NOT VERIFIED' || $isVerified === 'PENDING') {
            header("Location: student/subStudent/pendingStudentDashboard.php");
            exit();
        }
        header("Location: student/studentDashboard.php");
        exit();

    case "supervisor":
        header("Location: supervisor/supervisorDashboard.php");
        exit();

    default:
        header("Location: loginPhase.php");
        exit();
}
