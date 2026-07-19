<?php
require_once("kapstongConnection.php");

// $conn->query("
//     UPDATE attendance_logs
//     SET 
//         status = 'voided',
//         remarks = 'No time out recorded (auto-voided)',
//         current_state = 'VOIDED'
//     WHERE final_time_out IS NULL
//     AND log_date < CURDATE()
//     AND current_state != 'VOIDED'
// ");

if ($conn->connect_error) {
    error_log("[auto-void] Connection failed: " . $conn->connect_error);
    exit(1);
}

$result = $conn->query("
    UPDATE attendance_logs
    SET 
        status = 'voided',
        remarks = 'No time out recorded (auto-voided)',
        current_state = 'VOIDED'
    WHERE final_time_out IS NULL
    AND log_date < CURDATE()
    AND current_state != 'VOIDED'
");

if ($result === false) {
    error_log("[auto-void] Query failed: " . $conn->error);
    exit(1);
}

error_log("[auto-void] Rows affected: " . $conn->affected_rows . " at " . date('Y-m-d H:i:s'));
$conn->close();