<?php

header('Content-Type: application/json');
require_once("../../kapstongConnection.php");

$studentID = $_GET['studentID'] ?? '';

if (empty($studentID)) {
    echo json_encode(["error" => "No student ID"]);
    exit;
}

$attQuery = "
SELECT 
    COUNT(*) as total,
    SUM(status='present') as present,
    SUM(status='late') as late,
    SUM(status='absent') as absent,
    SUM(status='excused') as excused
FROM attendance_logs
WHERE studentID = '$studentID'
";

$att = $conn->query($attQuery)->fetch_assoc();

$totalAttendance = $att['total'] ?? 0;
$present = $att['present'] ?? 0;
$late = $att['late'] ?? 0;
$absent = $att['absent'] ?? 0;
$excused = $att['excused'] ?? 0;

$attendanceScore = ($totalAttendance > 0)
    ? ($present / $totalAttendance) * 100
    : 0;

$progQuery = "
SELECT completed_hours, required_hours
FROM student_progress
WHERE studentID = '$studentID'
LIMIT 1
";

$prog = $conn->query($progQuery)->fetch_assoc();

$completed = $prog['completed_hours'] ?? 0;
$required = $prog['required_hours'] ?? 500;

$progressScore = ($required > 0)
    ? ($completed / $required) * 100
    : 0;

$taskQuery = "
SELECT status
FROM student_tasks
WHERE studentID = '$studentID'
";

$taskResult = $conn->query($taskQuery);

$totalTasks = 0;
$approved = 0;
$submitted = 0;
$inProgress = 0;

while ($row = $taskResult->fetch_assoc()) {
    $totalTasks++;

    switch ($row['status']) {
        case 'APPROVED':
            $approved++;
            break;
        case 'SUBMITTED':
            $submitted++;
            break;
        case 'IN PROGRESS':
            $inProgress++;
            break;
    }
}

$taskScore =
    ($totalTasks > 0)
    ? (($approved * 100) + ($submitted * 75) + ($inProgress * 50)) / $totalTasks
    : 0;

$weightQuery = "
    SELECT attendance_weight, progress_weight, task_weight
    FROM evaluation_settings
    WHERE superID = (
        SELECT superID FROM student_supervisor WHERE studentID = '$studentID' LIMIT 1
    )
    LIMIT 1
";

$weightResult = $conn->query($weightQuery);
$weightRow = $weightResult->fetch_assoc();

$attendanceW = $weightRow['attendance_weight'] ?? 0.20;
$progressW   = $weightRow['progress_weight'] ?? 0.30;
$taskW       = $weightRow['task_weight'] ?? 0.50;

$total = $attendanceW + $progressW + $taskW;

if ($total > 0) {
    $attendanceW /= $total;
    $progressW   /= $total;
    $taskW       /= $total;
}

$finalGrade =
    ($attendanceScore * $attendanceW) +
    ($progressScore * $progressW) +
    ($taskScore * $taskW);

if ($finalGrade >= 90) $remarks = "EXCELLENT";
else if ($finalGrade >= 80) $remarks = "VERY GOOD";
else if ($finalGrade >= 70) $remarks = "SATISFACTORY";
else if ($finalGrade >= 60) $remarks = "NEEDS IMPROVEMENT";
else $remarks = "FAILED";

echo json_encode([
    "attendance" => [
        "score" => round($attendanceScore, 2),
        "present" => $present,
        "late" => $late,
        "absent" => $absent,
        "excused" => $excused,
        "total" => $totalAttendance
    ],
    "progress" => [
        "completed" => $completed,
        "required" => $required,
        "score" => round($progressScore, 2)
    ],
    "tasks" => [
        "approved" => $approved,
        "submitted" => $submitted,
        "in_progress" => $inProgress,
        "total" => $totalTasks,
        "score" => round($taskScore, 2)
    ],
    "final_grade" => round($finalGrade, 2),
    "remarks" => $remarks
]);
