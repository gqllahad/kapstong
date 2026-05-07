<?php
require_once("../../kapstongConnection.php");
require_once("../../functions.php");

header('Content-Type: application/json');

$studentID = $_POST['studentID'] ?? '';

if (empty($studentID)) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing student ID"
    ]);
    exit;
}

$stmt = $conn->prepare("
    UPDATE student_supervisor
    SET status = 'REMOVED',
        date_updated = NOW()
    WHERE studentID = ?
    AND status = 'ACTIVE'
");

$stmt->bind_param("s", $studentID);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Student unassigned successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to unassign student"
    ]);
}