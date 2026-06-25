<?php

header('Content-Type: application/json');
require_once("../../Shared/kapstongConnection.php");
require_once("../../auth/admin_auth.php");

$sql = "SELECT academic_year, required_hours, status
        FROM ojt_settings
        WHERE status = 'ACTIVE'
        ORDER BY settingID DESC
        LIMIT 1";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {

    $row = $result->fetch_assoc();

    echo json_encode([
        "success" => true,
        "academic_year" => $row['academic_year'],
        "required_hours" => $row['required_hours'],
        "status" => $row['status']
    ]);

} else {

    echo json_encode([
        "success" => false,
        "academic_year" => "",
        "required_hours" => "",
        "status" => "ACTIVE"
    ]);
}

$conn->close();