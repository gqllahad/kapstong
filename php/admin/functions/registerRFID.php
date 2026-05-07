<?php
require_once("../../kapstongConnection.php");

$rfid = trim($_POST['rfid'] ?? '');
$studentID = trim($_POST['studentID'] ?? '');

if (empty($rfid) || empty($studentID)) {
    echo json_encode([
        'success' => false,
        'message' => '❌ RFID and Student ID are required'
    ]);
    exit();
}

$checkStmt = $conn->prepare("SELECT id FROM user WHERE studentID = ?");
$checkStmt->bind_param("s", $studentID);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => '❌ Student ID not found'
    ]);
    exit();
}

$user = $checkResult->fetch_assoc();
$userID = $user['id'];

$rfidCheck = $conn->prepare("SELECT id FROM user WHERE rfid = ? AND id != ?");
$rfidCheck->bind_param("si", $rfid, $userID);
$rfidCheck->execute();
$rfidResult = $rfidCheck->get_result();

if ($rfidResult->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'message' => '⚠️ RFID already registered to another student'
    ]);
    exit();
}

$updateStmt = $conn->prepare("UPDATE user SET rfid = ? WHERE id = ?");
$updateStmt->bind_param("si", $rfid, $userID);

if ($updateStmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => '✅ RFID Registered Successfully!'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => '❌ Registration failed: ' . $conn->error
    ]);
}

$checkStmt->close();
$rfidCheck->close();
$updateStmt->close();
$conn->close();
?>
