<?php

require_once("../../auth/supervisor_auth.php");
require_once("../../kapstongConnection.php");
require_once("../../functions.php");

header('Content-Type: application/json');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$taskID = $_POST['taskID'] ?? '';
$status = $_POST['status'] ?? '';
$feedback = $_POST['supervisor_feedback'] ?? '';

if (empty($taskID) || empty($status)) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing required data"
    ]);
    exit;
}

if ($status === 'REJECTED' && empty($feedback)) {
    echo json_encode([
        "status" => "error",
        "message" => "Supervisor feedback is required when rejecting a task"
    ]);
    exit;
}

if ($status === 'APPROVED') {

    $stmt = $conn->prepare("
        UPDATE student_tasks
        SET 
            status = ?,
            supervisor_feedback = ?,
            completed_at = NOW(),
            date_updated = NOW()
        WHERE taskID = ?
    ");

    $stmt->bind_param("ssi", $status, $feedback, $taskID);
} else {

    $stmt = $conn->prepare("
        UPDATE student_tasks
        SET 
            status = ?,
            supervisor_feedback = ?,
            completed_at = NULL,
            date_updated = NOW()
        WHERE taskID = ?
    ");

    $stmt->bind_param("ssi", $status, $feedback, $taskID);
}

$get = $conn->prepare("
    SELECT studentID, title
    FROM student_tasks
    WHERE taskID = ?
");

$get->bind_param("i", $taskID);
$get->execute();
$result = $get->get_result();
$task = $result->fetch_assoc();

if ($stmt->execute()) {

    $ip = getUserIP();
    $userID = $_SESSION['user_id'];
    $role = "SUPERVISOR";
    $module = "TASK";

    if ($status === "APPROVED") {

        $action = "Approve Task";

        $description = "Approved task '{$task['title']}' for student ID {$task['studentID']}.";
    } else {

        $action = "Reject Task";

        $description = "Rejected task '{$task['title']}' for student ID {$task['studentID']} with feedback: {$feedback}";
    }

    $target_type = "task";
    $target_id = $taskID;

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
        "isssssis",
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

    echo json_encode([
        "status" => "success",
        "message" => "Task updated successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Update failed"
    ]);
}
