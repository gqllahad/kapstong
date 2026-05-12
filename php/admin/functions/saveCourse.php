<?php

header('Content-Type: application/json');
require_once("../../kapstongConnection.php");

$prg_name = $_POST['prg_name'] ?? null;
$prg_acro = $_POST['prg_acro'] ?? null;
$prg_department = $_POST['prg_department'] ?? null;
$prg_department_code = $_POST['prg_department_code'] ?? null;
$status = $_POST['prg_status'] ?? 'ACTIVE';

if (
    empty($prg_name) ||
    empty($prg_acro) ||
    empty($prg_department) ||
    empty($prg_department_code)
) {
    echo json_encode([
        "success" => false,
        "message" => "Please complete all fields!"
    ]);
    exit;
}

$check = $conn->prepare("
    SELECT program_id 
    FROM program 
    WHERE prg_name = ? OR prg_acro = ?
    LIMIT 1
");

$check->bind_param("ss", $prg_name, $prg_acro);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo json_encode([
        "success" => false,
        "message" => "Program already exists"
    ]);
    exit;
}

$sql = "INSERT INTO program 
        (prg_name, prg_acro, prg_department, prg_department_code, status)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "sssss",
    $prg_name,
    $prg_acro,
    $prg_department,
    $prg_department_code,
    $status
);

if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Course created successfully"
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Database insert failed"
    ]);
}

$stmt->close();
$conn->close();