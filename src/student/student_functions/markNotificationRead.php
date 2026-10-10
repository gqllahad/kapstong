<?php
session_start();
require_once("../../Shared/kapstongConnection.php");
require_once("../../auth/student_auth.php");

header('Content-Type: application/json');

$studentID = $_SESSION['studentID'] ?? null;

if (!$studentID || ($_SESSION['role'] ?? '') !== 'student') {
    echo json_encode(["success" => false]);
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

if ($id > 0) {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE notificationID = ? AND userID = ?");
    $stmt->bind_param("is", $id, $studentID);
} else {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE userID = ?");
    $stmt->bind_param("s", $studentID);
}

$stmt->execute();
echo json_encode(["success" => true]);