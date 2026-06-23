<?php

require_once("../../kapstongConnection.php");
require_once("../../functions.php");

session_start();
$studentID = $_SESSION['studentID'];

echo renderStudentAttendanceTable(
    $conn,
    $studentID,
    $_POST['category'] ?? '',
    $_POST['dateFromAttendance'] ?? '',
    $_POST['dateToAttendance'] ?? ''
);