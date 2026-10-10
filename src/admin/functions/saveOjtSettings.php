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

$program  = (int)($_POST['programID'] ?? 0);
$year     = trim($_POST['academic_year'] ?? '');
$semester = trim($_POST['semester'] ?? '');
$hours    = (int)($_POST['required_hours'] ?? 0);
$start    = $_POST['start_date'] ?? '';
$end      = $_POST['end_date'] ?? '';
$status   = ($_POST['status'] ?? '') === 'INACTIVE' ? 'INACTIVE' : 'ACTIVE';

if (!$program || $year === '' || $hours <= 0 || !$start || !$end) {
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

if ($status === 'ACTIVE') {
    $ov = $conn->prepare("SELECT academic_year, semester, end_date
                          FROM ojt_settings
                          WHERE programID = ?
                            AND status = 'ACTIVE'
                            AND start_date <= ?
                            AND end_date   >= ?
                          LIMIT 1");
    $ov->bind_param("iss", $program, $end, $start);
    $ov->execute();
    $ov->bind_result($ovYear, $ovSem, $ovEnd);

    if ($ov->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => "This program already has an active setting for those dates ($ovYear, $ovSem, ends "
                         . date('M d, Y', strtotime($ovEnd)) . "). Pick dates after that, or save this one as inactive."
        ]);
        exit;
    }
    $ov->close();
}

try {
    $stmt = $conn->prepare("INSERT INTO ojt_settings
        (programID, academic_year, semester, required_hours, start_date, end_date, status)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ississs", $program, $year, $semester, $hours, $start, $end, $status);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'OJT settings created.']);
} catch (mysqli_sql_exception $e) {
    $msg = $e->getCode() == 1062
        ? 'Settings for that program and term already exist.'
        : 'Failed to create settings.';
    echo json_encode(['success' => false, 'message' => $msg]);
}

$conn->close();
