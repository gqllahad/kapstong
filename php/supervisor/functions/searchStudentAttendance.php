<?php
session_start();
require_once("../../auth/supervisor_auth.php");
require_once("../../kapstongConnection.php");
require_once("../../functions.php");

$userID = $_SESSION['user_id'];
$superID = getSupervisorIDByUserID($conn, $userID);
$search = $_POST['search'] ?? '';
$status = $_POST['status'] ?? '';

echo renderStudentMainAttendance($conn, $superID, $search, $status);