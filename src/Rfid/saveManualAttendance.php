<?php
session_start();
require_once("../Shared/kapstongConnection.php");
require_once("../Shared/functions.php");
require_once("../auth/auth_guard.php");

requireRole(['ADMIN', 'supervisor']);

header('Content-Type: application/json');

function respond($success, $message) {
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

$role = $_SESSION['role'];
$userID = $_SESSION['user_id'] ?? null;
$superID = $_SESSION['superID'] ?? null; 

$studentID = trim($_POST['studentID'] ?? '');
$date = trim($_POST['date'] ?? '');
$status = trim($_POST['status'] ?? '');
$timeIn = trim($_POST['timeIn'] ?? '');
$timeOut = trim($_POST['timeOut'] ?? '');
$breakMinutes = (int) ($_POST['breakTime'] ?? 0);
$reason = trim($_POST['reason'] ?? '');

if (!$studentID || !$date || !$status || !$timeIn || !$timeOut || $reason === '') {
    respond(false, 'All required fields must be filled out.');
}

$allowedStatuses = ['present', 'late', 'excused'];
if (!in_array($status, $allowedStatuses)) {
    respond(false, 'Invalid status.');
}

if (!$userID) {
    respond(false, 'Session not found. Please log in again.');
}

if ($role === 'supervisor') {
    if (!$superID) {
        respond(false, 'Supervisor account not properly linked.');
    }

    $check = $conn->prepare("
        SELECT 1 FROM student_supervisor 
        WHERE studentID = ? AND superID = ? AND status = 'ACTIVE'
        LIMIT 1
    ");
    $check->bind_param("si", $studentID, $superID);
    $check->execute();
    if ($check->get_result()->num_rows === 0) {
        respond(false, 'This student is not assigned to you.');
    }
}

$dupCheck = $conn->prepare("
    SELECT attendanceID FROM attendance_logs 
    WHERE studentID = ? AND log_date = ?
    LIMIT 1
");
$dupCheck->bind_param("ss", $studentID, $date);
$dupCheck->execute();
if ($dupCheck->get_result()->num_rows > 0) {
    respond(false, 'An attendance record already exists for this student on this date.');
}

$firstTimeIn = "$date $timeIn:00";
$finalTimeOut = "$date $timeOut:00";

$timeInSeconds = strtotime($firstTimeIn);
$timeOutSeconds = strtotime($finalTimeOut);

if ($timeOutSeconds <= $timeInSeconds) {
    respond(false, 'Time out must be after time in.');
}

$workedSeconds = $timeOutSeconds - $timeInSeconds - ($breakMinutes * 60);

if ($workedSeconds < 0) {
    respond(false, 'Break duration exceeds total time worked.');
}

$maxPerDay  = (int) getAttendanceSetting($conn, 'max_hours_per_day', 8);
$totalHours = roundHoursWithThreshold(min(round($workedSeconds / 3600, 2), $maxPerDay));

$ownerStmt = $conn->prepare("SELECT superID FROM student_supervisor WHERE studentID = ? AND status = 'ACTIVE' LIMIT 1");
$ownerStmt->bind_param("s", $studentID);
$ownerStmt->execute();
$studentSuperID = (int)($ownerStmt->get_result()->fetch_column() ?: 0);

$calStmt = $conn->prepare("
    SELECT hour_multiplier FROM calendar_events
    WHERE event_date = ? AND superID IN (?, 0) AND studentID IN (?, '')
    ORDER BY (studentID <> '') DESC, superID DESC LIMIT 1
");
$calStmt->bind_param("sis", $date, $studentSuperID, $studentID);
$calStmt->execute();
$cal = $calStmt->get_result()->fetch_assoc();
$hourMultiplier = $cal ? (float)$cal['hour_multiplier'] : 1.0;

$pg = $conn->prepare("SELECT COALESCE(completed_hours,0) AS done, required_hours, completion_status FROM student_progress WHERE studentID = ?");
$pg->bind_param("s", $studentID);
$pg->execute();
$prog = $pg->get_result()->fetch_assoc();

if ($prog && strtoupper($prog['completion_status']) === 'COMPLETED') {
    respond(false, 'OJT already completed.');
}

$totalHours = round($totalHours * $hourMultiplier, 2);
if ($prog) {
    $totalHours = min($totalHours, max(0, round($prog['required_hours'] - $prog['done'], 2)));
}
$roleLabel = ($role === 'ADMIN') ? 'Admin' : 'Supervisor';
$remarks = "Manually recorded by {$roleLabel}: " . ucfirst($status) . " (break: {$breakMinutes} min)";

$stmt = $conn->prepare("
    INSERT INTO attendance_logs (
        studentID, log_date, first_time_in, final_time_out,
        status, remarks, total_hours, current_state,
        entry_method, manual_entered_by, manual_reason
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, 'TIMED_OUT', 'MANUAL', ?, ?)
");

$stmt->bind_param(
    "ssssssdis",
    $studentID,
    $date,
    $firstTimeIn,
    $finalTimeOut,
    $status,
    $remarks,
    $totalHours,
    $userID,  
    $reason
);

if ($stmt->execute()) {

    $up = $conn->prepare("
        UPDATE student_progress
        SET completed_hours   = LEAST(required_hours, COALESCE(completed_hours, 0) + ?),
            completion_status = IF(completed_hours >= required_hours, 'COMPLETED', completion_status),
            ojt_start_date    = IF(ojt_start_date IS NULL OR ? < ojt_start_date, ?, ojt_start_date),
            ojt_end_date      = IF(completed_hours >= required_hours AND ojt_end_date IS NULL, ?, ojt_end_date)
        WHERE studentID = ?
    ");
    $up->bind_param("dssss", $totalHours, $date, $date, $date, $studentID);
    $up->execute();

    $activityStmt = $conn->prepare("
        INSERT INTO activity_log 
        (userID, role, action, module, description, target_type, target_id, ip_address)
        VALUES (?, ?, 'Manual Attendance Entry', 'ATTENDANCE', ?, 'student', ?, ?)
    ");
    $description = "Manually recorded attendance for student {$studentID} on {$date} by {$roleLabel}. Reason: {$reason}";
    $ip = $_SERVER['REMOTE_ADDR'];
    $activityStmt->bind_param("issss", $userID, $role, $description, $studentID, $ip);
    $activityStmt->execute();

    respond(true, 'Attendance recorded successfully.');
} else {
    respond(false, 'Database error. Please try again.');
}