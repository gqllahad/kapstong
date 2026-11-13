<?php
require_once("kapstongConnection.php");
date_default_timezone_set('Asia/Manila');

if ($conn->connect_error) {
    error_log("[auto-void] Connection failed: " . $conn->connect_error);
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
    error_log("[auto-void] Query failed: " . $stmt->error);
    exit(1);
}

error_log("[auto-void] Rows affected: " . $stmt->affected_rows . " at " . date('Y-m-d H:i:s'));
$conn->close();