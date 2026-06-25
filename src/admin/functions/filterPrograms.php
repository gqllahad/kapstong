<?php
require_once("../../Shared/kapstongConnection.php");
require_once("../../auth/admin_auth.php");
require_once("../../Shared/functions.php");

$department = $_POST['department'] ?? '';

echo renderDepartmentManagementTable($conn, $department);
