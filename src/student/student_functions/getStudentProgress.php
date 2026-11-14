<?php
header('Content-Type: application/json');
require_once("../../auth/student_auth.php");
require_once("../../Shared/kapstongConnection.php");
require_once("../../Shared/ojtForecast.php"); 

date_default_timezone_set('Asia/Manila');

$studentID = $_SESSION['studentID'] ?? '';

if (empty($studentID)) {
    echo json_encode(["completed" => 0, "required" => 0, "remaining" => 0, "forecast" => null]);
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
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    echo json_encode(["completed" => 0, "required" => 0, "remaining" => 0, "forecast" => null]);
    exit;
}

$completed = (float) $row['completed_hours'];
$required  = (float) $row['required_hours'];

echo json_encode([
    "completed" => $completed,
    "required"  => $required,
    "remaining" => max($required - $completed, 0),
    "forecast"  => getStudentForecast($conn, $studentID),
]);