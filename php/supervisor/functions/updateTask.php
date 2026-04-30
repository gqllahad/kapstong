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

$sql = "
    UPDATE student_tasks
    SET
        title = '$title',
        description = '$description',
        due_date = '$due_date',
        status = '$status'
    WHERE taskID = '$taskID'
";

if ($conn->query($sql)) {
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
