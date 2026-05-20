<?php

session_start();
require_once("kapstongConnection.php");
require_once("functions.php");

date_default_timezone_set('Asia/Manila');

$timeInStart = getAttendanceSetting($conn, 'morning_time_in', '07:50:00');
$invalidScanTime = getAttendanceSetting($conn, 'late_time', '10:30:00');

$lateGraceMinutes = (int)getAttendanceSetting($conn, 'late_threshold_minutes', 5);

$lateTime = date(
    "H:i:s",
    strtotime($timeInStart . " +{$lateGraceMinutes} minutes")
);

$snackStartTime = "15:00:00";

$timeOutMorningTime = getAttendanceSetting($conn, 'morning_time_out', '12:00:00');

$timeInAfternoonTime = getAttendanceSetting($conn, 'afternoon_time_in', '13:00:00');
$timeOutAfternoonTime = getAttendanceSetting($conn, 'afternoon_time_out', '17:00:00');

$MIN_WORK_MINUTES = (int)getAttendanceSetting(
    $conn,
    'minimum_work_minutes',
    5
);

$MIN_BREAK_MINUTES = (int)getAttendanceSetting(
    $conn,
    'minimum_break_minutes',
    5
);

$MAX_HOURS_PER_DAY = (int)getAttendanceSetting(
    $conn,
    'max_hours_per_day',
    8
);

$snackBreakTime = (int)getAttendanceSetting(
    $conn,
    'snack_break_minutes',
    15
);

$lunchBreakTime = (int)getAttendanceSetting(
    $conn,
    'lunch_break_minutes',
    60
);

$current_time = date("H:i:s");
$now = time();

$role = $_SESSION['role'];
$superID = $_SESSION['superID'] ?? null;

