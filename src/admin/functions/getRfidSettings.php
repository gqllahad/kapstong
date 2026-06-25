<?php
header('Content-Type: application/json');
require_once("../../Shared/kapstongConnection.php");
require_once("../../auth/admin_auth.php");

$result = $conn->query("
    SELECT setting_key, setting_value
    FROM attendance_settings
");

$settings = [];

while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

echo json_encode($settings);
?>