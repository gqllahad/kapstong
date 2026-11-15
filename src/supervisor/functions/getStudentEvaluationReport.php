<?php

header('Content-Type: application/json');
require_once("../../auth/supervisor_auth.php");
require_once("../../Shared/kapstongConnection.php");
require_once("../../Shared/evaluationCalc.php");

$studentID = $_GET['studentID'] ?? '';

if (empty($studentID)) {
    echo json_encode(["error" => "No student ID"]);
    exit;
}

// attendance counts (display only)
$stmt = $conn->prepare("
    SELECT COUNT(*) total,
        SUM(status='present') present,
        SUM(status='late') late,
        SUM(status='absent') absent,
        SUM(status='excused') excused
    FROM attendance_logs
    WHERE studentID = ? AND status NOT IN ('voided','incomplete')
");
$stmt->bind_param("s", $studentID);
$stmt->execute();
$att = $stmt->get_result()->fetch_assoc();

$totalAttendance = (int)($att['total'] ?? 0);
$present = (int)($att['present'] ?? 0);
$late    = (int)($att['late'] ?? 0);
$absent  = (int)($att['absent'] ?? 0);
$excused = (int)($att['excused'] ?? 0);

// hours (display only)
$stmt = $conn->prepare("SELECT completed_hours, required_hours FROM student_progress WHERE studentID = ? LIMIT 1");
$stmt->bind_param("s", $studentID);
$stmt->execute();
$prog = $stmt->get_result()->fetch_assoc();

$completed = $prog['completed_hours'] ?? 0;
$required  = $prog['required_hours'] ?? 500;

// scores
$attendanceScore = calcAttendanceScore($conn, $studentID);
$progressScore   = calcProgressScore($conn, $studentID);

// tasks
$stmt = $conn->prepare("SELECT status FROM student_tasks WHERE studentID = ?");
$stmt->bind_param("s", $studentID);
$stmt->execute();
$taskResult = $stmt->get_result();

$map = ['APPROVED' => 100, 'SUBMITTED' => 75, 'IN PROGRESS' => 50, 'NOT STARTED' => 20, 'REJECTED' => 0];

$totalTasks = 0;
$approved = 0;
$submitted = 0;
$inProgress = 0;
$taskSum = 0;

while ($t = $taskResult->fetch_assoc()) {
    $totalTasks++;
    $taskSum += $map[$t['status']] ?? 0;

    if ($t['status'] === 'APPROVED') $approved++;
    elseif ($t['status'] === 'SUBMITTED') $submitted++;
    elseif ($t['status'] === 'IN PROGRESS') $inProgress++;
}

$taskScore = ($totalTasks > 0) ? $taskSum / $totalTasks : 0;

// weights (default row only)
$weightRow = $conn->query("
    SELECT attendance_weight, progress_weight, task_weight
    FROM evaluation_settings
    WHERE superID IS NULL
    LIMIT 1
")->fetch_assoc() ?? [];

$attendanceW = floatval($weightRow['attendance_weight'] ?? 0.33);
$progressW   = floatval($weightRow['progress_weight'] ?? 0.33);
$taskW       = floatval($weightRow['task_weight'] ?? 0.33);

$totalW = $attendanceW + $progressW + $taskW;
if ($totalW > 0) {
    $attendanceW /= $totalW;
    $progressW   /= $totalW;
    $taskW       /= $totalW;
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