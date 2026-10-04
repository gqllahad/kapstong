<?php

header('Content-Type: application/json');
require_once("../../auth/supervisor_auth.php");
require_once("../../Shared/kapstongConnection.php");
require_once("../../Shared/functions.php");

enforceSessionTimeout('supervisor');
$superID = getSupervisorIDByUserID($conn, $_SESSION['user_id']);

$studentID = $_POST['studentID'] ?? '';
$alertType = $_POST['alertType'] ?? '';

$typeMap = [
    'warning'  => 'INACTIVE_ATTENDANCE',
    'critical' => 'INACTIVE_ATTENDANCE',
    'danger'   => 'OVERDUE_TASK'
];

if (!isset($typeMap[$alertType])) {
    echo json_encode(["status" => "error", "message" => "Invalid type"]);
    exit;
}
$warningType = $typeMap[$alertType];

$stmt = $conn->prepare("SELECT 1 FROM student_supervisor WHERE studentID = ? AND superID = ? AND status = 'ACTIVE'");
$stmt->bind_param("si", $studentID, $superID);
$stmt->execute();
if (!$stmt->get_result()->fetch_assoc()) {
    echo json_encode(["status" => "error", "message" => "Not your student"]);
    exit;
}

$stmt = $conn->prepare("SELECT 1 FROM warning_logs WHERE studentID = ? AND warning_type = ? AND sent_at > NOW() - INTERVAL 24 HOUR");
$stmt->bind_param("ss", $studentID, $warningType);
$stmt->execute();
if ($stmt->get_result()->fetch_assoc()) {
    echo json_encode(["status" => "error", "message" => "Already reminded in the last 24 hours"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO warning_logs (studentID, superID, warning_type) VALUES (?, ?, ?)");
$stmt->bind_param("sis", $studentID, $superID, $warningType);
$stmt->execute();

echo json_encode(["status" => "ok"]);