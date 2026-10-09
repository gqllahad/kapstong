<?php
header('Content-Type: application/json');
require_once("../../Shared/kapstongConnection.php");
require_once("../../auth/admin_auth.php");

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