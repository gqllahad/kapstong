<?php
session_start();
require_once("../../Shared/kapstongConnection.php");
require_once("../../auth/admin_auth.php");
require_once("../../Shared/functions.php");

$search = $_POST['search'] ?? '';
$status = $_POST['status'] ?? '';
$course = $_POST['course'] ?? '';
$dateFromAttendance = $_POST['dateFromAttendance'] ?? '';
$dateToAttendance = $_POST['dateToAttendance'] ?? '';

echo renderAdminStudentAttendance(
    $conn,
    $search,
    $status,
    $course,
    $dateFromAttendance,
    $dateToAttendance
);