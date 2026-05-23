<?php
require_once("../../kapstongConnection.php");
require_once("../../auth/admin_auth.php");
require_once("../../functions.php");

$search = $_POST['search'] ?? '';
$course = $_POST['course'] ?? '';
$supervisor = $_POST['superID'] ?? '';

echo renderAdminFinalEvaluation($conn, $search, $course, $supervisor);