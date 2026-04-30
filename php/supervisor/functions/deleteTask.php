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
