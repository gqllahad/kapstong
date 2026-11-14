<?php
require_once("kapstongConnection.php");
date_default_timezone_set('Asia/Manila');

$logDir  = 'C:/xampp/htdocs/kapstong/src/logs';
$logFile = $logDir . '/autovoid.log';
$log = fn($msg) => file_put_contents($logFile, date('Y-m-d H:i:s') . " $msg" . PHP_EOL, FILE_APPEND);

if ($conn->connect_error) {
    $log("Connection failed: " . $conn->connect_error);
    exit(1);
}

$today = date('Y-m-d');

$stmt = $conn->prepare("
    UPDATE attendance_logs
    SET current_state = 'INCOMPLETE',
        remarks = 'No time out recorded (needs review)'
    WHERE final_time_out IS NULL
      AND log_date < ?
      AND current_state IN ('WORKING', 'LUNCH_BREAK', 'SNACK_BREAK')
");
$stmt->bind_param("s", $today);

if (!$stmt->execute()) {
    $log("Query failed: " . $stmt->error);
    exit(1);
}

$log("rows affected: " . $stmt->affected_rows);
$conn->close();