<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../auth/auth_guard.php");
require_once("../Shared/kapstongConnection.php");
require_once("../Shared/functions.php");

requireRole(['ADMIN', 'supervisor']);

date_default_timezone_set('Asia/Manila');

function respond(string $msg): void
{
    $_SESSION['status'] = $msg;
    echo $msg;
    exit();
}


$nowTs        = time();
$nowDt        = date('Y-m-d H:i:s', $nowTs);
$today        = date('Y-m-d', $nowTs);
$current_time = date('H:i:s', $nowTs);

/* Settings*/
$timeInStart          = getAttendanceSetting($conn, 'morning_time_in', '07:50:00');
$lateGraceMinutes     = (int) getAttendanceSetting($conn, 'late_threshold_minutes', 5);
$timeOutMorningTime   = getAttendanceSetting($conn, 'morning_time_out', '12:00:00');
$timeInAfternoonTime  = getAttendanceSetting($conn, 'afternoon_time_in', '13:00:00');
$timeOutAfternoonTime = getAttendanceSetting($conn, 'afternoon_time_out', '17:00:00');
$MIN_WORK_MINUTES     = (int) getAttendanceSetting($conn, 'minimum_work_minutes', 5);
$MAX_HOURS_PER_DAY    = (int) getAttendanceSetting($conn, 'max_hours_per_day', 8);
$lunchBreakTime       = (int) getAttendanceSetting($conn, 'lunch_break_minutes', 60);

// Late cutoff 
$lateTime = date("H:i:s", strtotime($timeInStart . " +{$lateGraceMinutes} minutes"));

$role    = $_SESSION['role'];
$superID = $_SESSION['superID'] ?? null;

if (!isset($_POST['rfid'])) {
    respond("Invalid scan action!");
}

$rfid = trim($_POST['rfid']);

if ($rfid === '') {
    respond("RFID cannot be empty.");
}

if ((int) getAttendanceSetting($conn, 'rfid_enabled', 1) !== 1) {
    respond("RFID scanning is currently disabled.");
}

$stmt = $conn->prepare("SELECT userID, studentID, name, role FROM users WHERE rfid_uid = ?");
$stmt->bind_param("s", $rfid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    respond("RFID not registered!");
}

$user      = $result->fetch_assoc();
$studentID = $user['studentID'];

