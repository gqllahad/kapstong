<?php
require_once("../../kapstongConnection.php");
require_once("../../functions.php");

$department = $_POST['department'] ?? '';

echo renderDepartmentManagementTable($conn, $department);
