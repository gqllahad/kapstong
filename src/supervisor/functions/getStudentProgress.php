<?php

header('Content-Type: application/json');
require_once("../../auth/supervisor_auth.php");
require_once("../../kapstongConnection.php");

$studentID = $_GET['studentID'] ?? '';

if (empty($studentID)) {
    echo json_encode([
        "completed" => 0,
        "remaining" => 0
    ]);
    exit;
}

$query = "SELECT completed_hours, remaining_hours
          FROM student_progress
          WHERE studentID = '$studentID'";

$result = $conn->query($query);

if (!$result) {
    echo json_encode([
        "completed" => 0,
        "remaining" => 0,
        "error" => "Query failed"
    ]);
    exit;
}

$row = $result->fetch_assoc();

if (!$row) {
    echo json_encode([
        "completed" => 0,
        "remaining" => 0
    ]);
    exit;
}

echo json_encode([
    "completed" => (int)$row['completed_hours'],
    "remaining" => (int)$row['remaining_hours']
]);
