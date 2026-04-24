<?php

session_start();
header('Content-Type: application/json');

require_once("../../kapstongConnection.php");

if (!isset($_SESSION['studentID'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$studentID = $_SESSION['studentID'];

if (!isset($_POST['taskID'])) {
    echo json_encode(["status" => "error", "message" => "Missing task ID"]);
    exit();
}

$taskID = $_POST['taskID'];
$student_note = $_POST['student_note'] ?? '';


$allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];

$uploadDir = "../../../uploads/student_tasks/$studentID/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$fileName = null;

if (isset($_FILES['submission_file']) && $_FILES['submission_file']['error'] !== 4) {

    $file = $_FILES['submission_file'];

    if (!in_array($file['type'], $allowedTypes)) {
        echo json_encode(["status" => "error", "message" => "Invalid file type"]);
        exit();
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

    $fileName = "task_" . $taskID . "_" . time() . "." . $ext;

    move_uploaded_file($file['tmp_name'], $uploadDir . $fileName);
}

$stmt = $conn->prepare("
    UPDATE student_tasks
    SET 
        status = 'SUBMITTED',
        student_note = ?,
        submission_file = ?,
        date_updated = NOW()
    WHERE taskID = ? AND studentID = ?
");

$stmt->bind_param("ssis", $student_note, $fileName, $taskID, $studentID);

if ($stmt->execute()) {

    echo json_encode([
        "status" => "success",
        "message" => "Task submitted successfully"
    ]);
} else {

    echo json_encode([
        "status" => "error",
        "message" => "Database update failed"
    ]);
}
