<?php

session_start();
require_once("../kapstongConnection.php");
require_once("../functions.php");

date_default_timezone_set('Asia/Manila');

if (isset($_POST['rfid'])) {

    $rfid = trim($_POST['rfid']);
    $reason = $_POST['reason'] ?? 'No reason provided';

    if (empty($rfid)) {
        die("⚠️ RFID REQUIRED");
    }

    $stmt = $conn->prepare("
        SELECT userID, studentID, name 
        FROM users 
        WHERE rfid_uid = ?
    ");
    $stmt->bind_param("s", $rfid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        die("❌ RFID NOT REGISTERED");
    }

    $user = $result->fetch_assoc();
    $studentID = $user['studentID'];

    $stmtAttendance = $conn->prepare("
        SELECT * FROM attendance_logs
        WHERE studentID = ?
        AND log_date = CURDATE()
        LIMIT 1
    ");

    $stmtAttendance->bind_param("s", $studentID);
    $stmtAttendance->execute();
    $row = $stmtAttendance->get_result()->fetch_assoc();

    if (!$row) {
        die("⚠️ No attendance record found today");
    }

    if ($row['current_state'] == 'TIMED_OUT') {
        die("⚠️ Already timed out today");
    }

    $now = date("Y-m-d H:i:s");

    $remarks = "EMERGENCY TIME OUT: " . $reason;
    $status = "excused";

    $stmt = $conn->prepare("
        UPDATE attendance_logs
        SET
            final_time_out = ?,
            current_state = 'TIMED_OUT',
            remarks = ?,
            status = ?
        WHERE attendanceID = ?
    ");

    $stmt->bind_param(
        "sssi",
        $now,
        $remarks,
        $status,
        $row['attendanceID']
    );

    $stmt->execute();

    $firstIn = strtotime($row['first_time_in']);
    $finalOut = time();

    $workedHours = round(($finalOut - $firstIn) / 3600, 2);

    $updateProgress = $conn->prepare("
        UPDATE student_progress
        SET 
            completed_hours = completed_hours + ?,
            remaining_hours = LEAST(required_hours, completed_hours + ?)
        WHERE studentID = ?
    ");

    $updateProgress->bind_param(
        "dds",
        $workedHours,
        $workedHours,
        $studentID
    );

    $updateProgress->execute();

    echo "✅ Emergency Time Out Successful for {$user['name']}";
}
?>