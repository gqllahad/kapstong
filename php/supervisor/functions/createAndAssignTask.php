<?php
header('Content-Type: application/json');

session_start();


require_once("../../kapstongConnection.php");
require_once("../../functions.php");

$superID = $_POST['superID'];
$title = $_POST['title'];
$description = $_POST['description'];
$due_date = $_POST['due_date'];
$studentIDs = $_POST['studentIDs'] ?? [];

if (empty($superID) || empty($title) || empty($studentIDs)) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing required fields"
    ]);
    exit;
}

$successCount = 0;

foreach ($studentIDs as $studentID) {

    $stmt = $conn->prepare("
        INSERT INTO student_tasks (
            studentID,
            superID,
            title,
            description,
            status,
            due_date
        )
        VALUES (?, ?, ?, ?, 'NOT STARTED', ?)
    ");

    $stmt->bind_param(
        "sisss",
        $studentID,
        $superID,
        $title,
        $description,
        $due_date
    );

    if ($stmt->execute()) {
        $successCount++;
    }
}

echo json_encode([
    "status" => "success",
    "message" => "$successCount task(s) created successfully"
]);
