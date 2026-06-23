<?php
require_once("../../kapstongConnection.php");
require_once("../../auth/admin_auth.php");
require_once("../../functions.php");

$search = $_POST['search'] ?? '';
$module = $_POST['module'] ?? '';
$dateFrom = $_POST['dateFrom'] ?? '';
$dateTo = $_POST['dateTo'] ?? '';

echo renderActivityLogTable($conn, $search, $module, $dateFrom, $dateTo);
