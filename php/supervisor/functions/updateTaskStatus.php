<?php
require_once("../../kapstongConnection.php");

$taskID = $_POST['taskID'] ?? '';
$status = $_POST['status'] ?? '';
$feedback = $_POST['supervisor_feedback'] ?? '';
$rating = $_POST['rating'] ?? '';

if (empty($taskID) || empty($status) || empty($rating) || empty($feedback)) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing data"
    ]);
    exit;
}

$stmt = $conn->prepare("
    UPDATE student_tasks
    SET 
        status = ?,
        supervisor_feedback = ?,
         rating = ?,
        completed_at = NOW(),
        date_updated = NOW()
    WHERE taskID = ?
");

$stmt->bind_param("sssi", $status, $feedback, $rating, $taskID);

if ($stmt->execute()) {
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
