<?php
header('Content-Type: application/json');
require_once("../../kapstongConnection.php");

$academic_year = $_POST['academic_year'] ?? null;
$required_hours = $_POST['required_hours'] ?? null;
$status = $_POST['status'] ?? null;

$check = $conn->query("SELECT settingID FROM ojt_settings WHERE status='ACTIVE' LIMIT 1");

if ($check->num_rows > 0 && $status == "ACTIVE") {

    $sql = "UPDATE ojt_settings 
            SET academic_year=?, required_hours=?, status=?
            WHERE status='ACTIVE'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sis",
        $academic_year,
        $required_hours,
        $status
    );
} else {

    $sql = "INSERT INTO ojt_settings 
            (academic_year, required_hours, status)
            VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sis",
        $academic_year,
        $required_hours,
        $status
    );
}

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "OJT settings saved successfully"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to save settings"
    ]);
}

$stmt->close();
$conn->close();
