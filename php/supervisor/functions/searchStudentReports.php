<?php
session_start();
require_once("../../auth/supervisor_auth.php");
require_once("../../kapstongConnection.php");
require_once("../../functions.php");

if (!isset($_SESSION['user_id'])) {
    exit("Session expired. Please login again.");
}

$userID = $_SESSION['user_id'];
$superID = getSupervisorIDByUserID($conn, $userID);
$reportsSearch = $_POST['search'] ?? '';
$reportsCourse = $_POST['course'] ?? '';

echo renderEvaluationList($conn, $superID, $reportsSearch, $reportsCourse);
