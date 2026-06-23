<?php
require_once("kapstongConnection.php");

$conn->query("
    UPDATE attendance_logs
    SET 
        status = 'voided',
        remarks = 'No time out recorded (auto-voided)',
        current_state = 'VOIDED'
    WHERE final_time_out IS NULL
    AND log_date < CURDATE()
    AND current_state != 'VOIDED'
");