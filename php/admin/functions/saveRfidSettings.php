<?php
header('Content-Type: application/json');
require_once("../../kapstongConnection.php");

$rfid_enabled = $_POST['rfid_enabled'] ?? 0;

$morning_time_in = $_POST['morning_time_in'] ?? null;
$morning_time_out = $_POST['morning_time_out'] ?? null;

$afternoon_time_in = $_POST['afternoon_time_in'] ?? null;
$afternoon_time_out = $_POST['afternoon_time_out'] ?? null;

$allowed_late_minutes = $_POST['allowed_late_minutes'] ?? 0;

if (!$morning_time_in || !$morning_time_out) {
    echo json_encode([
        "status" => false,
        "message" => "Morning time fields are required."
    ]);
    exit;
}

$settings = [
    "rfid_enabled" => $rfid_enabled,
    "morning_time_in" => $morning_time_in,
    "morning_time_out" => $morning_time_out,
    "afternoon_time_in" => $afternoon_time_in,
    "afternoon_time_out" => $afternoon_time_out,
    "allowed_late_minutes" => $allowed_late_minutes
];

$stmt = $conn->prepare("
    UPDATE attendance_settings
    SET setting_value = ?
    WHERE setting_key = ?
");

foreach ($settings as $key => $value) {

    $stmt->bind_param("ss", $value, $key);
    $stmt->execute();
}

echo json_encode([
    "status" => true,
    "message" => "RFID settings updated successfully!"
]);
?>