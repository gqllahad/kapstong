<?php
header('Content-Type: application/json');
require_once("../../kapstongConnection.php");

session_start();

$studentID = $_SESSION['studentID'] ?? '';

if (empty($studentID)) {
    echo json_encode([
        "completed" => 0,
        "required" => 0,
        "remaining" => 0
    ]);
    exit;
}

$stmt = $conn->prepare("
    SELECT completed_hours, required_hours
    FROM student_progress
    WHERE studentID = ?
    LIMIT 1
");

$stmt->bind_param("s", $studentID);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo json_encode([
        "completed" => 0,
        "required" => 0,
        "remaining" => 0
    ]);
    exit;
}

$completed = (int)$row['completed_hours'];
$required = (int)$row['required_hours'];
$remaining = max($required - $completed, 0);

echo json_encode([
    "completed" => $completed,
    "required" => $required,
    "remaining" => $remaining
]);

?>