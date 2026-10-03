<?php
session_start();
require_once("../../auth/admin_auth.php");
require_once("../../Shared/kapstongConnection.php");

header('Content-Type: application/json');

$stmt = $conn->prepare("
    SELECT file_path FROM certificate_template ORDER BY uploaded_at DESC LIMIT 1
");
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