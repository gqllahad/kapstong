<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once("../auth/auth_guard.php");
require_once("../Shared/kapstongConnection.php");
require_once("../Shared/functions.php");

requireRole(['ADMIN', 'supervisor']);
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json');

function out(bool $ok, string $msg): void {
    echo json_encode(['success' => $ok, 'message' => $msg]);
    exit;
}



$attendanceID = (int) ($_POST['attendanceID'] ?? 0);
$action       = $_POST['action'] ?? '';         
$timeOutInput = $_POST['time_out'] ?? '';        
$role         = $_SESSION['role'];
$superID      = $_SESSION['superID'] ?? null;

if (!$attendanceID || !in_array($action, ['acknowledge', 'count', 'void'], true)) {
    out(false, "Invalid request.");
}
$note = trim($_POST['note'] ?? '');

$MAX_HOURS_PER_DAY = (int) getAttendanceSetting($conn, 'max_hours_per_day', 8);
$lunchStart = getAttendanceSetting($conn, 'morning_time_out', '12:00:00');
$lunchBreakTime    = (int) getAttendanceSetting($conn, 'lunch_break_minutes', 60);
$defaultTimeOut    = getAttendanceSetting($conn, 'afternoon_time_out', '17:00:00');
$afternoonIn       = getAttendanceSetting($conn, 'afternoon_time_in', '13:00:00');

$validTime = fn($t) => (bool) preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $t);

$conn->begin_transaction();
try {
  
    $s = $conn->prepare("SELECT * FROM attendance_logs WHERE attendanceID = ? FOR UPDATE");
    $s->bind_param("i", $attendanceID);
    $s->execute();
    $row = $s->get_result()->fetch_assoc();

    if (!$row || $row['current_state'] !== 'INCOMPLETE' || $row['final_time_out'] !== null) {
        $conn->rollback();
        out(false, "This log is not waiting for review.");
    }

    $studentID = $row['studentID'];

    if ($action === 'acknowledge') {
        if ($role !== 'supervisor') { $conn->rollback(); out(false, "Only supervisors can acknowledge."); }

        $c = $conn->prepare("SELECT 1 FROM student_supervisor
                            WHERE studentID = ? AND superID = ? AND status = 'ACTIVE' LIMIT 1");
        $c->bind_param("si", $studentID, $superID);
        $c->execute();
        if ($c->get_result()->num_rows == 0) { $conn->rollback(); out(false, "Not your student."); }

        if (!$validTime($timeOutInput)) { $conn->rollback(); out(false, "Enter a valid time out."); }
        $proposed = $timeOutInput . ':00';

        if (strtotime($row['log_date'] . ' ' . $proposed) <= strtotime($row['first_time_in'])) {
            $conn->rollback(); out(false, "Time out must be after time in.");
        }

        $a = $conn->prepare("UPDATE attendance_logs
                            SET ack_by = ?, ack_at = ?, ack_time_out = ?, ack_note = ?
                            WHERE attendanceID = ?");
        $nowDt = date('Y-m-d H:i:s');
        $a->bind_param("isssi", $superID, $nowDt, $proposed, $note, $attendanceID);
        $a->execute();
        $conn->commit();
        out(true, "Acknowledged. Waiting for admin approval.");
    }

    if ($role !== 'ADMIN') { $conn->rollback(); out(false, "Only an admin can approve."); }

    if ($action === 'void') {
        $v = $conn->prepare("UPDATE attendance_logs
                            SET status = 'voided', current_state = 'VOIDED',
                                remarks = 'Voided by admin (no time out)'
                            WHERE attendanceID = ?");
        $v->bind_param("i", $attendanceID);
        $v->execute();
        $conn->commit();
        out(true, "Log voided.");
    }

    if ($row['ack_at']) { $conn->rollback(); out(false, "Already acknowledged."); }

    if ($timeOutInput !== '' && !$validTime($timeOutInput)) { $conn->rollback(); out(false, "Invalid time."); }

    $timeOutTime = $timeOutInput !== '' ? $timeOutInput . ':00': ($row['ack_time_out'] ?: $defaultTimeOut);
    $timeOutDt   = $row['log_date'] . ' ' . $timeOutTime;

    $firstIn = strtotime($row['first_time_in']);
    $outTs   = strtotime($timeOutDt);

    if ($outTs <= $firstIn) { $conn->rollback(); out(false, "Time out must be after time in."); }

    $worked = $outTs - $firstIn;

    // lunch bawas
    if ($row['lunch_break_out']) {
        $lo = strtotime($row['lunch_break_out']);
        $worked -= $row['lunch_break_in']
            ? (strtotime($row['lunch_break_in']) - $lo)
            : min($outTs - $lo, $lunchBreakTime * 60);
    } elseif (date('H:i:s', $firstIn) < $lunchStart && $timeOutTime > $afternoonIn) {
        $worked -= $lunchBreakTime * 60;
    }

    $hours = round(max($worked, 0) / 3600, 2);
    $hours = min($hours, $MAX_HOURS_PER_DAY);
    $hours = roundHoursWithThreshold($hours);

    $m = $conn->prepare("SELECT hour_multiplier FROM calendar_events WHERE event_date = ?");
    $m->bind_param("s", $row['log_date']);
    $m->execute();
    $ev = $m->get_result()->fetch_assoc();
    $multiplier = $ev ? (float) $ev['hour_multiplier'] : 1.0;

    $credited = round($hours * $multiplier, 2);

    $p = $conn->prepare("SELECT COALESCE(completed_hours,0) AS done, required_hours
                         FROM student_progress WHERE studentID = ? FOR UPDATE");
    $p->bind_param("s", $studentID);
    $p->execute();
    $cur = $p->get_result()->fetch_assoc();

    if ($cur) {
        $room = max(0, round($cur['required_hours'] - $cur['done'], 2));
        $credited = min($credited, $room);
    }

    $remarks = "Time out set by admin ({$timeOutTime}), acknowledged by supervisor #{$row['ack_by']}, credited {$credited}h";

    $u = $conn->prepare("UPDATE attendance_logs
                         SET final_time_out = ?, current_state = 'TIMED_OUT',
                             total_hours = ?, remarks = ?
                         WHERE attendanceID = ?");
    $u->bind_param("sdsi", $timeOutDt, $credited, $remarks, $attendanceID);
    $u->execute();

    $up = $conn->prepare("
        UPDATE student_progress
        SET completed_hours   = LEAST(required_hours, COALESCE(completed_hours, 0) + ?),
            completion_status = IF(completed_hours >= required_hours, 'COMPLETED', completion_status),
            ojt_end_date      = IF(completed_hours >= required_hours AND ojt_end_date IS NULL, ?, ojt_end_date)
        WHERE studentID = ?
    ");
    $up->bind_param("dss", $credited, $row['log_date'], $studentID);
    $up->execute();

    $conn->commit();
    out(true, "Timed out at " . date('h:i A', $outTs) . " and credited {$credited} hrs.");

} catch (Throwable $e) {
    $conn->rollback();
    error_log("[resolve-log] " . $e->getMessage());
    out(false, "Could not resolve this log.");
}