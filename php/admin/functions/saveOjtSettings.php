<?php
header('Content-Type: application/json');
require_once("../../kapstongConnection.php");

$academic_year = $_POST['academic_year'];
$required_hours = $_POST['required_hours'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$status = $_POST['status'];

$check = $conn->query("SELECT settingID FROM ojt_settings WHERE status='ACTIVE' LIMIT 1");

if ($check->num_rows > 0 && $status == "ACTIVE") {

    $sql = "UPDATE ojt_settings 
            SET academic_year=?, required_hours=?, start_date=?, end_date=?, status=?
            WHERE status='ACTIVE'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sisss",
        $academic_year,
        $required_hours,
        $start_date,
        $end_date,
        $status
    );
} else {

    $sql = "INSERT INTO ojt_settings 
            (academic_year, required_hours, start_date, end_date, status)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sisss",
        $academic_year,
        $required_hours,
        $start_date,
        $end_date,
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