$ownerStmt = $conn->prepare("
    SELECT superID FROM student_supervisor
    WHERE studentID = ? AND status = 'ACTIVE' LIMIT 1
");
$ownerStmt->bind_param("s", $studentID);
$ownerStmt->execute();
$studentSuperID = (int)($ownerStmt->get_result()->fetch_column() ?: 0);

$calStmt = $conn->prepare("
    SELECT type, label, hour_multiplier
    FROM calendar_events
    WHERE event_date = ?
      AND superID IN (?, 0)
      AND studentID IN (?, '')
    ORDER BY (studentID <> '') DESC, superID DESC
    LIMIT 1
");
$calStmt->bind_param("sis", $today, $studentSuperID, $studentID);
$calStmt->execute();
$calEvent = $calStmt->get_result()->fetch_assoc();

$dayType        = $calEvent['type'] ?? 'WORKDAY';
$hourMultiplier = $calEvent ? (float)$calEvent['hour_multiplier'] : 1.0;
$dayLabel       = $calEvent['label'] ?? null;

if ($dayType === 'NO_WORK') {
    respond("Today is marked as a no-work day" . ($dayLabel ? " ({$dayLabel})" : "") . ". Attendance scanning is disabled.");
}


$progressCheck = $conn->prepare("
    SELECT completion_status FROM student_progress WHERE studentID = ? LIMIT 1
");
$progressCheck->bind_param("s", $studentID);
$progressCheck->execute();
$progressData = $progressCheck->get_result()->fetch_assoc();

if ($progressData && strtoupper($progressData['completion_status']) === 'COMPLETED') {
    respond("OJT already completed. Attendance is no longer allowed.");
}

// validations ss
if ($role === "supervisor") {
    $check = $conn->prepare("
        SELECT 1 FROM student_supervisor
        WHERE studentID = ? AND superID = ? AND status = 'ACTIVE'
        LIMIT 1
    ");
    $check->bind_param("si", $studentID, $superID);
    $check->execute();
    if ($check->get_result()->num_rows == 0) {
        respond("No record of this ID!");
    }
}

/* today log*/
$stmtAttendance = $conn->prepare("
    SELECT * FROM attendance_logs
    WHERE studentID = ? AND log_date = ?
    LIMIT 1
");
$stmtAttendance->bind_param("ss", $studentID, $today);
$stmtAttendance->execute();
$row = $stmtAttendance->get_result()->fetch_assoc();

$state = $row['current_state'] ?? 'NONE';

/* TIME IN  */
if (!$row) {

    if ($current_time < $timeInStart) {
        respond("Too early. Time-in opens at " . date('h:i A', strtotime($timeInStart)) . ".");
    }

    if ($current_time >= $timeOutAfternoonTime) {
        respond("Time-in is closed for today.");
    }

    $lateMinutes = 0;

    if ($current_time > $lateTime) {
        $attStatus   = "late";
        $lateMinutes = (int) round((strtotime($current_time) - strtotime($lateTime)) / 60);
        $remarks     = "Late by {$lateMinutes} minute(s)";
    } else {
        $attStatus = "present";
        $remarks   = "Arrived on time";
    }

    if ($dayType === 'HOLIDAY') {
        $remarks .= " (Holiday: " . ($dayLabel ?: 'Unnamed') . ", {$hourMultiplier}x hours)";
    }

    try {
        $stmtIn = $conn->prepare("
            INSERT INTO attendance_logs (
                studentID, rfid_uid, log_date, first_time_in,
                status, late_minutes, remarks, current_state
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, 'WORKING')
        ");
        $stmtIn->bind_param("sssssis", $studentID, $rfid, $today, $nowDt, $attStatus, $lateMinutes, $remarks);
        try {
            $stmtIn->execute();

            $setStart = $conn->prepare("
                UPDATE student_progress
                SET ojt_start_date = ?
                WHERE studentID = ? AND ojt_start_date IS NULL
            ");
            $setStart->bind_param("ss", $today, $studentID);
            $setStart->execute();
        } catch (mysqli_sql_exception $e) {
            respond($e->getCode() == 1062 ? "Already timed in today." : "Could not record time-in.");
        }
    } catch (mysqli_sql_exception $e) {
        respond($e->getCode() == 1062 ? "Already timed in today." : "Could not record time-in.");
    }

    respond("TIME IN SUCCESS ($attStatus)");
}

/* FINAL TIME OUT  */
if (in_array($state, ['WORKING', 'LUNCH_BREAK'], true)
    && $current_time >= $timeOutAfternoonTime
    && !$row['final_time_out']) {

    $firstIn       = strtotime($row['first_time_in']);
    $workedMinutes = ($nowTs - $firstIn) / 60;

    if ($workedMinutes < $MIN_WORK_MINUTES) {
        $remaining = ceil($MIN_WORK_MINUTES - $workedMinutes);
        respond("You must work at least {$MIN_WORK_MINUTES} minutes before time out. Wait {$remaining} more minute(s).");
    }

    $totalWorkedSeconds = $nowTs - $firstIn;

    // Lunch bawas
    if ($row['lunch_break_out']) {
        $lunchOut = strtotime($row['lunch_break_out']);

        if ($row['lunch_break_in']) {
            $totalWorkedSeconds -= (strtotime($row['lunch_break_in']) - $lunchOut);
        } else {
    
            $totalWorkedSeconds -= min($nowTs - $lunchOut, $lunchBreakTime * 60);
        }
    }

    // bawas
    if ($row['snack_break_out'] && $row['snack_break_in']) {
        $totalWorkedSeconds -= (strtotime($row['snack_break_in']) - strtotime($row['snack_break_out']));
    }

    $totalHours = round(max($totalWorkedSeconds, 0) / 3600, 2);

    if ($totalHours > $MAX_HOURS_PER_DAY) {
        $totalHours = $MAX_HOURS_PER_DAY;
    }

    $totalHours    = roundHoursWithThreshold($totalHours);
    $creditedHours = round($totalHours * $hourMultiplier, 2);
    $curStmt = $conn->prepare("SELECT COALESCE(completed_hours,0) AS done, required_hours FROM student_progress WHERE studentID = ?");
    $curStmt->bind_param("s", $studentID);
    $curStmt->execute();
    $cur = $curStmt->get_result()->fetch_assoc();

    if ($cur) {
        $room = max(0, round($cur['required_hours'] - $cur['done'], 2));
        $creditedHours = min($creditedHours, $room);
    }

    $remarks = "Completed {$totalHours} hours for the day";
    if ($hourMultiplier != 1.0) {
        $remarks .= " — credited as {$creditedHours}h ({$hourMultiplier}x" . ($dayLabel ? ", {$dayLabel}" : "") . ")";
    }
    if ($cur && $creditedHours < round($totalHours * $hourMultiplier, 2)) {
        $remarks .= " — capped at {$creditedHours}h (OJT hours reached)";
    }

    $stmt = $conn->prepare("
        UPDATE attendance_logs
        SET final_time_out = ?, current_state = 'TIMED_OUT',
            total_hours = ?, remarks = ?
        WHERE attendanceID = ?
    ");
    $stmt->bind_param("sdsi", $nowDt, $creditedHours, $remarks, $row['attendanceID']);
    $stmt->execute();

    $updateProgress = $conn->prepare("
        UPDATE student_progress
        SET completed_hours   = LEAST(required_hours, COALESCE(completed_hours, 0) + ?),
            completion_status = IF(completed_hours >= required_hours, 'COMPLETED', completion_status),
            ojt_end_date      = IF(completed_hours >= required_hours AND ojt_end_date IS NULL, ?, ojt_end_date)
        WHERE studentID = ?
    ");
    $updateProgress->bind_param("dss", $creditedHours, $today, $studentID);
    $updateProgress->execute();

    respond("TIME OUT SUCCESS ({$creditedHours} hrs)" . ($hourMultiplier != 1.0 ? " [{$hourMultiplier}x applied]" : "") . ".");
}

// start lunch
if ($state === 'WORKING' && !$row['lunch_break_out']
    && $current_time >= $timeOutMorningTime && $current_time < $timeInAfternoonTime) {

    $remarks = "Started lunch break";

    $stmt = $conn->prepare("
        UPDATE attendance_logs
        SET lunch_break_out = ?, remarks = ?, current_state = 'LUNCH_BREAK'
        WHERE attendanceID = ?
    ");
    $stmt->bind_param("ssi", $nowDt, $remarks, $row['attendanceID']);
    $stmt->execute();

    respond("LUNCH BREAK STARTED");
}

// lunch break
if ($state === 'LUNCH_BREAK') {

    $breakMinutes = ($nowTs - strtotime($row['lunch_break_out'])) / 60;

    if ($breakMinutes < $lunchBreakTime) {
        $remaining = ceil($lunchBreakTime - $breakMinutes);
        respond("Lunch break ongoing. Wait {$remaining} minute(s).");
    }

    $remarks = "Returned from lunch break";

    $stmt = $conn->prepare("
        UPDATE attendance_logs
        SET lunch_break_in = ?, remarks = ?, current_state = 'WORKING'
        WHERE attendanceID = ?
    ");
    $stmt->bind_param("ssi", $nowDt, $remarks, $row['attendanceID']);
    $stmt->execute();

    respond("RETURNED FROM LUNCH");
}

switch ($state) {
    case 'TIMED_OUT':
        respond("You are already timed out today.");
    case 'SNACK_BREAK':
        respond("Snack break is not supported by the scanner yet. Ask your supervisor to fix the record.");
    case 'VOIDED':
        respond("Today's attendance was voided. Contact your supervisor.");
    case 'INCOMPLETE':
        respond("Today's attendance is marked incomplete. Contact your supervisor.");
    case 'WORKING':
        if (!$row['lunch_break_out'] && $current_time < $timeOutMorningTime) {
            respond("Too early for lunch. Lunch opens at " . date('h:i A', strtotime($timeOutMorningTime)) . ".");
        }
        respond("Too early to time out. Allowed after " . date('h:i A', strtotime($timeOutAfternoonTime)) . ".");
}

respond("Invalid scan action!");