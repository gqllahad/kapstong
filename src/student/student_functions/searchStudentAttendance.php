<?php

require_once("../../auth/student_auth.php");
require_once("../../Shared/kapstongConnection.php");
require_once("../../Shared/functions.php");

session_start();
$studentID = $_SESSION['studentID'];

echo renderStudentAttendanceTable(
    $conn,
    $studentID,
    $_POST['category'] ?? '',
    $_POST['dateFromAttendance'] ?? '',
    $_POST['dateToAttendance'] ?? ''
);