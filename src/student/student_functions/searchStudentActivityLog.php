<?php
session_start();
// require_once("../../auth/supervisor_auth.php");
require_once("../../kapstongConnection.php");
require_once("../../functions.php");

$studentID = $_SESSION['studentID'] ?? '';

if (!$studentID) {
    
    die("NO STUDENT ID RECEIVED");
}

$search = $_POST['search'] ?? '';
$module = $_POST['module'] ?? '';
$dateFrom = $_POST['dateFrom'] ?? '';
$dateTo = $_POST['dateTo'] ?? '';

echo renderStudentActivityLogTable($conn, $studentID, $search, $module, $dateFrom, $dateTo);