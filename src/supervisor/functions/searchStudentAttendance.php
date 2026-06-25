<?php
session_start();
require_once("../../auth/supervisor_auth.php");
require_once("../../Shared/kapstongConnection.php");
require_once("../../Shared/functions.php");

$userID = $_SESSION['user_id'];
$superID = getSupervisorIDByUserID($conn, $userID);
$search = $_POST['search'] ?? '';
$status = $_POST['status'] ?? '';
$attendanceFrom = $_POST['attendanceFrom'] ?? '';
$attendanceTo = $_POST['attendanceTo'] ?? '';

echo renderStudentMainAttendance($conn, $superID, $search, $status, $attendanceFrom, $attendanceTo) ;