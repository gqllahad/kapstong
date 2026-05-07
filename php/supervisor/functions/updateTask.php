<?php
require_once("../../auth/supervisor_auth.php");
require_once("../../kapstongConnection.php");

$taskID = $_POST['taskID'] ?? '';
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$due_date = $_POST['due_date'] ?? '';
$status = $_POST['status'] ?? '';

if (
    empty($taskID) ||
    empty($title) ||
    empty($due_date) ||
    empty($status)
) {
    echo json_encode([
        "status" => "error",
        "message" => "Please complete required fields"
    ]);
    exit;
}

$stmt = $conn->prepare("
    UPDATE student_tasks
    SET title = ?, description = ?, due_date = ?, status = ?
    WHERE taskID = ?
");

$stmt->bind_param(
    "ssssi",
    $title,
    $description,
    $due_date,
    $status,
    $taskID
);

$getTask = $conn->prepare("
    SELECT studentID
    FROM student_tasks
    WHERE taskID = ?
");

$getTask->bind_param("i", $taskID);
$getTask->execute();
$result = $getTask->get_result();
$task = $result->fetch_assoc();

if ($stmt->execute()) {

    $ip = getUserIP();
    $userID = $_SESSION['user_id'];
    $role = "SUPERVISOR";
    $action = "Update Task";
    $module = "TASK";

    $description = "Updated task '$title' (ID: $taskID) assigned to student ID {$task['studentID']}";

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
        "message" => "Task updated successfully"
    ]);

} else {
    echo json_encode([
        "status" => "error",
        "message" => $conn->error
    ]);
}
