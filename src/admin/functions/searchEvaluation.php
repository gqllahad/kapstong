<?php
require_once("../../Shared/kapstongConnection.php");
require_once("../../auth/admin_auth.php");
require_once("../../Shared/functions.php");

$search = $_POST['search'] ?? '';
$course = $_POST['course'] ?? '';
$supervisor = $_POST['superID'] ?? '';

echo renderAdminFinalEvaluation($conn, $search, $course, $supervisor);