<?php

header('Content-Type: application/json');
require_once("../../auth/supervisor_auth.php");
require_once("../../kapstongConnection.php");
require_once("../../functions.php");

$search = $_POST['search'] ?? '';
$superID = $_SESSION['superID'] ?? null;

echo renderTaskAssignStudentList($conn, $superID,$search);
