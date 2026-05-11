<?php
session_start();
require_once("../../kapstongConnection.php");
require_once("../../functions.php");

$studentID = $_SESSION['studentID'] ?? ''; 
$category = $_POST['category'] ?? '';

echo renderStudentAttendanceTable($conn, $studentID, $category);