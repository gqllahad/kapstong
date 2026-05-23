<?php

session_start();
require_once("../kapstongConnection.php");
require_once("../functions.php");
header('Content-Type: application/json');

date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['user_id'])) {
     echo json_encode([
        "success" => false,
        "message" => "UNAUTHORIZED"
    ]);
    exit();
}

$role = $_SESSION['role'] ?? null;
$superID = $_SESSION['superID'] ?? null;

if (isset($_POST['rfid'])) {

    $rfid = trim($_POST['rfid']);
    $reason = $_POST['reason'] ?? 'No reason provided';

    if (empty($rfid)) {
        echo json_encode([
        "success" => false,
        "message" => "RFID REQUIRED"
    ]);
    exit();
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
       echo json_encode([
        "success" => false,
        "message" => "RFID NOT REGISTERED"
    ]);
    exit();
    }

    $user = $result->fetch_assoc();
    $studentID = $user['studentID'];

    if ($role === "supervisor") {

        $check = $conn->prepare("
            SELECT 1 
            FROM student_supervisor
            WHERE studentID = ?
            AND superID = ?
            AND status = 'ACTIVE'
            LIMIT 1
        ");

        $check->bind_param("si", $studentID, $superID);
        $check->execute();

        $res = $check->get_result();

        if ($res->num_rows == 0) {
            echo json_encode([
            "success" => false,
            "message" => "STUDENT NOT ASSIGNED TO YOU"
        ]);
        exit();
        }
    }

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
         echo json_encode([
        "success" => false,
        "message" => "NO ATTENDANCE RECORD TODAY"
    ]);
    exit();
    }

    if ($row['current_state'] == 'TIMED_OUT') {
        echo json_encode([
        "success" => false,
        "message" => "ALREADY TIMED OUT TODAY"
    ]);
    exit();
    }

    $now = date("Y-m-d H:i:s");

    $remarks = "EMERGENCY TIME OUT: " . $reason;
    $status = "excused";

    $firstIn = strtotime($row['first_time_in']);
    $finalOut = time();

    $totalSecondsWorked = $finalOut - $firstIn;

    $lunchBreakSeconds = 0;

        if (
        !empty($row['lunch_break_out']) &&
        !empty($row['lunch_break_in'])
    ) {

        $lunchOut = strtotime($row['lunch_break_out']);
        $lunchIn = strtotime($row['lunch_break_in']);

        $lunchBreakSeconds = $lunchIn - $lunchOut;
    }

    $snackBreakSeconds = 0;

    if (
        !empty($row['snack_break_out']) &&
        !empty($row['snack_break_in'])
    ) {

        $snackOut = strtotime($row['snack_break_out']);
        $snackIn = strtotime($row['snack_break_in']);

        $snackBreakSeconds = $snackIn - $snackOut;
    }

    $finalWorkedSeconds = $totalSecondsWorked - $lunchBreakSeconds - $snackBreakSeconds;

    $workedHours = round($finalWorkedSeconds / 3600, 2);

    $stmt = $conn->prepare("
        UPDATE attendance_logs
        SET
            final_time_out = ?,
            total_hours = ?,
            current_state = 'TIMED_OUT',
            remarks = ?,
            status = ?,
            emergency_timeout = 1,
            emergency_reason = ?
        WHERE attendanceID = ?
    ");

    $stmt->bind_param(
        "sdsssi",
        $now,
        $workedHours,
        $remarks,
        $status,
        $reason,
        $row['attendanceID']
    );

    $stmt->execute();

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

    echo json_encode([
        "success" => true,
        "message" => "Emergency Time Out Successful",
        "student" => $user['name']
    ]);
}
?>