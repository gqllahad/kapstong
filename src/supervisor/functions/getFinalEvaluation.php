<?php

session_start();
require_once("../../auth/supervisor_auth.php");
require_once("../../Shared/kapstongConnection.php");
require_once("../../Shared/functions.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "UNAUTHORIZED"
    ]);
    exit();
}

$studentID = $_POST['studentID'] ?? null;

if (!$studentID) {
    echo json_encode([
        "success" => false,
        "message" => "STUDENT ID REQUIRED"
    ]);
    exit();
}

$studentStmt = $conn->prepare("
    SELECT studentID, name
    FROM users
    WHERE studentID = ?
");
$studentStmt->bind_param("s", $studentID);
$studentStmt->execute();
$student = $studentStmt->get_result()->fetch_assoc();

$progressStmt = $conn->prepare("
    SELECT required_hours, completed_hours, completion_status
    FROM student_progress
    WHERE studentID = ?
");
$progressStmt->bind_param("s", $studentID);
$progressStmt->execute();
$progress = $progressStmt->get_result()->fetch_assoc();

$required = floatval($progress['required_hours'] ?? 0);
$completed = floatval($progress['completed_hours'] ?? 0);

$attendanceScore = ($required > 0)
    ? min(($completed / $required) * 100, 100)
    : 0;


    $taskStmt = $conn->prepare("
    SELECT 
        title,
        status,
        rating,
        supervisor_feedback,
        completed_at
    FROM student_tasks
    WHERE studentID = ?
    ORDER BY date_updated DESC
");
$taskStmt->bind_param("s", $studentID);
$taskStmt->execute();
$taskResult = $taskStmt->get_result();

$taskList = [];
$totalTasks = 0;
$taskScoreSum = 0;

while ($t = $taskResult->fetch_assoc()) {

    $totalTasks++;

    switch ($t['status']) {
        case 'APPROVED': $statusScore = 100; break;
        case 'SUBMITTED': $statusScore = 75; break;
        case 'IN PROGRESS': $statusScore = 50; break;
        case 'NOT STARTED': $statusScore = 20; break;
        case 'REJECTED': $statusScore = 0; break;
        default: $statusScore = 0;
    }

    $ratingScore = 0;

    if (!empty($t['rating'])) {
        $ratingScore = $t['rating'];
    }

    $taskFinal = ($statusScore * 0.6) + ($ratingScore * 0.4);

    $taskScoreSum += $taskFinal;

    $taskList[] = [
        "title" => $t['title'],
        "status" => $t['status'],
        "rating" => $t['rating'],
        "feedback" => $t['supervisor_feedback'],
        "completed_at" => $t['completed_at'],
        "score" => round($taskFinal, 2)
    ];
}

$taskScore = ($totalTasks > 0)
    ? ($taskScoreSum / $totalTasks)
    : 0;



    $stageStmt = $conn->prepare("
    SELECT status
    FROM internship_stages
    WHERE studentID = ?
");
$stageStmt->bind_param("s", $studentID);
$stageStmt->execute();
$stageResult = $stageStmt->get_result();

$totalStages = 0;
$completedStages = 0;

while ($s = $stageResult->fetch_assoc()) {
    $totalStages++;
    if ($s['status'] === 'COMPLETED') {
        $completedStages++;
    }
}

$progressScore = ($totalStages > 0)
    ? ($completedStages / $totalStages) * 100
    : 0;

    $weightData = $conn->query("
    SELECT attendance_weight, progress_weight, task_weight
    FROM evaluation_settings
    WHERE superID IS NULL
    LIMIT 1
")->fetch_assoc() ?? [];

$attendanceW = floatval($weightData['attendance_weight'] ?? 0.33);
$progressW    = floatval($weightData['progress_weight'] ?? 0.33);
$taskW        = floatval($weightData['task_weight'] ?? 0.33);

$totalW = $attendanceW + $progressW + $taskW;

if ($totalW > 0) {
    $attendanceW /= $totalW;
    $progressW    /= $totalW;
    $taskW        /= $totalW;
}

$finalGrade =
    ($attendanceScore * $attendanceW) +
    ($progressScore * $progressW) +
    ($taskScore * $taskW);


    echo json_encode([
    "success" => true,

    "student" => $student,

    "scores" => [
        "attendance" => round($attendanceScore, 2),
        "progress"   => round($progressScore, 2),
        "tasks"      => round($taskScore, 2),
        "final"      => round($finalGrade, 2)
    ],

    "tasks" => $taskList,

    "weights" => [
        "attendance" => $attendanceW,
        "progress"   => $progressW,
        "tasks"      => $taskW
    ]
]);