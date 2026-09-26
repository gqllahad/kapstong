<?php
session_start();
require_once("../../auth/supervisor_auth.php");
require_once("../../Shared/kapstongConnection.php");

header('Content-Type: application/json');

$superID = $_SESSION['superID'] ?? null;

if (!$superID) {
    echo json_encode(["exists" => false]);
    exit;
}

$stmt = $conn->prepare("
    SELECT file_path FROM supervisor_certificate WHERE superID = ?
");
$stmt->bind_param("i", $superID);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row || !file_exists($row['file_path'])) {
    echo json_encode(["exists" => false]);
    exit;
}

echo json_encode([
    "exists" => true,
    "file_name" => basename($row['file_path']),
    "file_size" => filesize($row['file_path'])
]);