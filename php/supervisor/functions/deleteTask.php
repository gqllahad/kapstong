<?php
require_once("../../auth/supervisor_auth.php");
require_once("../../kapstongConnection.php");

// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supervisor') {
//     http_response_code(403);
//     echo json_encode(["error" => "Unauthorized"]);
//     exit;
// }

$taskID = $_POST['taskID'] ?? '';

if (empty($taskID)) {
    echo json_encode([
        "status" => "error",
        "message" => "No task selected"
    ]);
    exit;
}

$getTask = $conn->prepare("
    SELECT title, studentID
    FROM student_tasks
    WHERE taskID = ?
    LIMIT 1
");

$getTask->bind_param("i", $taskID);
$getTask->execute();
$result = $getTask->get_result();
$task = $result->fetch_assoc();

if (!$task) {
    echo json_encode([
        "status" => "error",
        "message" => "Task not found"
    ]);
    exit;
}

$stmt = $conn->prepare("
    DELETE FROM student_tasks
    WHERE taskID = ?
");

$stmt->bind_param("i", $taskID);

if ($stmt->execute()) {

    $ip = getUserIP();
    $userID = $_SESSION['user_id'];
    $role = "SUPERVISOR";
    $action = "Delete Task";
    $module = "TASK";

    $description = "Deleted task '{$task['title']}' assigned to student ID {$task['studentID']}";

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

    echo json_encode([
        "status" => "success",
        "message" => "Task deleted successfully"
    ]);

} else {
    echo json_encode([
        "status" => "error",
        "message" => $conn->error
    ]);
}
