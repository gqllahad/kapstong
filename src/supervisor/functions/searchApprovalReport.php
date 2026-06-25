<?php

session_start();
require_once("../../auth/supervisor_auth.php");
require_once("../../Shared/kapstongConnection.php");
require_once("../../Shared/functions.php");

if (!isset($_SESSION['user_id'])) {
    exit("Session expired. Please login again.");
}

$userID = $_SESSION['user_id'];
$superID = getSupervisorIDByUserID($conn, $userID);
$search = $_POST['search'] ?? '';

echo renderApprovalReportList($conn, $superID, $search);
