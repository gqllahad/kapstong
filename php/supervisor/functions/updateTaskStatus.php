<?php
require_once("../../kapstongConnection.php");

$taskID = $_POST['taskID'] ?? '';
$status = $_POST['status'] ?? '';
$feedback = $_POST['supervisor_feedback'] ?? '';
$rating = $_POST['rating'] ?? '';


if (empty($taskID) || empty($status)) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing required data"
    ]);
    exit;
}

if ($status === "APPROVED" && empty($rating) && empty($feedback)) {
    echo json_encode([
        "status" => "error",
        "message" => "Rating is required when approving a task"
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
            rating = ?,
            completed_at = NOW(),
            date_updated = NOW()
        WHERE taskID = ?
    ");

    $stmt->bind_param("sssi", $status, $feedback, $rating, $taskID);
} else {

    $stmt = $conn->prepare("
        UPDATE student_tasks
        SET 
            status = ?,
            supervisor_feedback = ?,
            rating = ?,
            completed_at = NULL,
            date_updated = NOW()
        WHERE taskID = ?
    ");

    $stmt->bind_param("sssi", $status, $feedback, $rating, $taskID);
}


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
