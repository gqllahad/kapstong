<?php
header('Content-Type: application/json');
require_once("../../kapstongConnection.php");

$program_id = $_POST['program_id'] ?? '';
$prg_name = $_POST['prg_name'] ?? '';
$prg_acro = $_POST['prg_acro'] ?? '';
$prg_department = $_POST['prg_department'] ?? '';
$prg_department_code = $_POST['prg_department_code'] ?? '';
$status = $_POST['status'] ?? '';

if (
    empty($program_id) ||
    empty($prg_name) ||
    empty($prg_acro) ||
    empty($prg_department) ||
    empty($prg_department_code) ||
    empty($status)
) {
    echo json_encode([
        "success" => false,
        "message" => "Please complete all fields."
    ]);
    exit;
}

$sql = "UPDATE program 
        SET 
            prg_name = ?, 
            prg_acro = ?, 
            prg_department = ?, 
            prg_department_code = ?,
            status = ?
        WHERE program_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "sssssi",
    $prg_name,
    $prg_acro,
    $prg_department,
    $prg_department_code,
    $status,
    $program_id
);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Program updated successfully."
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to update program."
    ]);
}

$stmt->close();
$conn->close();
