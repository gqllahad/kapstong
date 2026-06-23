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
$search = $_POST['search'] ?? '';
$status = $_POST['status'] ?? '';
$deadline = $_POST['deadline'] ?? '';

echo renderTaskManagementList($conn, $superID, $search, $status, $deadline);
