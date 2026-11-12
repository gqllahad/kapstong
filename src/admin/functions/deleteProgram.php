<?php
session_start();
require_once("../../auth/admin_auth.php");
require_once("../../Shared/kapstongConnection.php");

header('Content-Type: application/json');

$programID = (int)($_POST['program_id'] ?? 0);

if ($programID <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid program ID"]);
    exit;
}

$stmt = $conn->prepare("SELECT prg_acro FROM program WHERE program_id = ?");
$stmt->bind_param("i", $programID);
$stmt->execute();
$program = $stmt->get_result()->fetch_assoc();

if (!$program) {
    echo json_encode(["status" => "error", "message" => "Program not found"]);
    exit;
}

$chk = $conn->prepare("SELECT COUNT(*) AS total FROM ojtstudent WHERE course = ?");
$chk->bind_param("s", $program['prg_acro']);
$chk->execute();
$inUse = (int)$chk->get_result()->fetch_assoc()['total'];

if ($inUse > 0) {
    echo json_encode([
        "status"  => "error",
        "message" => "Can't delete: $inUse student(s) are still under this program. Set it to INACTIVE instead."
    ]);
    exit;
}

$del = $conn->prepare("DELETE FROM program WHERE program_id = ?");
$del->bind_param("i", $programID);
$del->execute();

if ($del->affected_rows > 0) {
    echo json_encode(["status" => "ok"]);
} else {
    echo json_encode(["status" => "error", "message" => "Delete failed"]);
}