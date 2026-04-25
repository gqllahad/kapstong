<?php

session_start();
header('Content-Type: application/json');

require_once("../../kapstongConnection.php");

if (!isset($_SESSION['studentID'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Unauthorized"
    ]);
    exit();
}

$studentID = $_SESSION['studentID'];

if (!isset($_POST['taskID'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing task ID"
    ]);
    exit();
}

$taskID = $_POST['taskID'];
$student_note = $_POST['student_note'] ?? '';

$allowedTypes = [
    'image/jpeg',
    'image/png',
    'application/pdf'
];

$uploadDir = "../../../uploads/student_tasks/$studentID/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$fileNames = [];

if (!empty($_FILES['submission_file']['name'])) {

    $names = $_FILES['submission_file']['name'];
    $tmpNames = $_FILES['submission_file']['tmp_name'];
    $types = $_FILES['submission_file']['type'];

    if (!is_array($names)) {
        $names = [$names];
        $tmpNames = [$tmpNames];
        $types = [$types];
    }

    foreach ($names as $key => $name) {

        $tmpName = $tmpNames[$key];
        $type = $types[$key];

        if (!in_array($type, $allowedTypes)) {
            continue;
        }

        $ext = pathinfo($name, PATHINFO_EXTENSION);

        $newFileName = "task_" . $taskID . "_" . time() . "_" . $key . "." . $ext;

        if (move_uploaded_file($tmpName, $uploadDir . $newFileName)) {
            $fileNames[] = $newFileName;
        }
    }
}

$fileName = implode(",", $fileNames);

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
