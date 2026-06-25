<?php
session_start();
header('Content-Type: application/json');
require_once("../../auth/supervisor_auth.php");
require_once("../../Shared/kapstongConnection.php");
require_once("../../Shared/functions.php");

$superID = $_POST['superID'];
$title = $_POST['title'];
$taskDescription = $_POST['description'];
$due_date = $_POST['due_date'];
$studentIDs = $_POST['studentIDs'] ?? [];

if (empty($due_date) || $due_date < date("Y-m-d")) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid due date selected"
    ]);
    exit;
}

$today = date("Y-m-d");

if ($due_date < $today) {
    echo json_encode([
        "status" => "error",
        "message" => "Due date cannot be in the past"
    ]);
    exit;
}


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supervisor') {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

if (empty($superID) || empty($title) || empty($studentIDs)) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing required fields"
    ]);
    exit;
}

$successCount = 0;

foreach ($studentIDs as $studentID) {

    $stmt = $conn->prepare("
        INSERT INTO student_tasks (
            studentID,
            superID,
            title,
            description,
            status,
            due_date
        )
        VALUES (?, ?, ?, ?, 'NOT STARTED', ?)
    ");

    $stmt->bind_param(
        "sisss",
        $studentID,
        $superID,
        $title,
        $taskDescription,
        $due_date
    );

    if ($stmt->execute()) {
        $successCount++;

        $ip = getUserIP();
        $userID = $_SESSION['user_id'];
        $role = "SUPERVISOR";
        $action = "Assign Task";
        $module = "TASK";

        $description = "Assigned task '$title' to student ID $studentID";

        $target_type = "assignment";
        $target_id = $studentID;

        $log = $conn->prepare("
        INSERT INTO activity_log
        (
            userID,
            role,
            action,
            module,
            description,
            target_type,
            target_id,
            ip_address
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

        $log->bind_param(
            "isssssss",
            $userID,
            $role,
            $action,
            $module,
            $description,
            $target_type,
            $target_id,
            $ip
        );

        $log->execute();
    }
}

echo json_encode([
    "status" => "success",
    "message" => "$successCount task(s) created successfully"
]);