if (isset($_POST['rfid'])) {

    $rfid = trim($_POST['rfid']);

    if (empty($rfid)) {
        die("RFID CANNOT BE EMPTY");
    }

    $stmt = $conn->prepare("
        SELECT userID, studentID, name, role 
        FROM users 
        WHERE rfid_uid = ?
    ");
    $stmt->bind_param("s", $rfid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        die("RFID not Registered!");
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
            die("Student not assigned to you!");
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

    // $stmtCount = $conn->prepare("
    //     SELECT COUNT(*) as total
    //     FROM attendance_logs
    //     WHERE studentID = ? AND log_date = CURDATE()
    // ");

    // $stmtCount->bind_param("s", $studentID);
    // $stmtCount->execute();
    // $count = $stmtCount->get_result()->fetch_assoc();

    // $isFirstTimeToday = ($count['total'] == 0);

    $state = $row['current_state'] ?? 'NONE';

    if (!$row) {
        $state = 'NONE';
    }

    // time in
    if ($state === 'NONE') {

    if ($current_time < $timeInStart ) { //|| $current_time > $invalidScanTime

        $_SESSION['status'] =   
            "Time-in allowed only between $timeInStart - $lateTime";
             echo $_SESSION['status'];
             exit();
    }
    
        $lateMinutes = 0;
        $remarks = "Arrived on time";

        if ($current_time > $lateTime) {
            $status = "late";

            $lateMinutes = round(
                (strtotime($current_time) - strtotime($lateTime)) / 60
            );

            $remarks = "Late by {$lateMinutes} minute(s)";
        } else {
            $status = "present";
            $remarks = "Arrived on time";
        }

       $stmtIn = $conn->prepare("
            INSERT INTO attendance_logs (
                studentID,
                rfid_uid,
                log_date,
                first_time_in,
                status,
                remarks,
                current_state
            )
            VALUES (?, ?, CURDATE(), NOW(), ?, ?, 'WORKING')
        ");
        $stmtIn->bind_param(
            "ssss",
            $studentID,
            $rfid,
            $status,
            $remarks
        );

        $stmtIn->execute();

        $_SESSION['status'] = "TIME IN SUCCESS ($status)";
        echo $_SESSION['status'];
        exit();
    }
    // lunch break
     if ($state === 'WORKING' && !$row['lunch_break_out'] && $current_time >= $timeOutMorningTime && $current_time < $timeInAfternoonTime) {
        $remarks = "Started lunch break";

        $stmt = $conn->prepare("
            UPDATE attendance_logs
            SET
                lunch_break_out = NOW(),
                remarks = ?,
                current_state = 'LUNCH_BREAK'
            WHERE attendanceID = ?
        ");

        $stmt->bind_param("si", $remarks, $row['attendanceID']);
        $stmt->execute();

        $_SESSION['status'] = "LUNCH BREAK STARTED";
        echo $_SESSION['status'];
        exit();
    }
    // return lunch break
      if ($state === 'LUNCH_BREAK') {

        $remarks = "Returned from lunch break";
        $lastBreak = strtotime($row['lunch_break_out']);
        $breakMinutes = ($now - $lastBreak) / 60;

        if ($breakMinutes < $lunchBreakTime) {

            $remaining = ceil($lunchBreakTime - $breakMinutes);

            $_SESSION['status'] =
                "Lunch break ongoing. Wait {$remaining} minutes.";
            exit();
        }

        $stmt = $conn->prepare("
            UPDATE attendance_logs
            SET
                lunch_break_in = NOW(),
                remarks = ?,
                current_state = 'WORKING'
            WHERE attendanceID = ?
        ");

        $stmt->bind_param("si", $remarks, $row['attendanceID']);
        $stmt->execute();

        $_SESSION['status'] = "RETURNED FROM LUNCH";
         echo $_SESSION['status'];
         exit();
    }
    // snack break
      if ( $state === 'WORKING' && $row['lunch_break_in'] && !$row['snack_break_out'] && $current_time >= $snackStartTime) {

      $remarks = "Started snack break";
        $stmt = $conn->prepare("
            UPDATE attendance_logs
            SET
                snack_break_out = NOW(),
                remarks = ?,
                current_state = 'SNACK_BREAK'
            WHERE attendanceID = ?
        ");

        $stmt->bind_param("si", $remarks,$row['attendanceID']);
        $stmt->execute();

        $_SESSION['status'] = "SNACK BREAK STARTED";
        echo $_SESSION['status'];
        exit();
    }
    // return snack break
      if ($state === 'SNACK_BREAK') {

         $remarks = "Returned from snack break";
        $lastBreak = strtotime($row['snack_break_out']);
        $breakMinutes = ($now - $lastBreak) / 60;

        if ($breakMinutes < $snackBreakTime) {

            $remaining = ceil($snackBreakTime - $breakMinutes);

            $_SESSION['status'] =
                "Snack break ongoing. Wait {$remaining} minutes.";

            exit();
        }

        $stmt = $conn->prepare("
            UPDATE attendance_logs
            SET
                snack_break_in = NOW(),
                remarks = ?,
                current_state = 'WORKING'
            WHERE attendanceID = ?
        ");

        $stmt->bind_param("si", $remarks, $row['attendanceID']);
        $stmt->execute();

        $_SESSION['status'] = "RETURNED FROM SNACK BREAK";
        echo $_SESSION['status'];
        exit();
    }


    //time out
     if ($state === 'WORKING' && $current_time >= $timeOutAfternoonTime && !$row['final_time_out']) {

        $firstIn = strtotime($row['first_time_in']);
        $finalOut = time();

        $workedMinutes = ($finalOut - $firstIn) / 60;

        

         if ($workedMinutes < $MIN_WORK_MINUTES) {

            $remaining = ceil($MIN_WORK_MINUTES - $workedMinutes);

            $_SESSION['status'] =
                "You must work at least {$MIN_WORK_MINUTES} minutes before time out. Wait {$remaining} more minutes.";

            echo $_SESSION['status'];
            exit();
        }

        $totalWorkedSeconds = $finalOut - $firstIn;

        if ($row['lunch_break_out'] && $row['lunch_break_in']) {

            $lunchOut = strtotime($row['lunch_break_out']);
            $lunchIn = strtotime($row['lunch_break_in']);

            $totalWorkedSeconds -= ($lunchIn - $lunchOut);
        }

        if ($row['snack_break_out'] && $row['snack_break_in']) {

            $snackOut = strtotime($row['snack_break_out']);
            $snackIn = strtotime($row['snack_break_in']);

            $totalWorkedSeconds -= ($snackIn - $snackOut);
        }

        $totalHours = round($totalWorkedSeconds / 3600, 2);

        if ($totalHours > $MAX_HOURS_PER_DAY) {
            $totalHours = $MAX_HOURS_PER_DAY;
        }

        $remarks = "Completed {$totalHours} hours for the day";

        $stmt = $conn->prepare("
            UPDATE attendance_logs
            SET
                final_time_out = NOW(),
                current_state = 'TIMED_OUT',
                total_hours = ?,
                remarks = ?
            WHERE attendanceID = ?
        ");

        $stmt->bind_param("dsi",$totalHours,$remarks,$row['attendanceID']);
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
            $totalHours,
            $totalHours,
            $studentID
        );

        $updateProgress->execute();

        $_SESSION['status'] = "TIME OUT SUCCESS ($totalHours hrs).";
        echo $_SESSION['status'];
        exit();
    }

    // time out validations
      if ($state == 'TIMED_OUT') {

        $_SESSION['status'] =
            "You are already timed out today.";
            echo $_SESSION['status'];
        exit();
    }
}
$_SESSION['status'] = "Invalid scan action!";
echo $_SESSION['status'];
exit();
// echo $_SESSION['status'] ?? "UNKNOWN STATUS";
// exit();
?>
