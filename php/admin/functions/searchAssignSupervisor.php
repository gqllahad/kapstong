<?php

require_once("../../kapstongConnection.php");
require_once("../../functions.php");

$search = $_POST['search'] ?? '';

echo renderAssignSupervisorList($conn, $search);
