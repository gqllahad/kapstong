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

switch ($role) {

    case "admin":
        header("Location: admin/adminDashboard.php");
        exit();

    case "student":
        header("Location: student/studentDashboard.php");
        exit();

    default:
        header("Location: loginPhase.php");
        exit();
}
