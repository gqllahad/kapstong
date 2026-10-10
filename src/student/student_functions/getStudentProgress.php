<?php
header('Content-Type: application/json');
require_once("../../auth/student_auth.php");
require_once("../../Shared/kapstongConnection.php");
require_once("../../Shared/ojtForecast.php");

date_default_timezone_set('Asia/Manila');

$empty = [
    "completed" => 0, "required" => 0, "remaining" => 0,
    "forecast" => "NOT STARTED", "projected_finish" => null, "deadline" => null,
    "gap_days" => null, "required_pace" => null, "weekly_pace" => 0
];

$studentID = $_SESSION['studentID'] ?? '';

if (empty($studentID)) {
    echo json_encode($empty);
    exit;
}

$stmt = $conn->prepare("
    SELECT p.completed_hours, p.required_hours, p.ojt_start_date,
           s.end_date AS deadline
    FROM student_progress p
    LEFT JOIN ojt_settings s ON s.settingID = p.settingID
    WHERE p.studentID = ?
    LIMIT 1
");
$stmt->bind_param("s", $studentID);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    echo json_encode($empty);
    exit;
}

$completed = (float)$row['completed_hours'];
$required  = (float)$row['required_hours'];

$f = computeForecast($required, $completed, $row['ojt_start_date'], $row['deadline'],
                     loadPaceFor($conn, $studentID));

echo json_encode([
    "completed" => $completed,
    "required"  => $required,
    "remaining" => max($required - $completed, 0),

    "forecast"         => $f['forecast'],
    "projected_finish" => $f['projected'],
    "deadline"         => $row['deadline'],
    "gap_days"         => $f['gap_days'],
    "required_pace"    => $f['required_pace'],
    "weekly_pace"      => round($f['weekly'], 1),
]);