<?php
header('Content-Type: application/json');
require_once("../../auth/supervisor_auth.php");
require_once("../../Shared/kapstongConnection.php");
require_once("../../Shared/functions.php");

enforceSessionTimeout('supervisor');
$superID = getSupervisorIDByUserID($conn, $_SESSION['user_id']);
$studentID = $_POST['studentID'] ?? '';

// 1. must be this supervisor's active student
$stmt = $conn->prepare("SELECT 1 FROM student_supervisor WHERE studentID = ? AND superID = ? AND status = 'ACTIVE'");
$stmt->bind_param("si", $studentID, $superID);
$stmt->execute();
if (!$stmt->get_result()->fetch_assoc()) {
    echo json_encode(["status" => "error", "message" => "Not your student"]);
    exit;
}

// 2. don't escalate the same student twice within 7 days
$stmt = $conn->prepare("SELECT 1 FROM warning_logs WHERE studentID = ? AND warning_type = 'ESCALATED_TO_HR' AND sent_at > NOW() - INTERVAL 7 DAY");
$stmt->bind_param("s", $studentID);
$stmt->execute();
if ($stmt->get_result()->fetch_assoc()) {
    echo json_encode(["status" => "error", "message" => "Already escalated this week"]);
    exit;
}

// 3. log it
$stmt = $conn->prepare("INSERT INTO warning_logs (studentID, superID, warning_type) VALUES (?, ?, 'ESCALATED_TO_HR')");
$stmt->bind_param("sis", $studentID, $superID);
$stmt->execute();

echo json_encode(["status" => "ok"]);