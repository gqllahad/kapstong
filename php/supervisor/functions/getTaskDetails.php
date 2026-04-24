<?php

require_once("../../kapstongConnection.php");

$taskID = $_GET['taskID'] ?? '';

if (empty($taskID)) {
    echo json_encode([
        "status" => "error",
        "message" => "No task selected"
    ]);
    exit;
}

$sql = "
    SELECT 
        taskID,
        title,
        description,
        due_date,
        status
    FROM student_tasks
    WHERE taskID = '$taskID'
    LIMIT 1
";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Task not found"
    ]);
}
