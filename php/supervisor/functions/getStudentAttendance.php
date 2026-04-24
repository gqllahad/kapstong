<?php

header('Content-Type: application/json');

require_once("../../kapstongConnection.php");

$studentID = $_GET['studentID'] ?? '';

if (empty($studentID)) {
    echo json_encode([
        "present" => 0,
        "late" => 0,
        "absent" => 0,
        "excused" => 0
    ]);
    exit;
}

$query = "SELECT 
            SUM(status='present') AS present,
            SUM(status='late') AS late,
            SUM(status='absent') AS absent,
            SUM(status='excused') AS excused
          FROM attendance_logs
          WHERE studentID = '$studentID'";

$result = $conn->query($query);

if (!$result) {
    echo json_encode([
        "present" => 0,
        "late" => 0,
        "absent" => 0,
        "excused" => 0,
        "error" => "Query failed"
    ]);
    exit;
}

$row = $result->fetch_assoc();

echo json_encode([
    "present" => (int)($row['present'] ?? 0),
    "late" => (int)($row['late'] ?? 0),
    "absent" => (int)($row['absent'] ?? 0),
    "excused" => (int)($row['excused'] ?? 0)
]);
