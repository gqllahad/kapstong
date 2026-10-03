<?php
session_start();
require_once("../../auth/admin_auth.php");
require_once("../../Shared/kapstongConnection.php");

header('Content-Type: application/json');

function respond($success, $message) {
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

$userID = $_SESSION['user_id'] ?? null;

if (!$userID) {
    respond(false, 'Not authenticated.');
}

if (!isset($_FILES['certificate']) || $_FILES['certificate']['error'] !== UPLOAD_ERR_OK) {
    respond(false, 'No file uploaded or upload error.');
}

$file = $_FILES['certificate'];
$allowedTypes = ['application/pdf', 'image/png', 'image/jpeg'];

if (!in_array($file['type'], $allowedTypes)) {
    respond(false, 'Unsupported file type.');
}

if ($file['size'] > 10 * 1024 * 1024) {
    respond(false, 'File exceeds 10MB limit.');
}

$uploadDir = "../../uploads/certificate_templates/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$safeFileName = "certificate_template_" . time() . "." . $extension;
$destination = $uploadDir . $safeFileName;

$existing = $conn->prepare("SELECT id, file_path FROM certificate_template ORDER BY uploaded_at DESC LIMIT 1");
$existing->execute();
$existingRow = $existing->get_result()->fetch_assoc();

if ($existingRow && file_exists($existingRow['file_path'])) {
    unlink($existingRow['file_path']);
}
if ($existingRow) {
    $delOld = $conn->prepare("DELETE FROM certificate_template WHERE id = ?");
    $delOld->bind_param("i", $existingRow['id']);
    $delOld->execute();
}

if (!move_uploaded_file($file['tmp_name'], $destination)) {
    respond(false, 'Failed to save uploaded file.');
}

$stmt = $conn->prepare("
    INSERT INTO certificate_template (file_path, uploaded_by, uploaded_at)
    VALUES (?, ?, NOW())
");
$stmt->bind_param("si", $destination, $userID);
$stmt->execute();

respond(true, 'Certificate template saved.');