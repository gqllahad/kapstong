<?php
session_start();
require_once("../../auth/supervisor_auth.php");
require_once("../../Shared/kapstongConnection.php");

header('Content-Type: application/json');

$superID = $_SESSION['superID'] ?? null;

if (!$superID) {
    echo json_encode(["success" => false, "message" => "Not authenticated."]);
    exit;
}

$stmt = $conn->prepare("
    SELECT file_path FROM supervisor_certificate WHERE superID = ?
");
$stmt->bind_param("i", $superID);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if ($row && file_exists($row['file_path'])) {
    unlink($row['file_path']);
}

$delStmt = $conn->prepare("
    DELETE FROM supervisor_certificate WHERE superID = ?
");
$delStmt->bind_param("i", $superID);
$delStmt->execute();

echo json_encode(["success" => true]);