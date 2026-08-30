<?php

require_once("../../Shared/kapstongConnection.php");
require_once("../../auth/admin_auth.php");
require_once("../../Shared/functions.php");

$superID = $_POST['superID'] ?? '';

echo renderSupervisorAssignedStudents($conn, $superID);