<?php
header('Content-Type: application/json');
require_once("../../kapstongConnection.php");

$superID = $_GET['superID'] ?? NULL;

$sql = "SELECT * FROM evaluation_settings WHERE superID " .
    ($superID ? "= '$superID'" : "IS NULL") . " LIMIT 1";

$result = $conn->query($sql);

echo json_encode($result->fetch_assoc());
