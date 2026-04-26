<?php

header('Content-Type: application/json');
require_once("../../kapstongConnection.php");

$taskID = $_GET['taskID'] ?? '';

if (empty($taskID)) {
    echo json_encode(["error" => "No task ID"]);
    exit;
}

$stmt = $conn->prepare("
    SELECT *
    FROM student_tasks
    WHERE taskID = ?
    LIMIT 1
");

$stmt->bind_param("i", $taskID);
$stmt->execute();

$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    echo json_encode(["error" => "Task not found"]);
    exit;
}

$row = $result->fetch_assoc();

$progress = 0;

switch ($row['status']) {
    case 'NOT STARTED':
        $progress = 0;
        break;
    case 'IN PROGRESS':
        $progress = 40;
        break;
    case 'SUBMITTED':
        $progress = 70;
        break;
    case 'APPROVED':
        $progress = 100;
        break;
    case 'REJECTED':
        $progress = 0;
        break;
}

echo json_encode([
    "taskID" => $row['taskID'],
    "title" => $row['title'],
    "description" => $row['description'],
    "status" => $row['status'],
    "due_date" => date("M d, Y", strtotime($row['due_date'])),
    "progress" => $progress,
    "student_note" => $row['student_note'],
    "supervisor_feedback" => $row['supervisor_feedback'],
    "rating" => $row['rating'],
    "submission_file" => $row['submission_file'],
    "studentID" => $row['studentID'],
    "completed_at" => $row['completed_at']
        ? date("M d, Y", strtotime($row['completed_at']))
        : null
]);
