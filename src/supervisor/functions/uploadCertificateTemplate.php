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

$oldStmt = $conn->prepare("SELECT file_path FROM supervisor_certificate WHERE superID = ?");
$oldStmt->bind_param("i", $superID);
$oldStmt->execute();
$oldRow = $oldStmt->get_result()->fetch_assoc();
if ($oldRow && file_exists($oldRow['file_path'])) {
    unlink($oldRow['file_path']);
}

if (!isset($_FILES['certificate']) || $_FILES['certificate']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["success" => false, "message" => "No file received."]);
    exit;
}

$file = $_FILES['certificate'];
$allowedTypes = ['application/pdf', 'image/png', 'image/jpeg'];

if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(["success" => false, "message" => "Unsupported file type."]);
    exit;
}

if ($file['size'] > 10 * 1024 * 1024) {
    echo json_encode(["success" => false, "message" => "File exceeds 10MB limit."]);
    exit;
}

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = "certificate_template_{$superID}." . $ext;
$destDir = "../../../uploads/certificate_templates/";

if (!is_dir($destDir)) {
    mkdir($destDir, 0755, true);
}

$destPath = $destDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    echo json_encode(["success" => false, "message" => "Failed to save file."]);
    exit;
}

$stmt = $conn->prepare("
    INSERT INTO supervisor_certificate (superID, file_path, uploaded_at)
    VALUES (?, ?, NOW())
    ON DUPLICATE KEY UPDATE file_path = VALUES(file_path), uploaded_at = NOW()
");
$stmt->bind_param("is", $superID, $destPath);
$stmt->execute();

echo json_encode(["success" => true]);


