<?php
header('Content-Type: application/json');
require_once("../../Shared/kapstongConnection.php");
require_once("../../auth/admin_auth.php");

date_default_timezone_set('Asia/Manila');   

function academicYearFor(string $date): string
{
    $y = (int)date('Y', strtotime($date));
    $m = (int)date('n', strtotime($date));
    return $m >= 6 ? "$y-" . ($y + 1) : ($y - 1) . "-$y";
}

$id       = (int)($_POST['settingID'] ?? 0);
$program  = (int)($_POST['programID'] ?? 0);
$year     = trim($_POST['academic_year'] ?? '');
$semester = trim($_POST['semester'] ?? '') ?: 'N/A';
$hours    = (int)($_POST['required_hours'] ?? 0);
$start    = $_POST['start_date'] ?? '';
$end      = $_POST['end_date'] ?? '';
$status   = ($_POST['status'] ?? '') === 'INACTIVE' ? 'INACTIVE' : 'ACTIVE';

if (!$id || !$program || $year === '' || $hours <= 0 || !$start || !$end) {
    echo json_encode(['success' => false, 'message' => 'Please complete all fields.']);
    exit;
}

if ($end <= $start) {
    echo json_encode(['success' => false, 'message' => 'End date must be after the start date.']);
    exit;
}

if (!in_array($semester, ['1st Semester', '2nd Semester'], true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid semester.']);
    exit;
}

if ($year !== academicYearFor($start)) {
    echo json_encode(['success' => false, 'message' => 'Academic year does not match the start date.']);
    exit;
}

if ($start < date('Y-m-d')) {
    echo json_encode(['success' => false, 'message' => 'Start date cannot be in the past.']);
    exit;
}

$stmt = $conn->prepare("UPDATE ojt_settings
    SET programID = ?, academic_year = ?, semester = ?, required_hours = ?,
        start_date = ?, end_date = ?, status = ?
    WHERE settingID = ?");
$stmt->bind_param("ississsi", $program, $year, $semester, $hours, $start, $end, $status, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'OJT settings updated.']);
} else {
    $msg = $conn->errno == 1062
        ? 'Settings for that program and term already exist.'
        : 'Failed to update settings.';
    echo json_encode(['success' => false, 'message' => $msg]);
}