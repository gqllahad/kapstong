<?php
session_start();
require_once("../../auth/supervisor_auth.php");
require_once("../../Shared/kapstongConnection.php");

header('Content-Type: application/json');

$superID = $_SESSION['superID'] ?? null;

if (!$superID) {
    echo json_encode(["pending_tasks" => 0]);
    exit;
}

$taskStmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM student_tasks
    WHERE superID = ?
    AND status = 'SUBMITTED'
");
$taskStmt->bind_param("i", $superID);
$taskStmt->execute();
$pendingTasks = (int)$taskStmt->get_result()->fetch_assoc()['total'];

echo json_encode([
    "pending_tasks" => $pendingTasks
]);