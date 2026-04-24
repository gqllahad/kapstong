<?php

require_once("../../kapstongConnection.php");

$taskID = $_POST['taskID'] ?? '';

if (empty($taskID)) {
    echo json_encode([
        "status" => "error",
        "message" => "No task selected"
    ]);
    exit;
}

$sql = "
    DELETE FROM student_tasks
    WHERE taskID = '$taskID'
";

if ($conn->query($sql)) {
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
