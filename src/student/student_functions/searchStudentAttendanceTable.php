<?php
session_start();
require_once("../../auth/student_auth.php");
require_once("../../Shared/kapstongConnection.php");
require_once("../../Shared/functions.php");

$studentID = $_SESSION['studentID'] ?? ''; 
$category = $_POST['category'] ?? '';

echo renderStudentAttendanceTable($conn, $studentID, $category);