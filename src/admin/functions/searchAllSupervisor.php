<?php
require_once("../../Shared/kapstongConnection.php");
require_once("../../auth/admin_auth.php");
require_once("../../Shared/functions.php");

$search = $_POST['search'] ?? '';

echo renderSupervisorTable($conn, $search);
