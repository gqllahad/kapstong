<?php

header('Content-Type: application/json');

require_once("../../kapstongConnection.php");
require_once("../../functions.php");

$search = $_POST['search'] ?? '';

echo renderTaskAssignStudentList($conn, $search);
