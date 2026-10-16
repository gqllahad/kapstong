<?php
header('Content-Type: application/json');

enforceSessionTimeout('student');

$studentID = $_SESSION['studentID'] ?? '';   
if ($studentID === '') {
    echo json_encode(["status" => "error", "message" => "No student"]);
    exit;
}

if (($_POST['all'] ?? '') === '1') {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE userID = ? AND is_read = 0");
    $stmt->bind_param("s", $studentID);
} else {
    $id = (int) ($_POST['id'] ?? 0);
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE notificationID = ? AND userID = ?");
    $stmt->bind_param("is", $id, $studentID);
}
$stmt->execute();

echo json_encode(["status" => "ok"]);