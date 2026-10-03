<?php
session_start();
require_once("../../auth/admin_auth.php");
require_once("../../Shared/kapstongConnection.php");

header('Content-Type: application/json');

$stmt = $conn->prepare("
    SELECT id, file_path FROM certificate_template ORDER BY uploaded_at DESC LIMIT 1
");
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if ($row && file_exists($row['file_path'])) {
    unlink($row['file_path']);
}

if ($row) {
    $delStmt = $conn->prepare("DELETE FROM certificate_template WHERE id = ?");
    $delStmt->bind_param("i", $row['id']);
    $delStmt->execute();
}

echo json_encode(["success" => true]);