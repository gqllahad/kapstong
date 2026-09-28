<?php
session_start();
require_once("../../Shared/kapstongConnection.php");
require_once("../../auth/admin_auth.php");
require_once("../../Shared/functions.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit("Invalid request");
}

$studentID = trim($_POST['studentID'] ?? '');
$newUID    = trim($_POST['rfid_uid'] ?? '');
$adminID   = $_SESSION['user_id'] ?? null;

if ($studentID === '' || $newUID === '' || !$adminID) {
    exit("Missing data");
}

$check = $conn->prepare("SELECT studentID FROM users WHERE rfid_uid = ?");
$check->bind_param("s", $newUID);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    exit("RFID already registered");
}

$conn->begin_transaction();

try {
    $old = $conn->prepare("SELECT rfid_uid FROM users WHERE studentID = ?");
    $old->bind_param("s", $studentID);
    $old->execute();
    $oldRow = $old->get_result()->fetch_assoc();

    if (!$oldRow) {
        throw new Exception("Student not found");
    }
    $oldUID = $oldRow['rfid_uid'];

    $upd = $conn->prepare("
        UPDATE users
        SET rfid_uid = ?, rfid_status = 'Registered'
        WHERE studentID = ?
    ");
    $upd->bind_param("ss", $newUID, $studentID);
    $upd->execute();

    $log = $conn->prepare("
        INSERT INTO rfid_card_history (studentID, old_rfid_uid, new_rfid_uid, changed_by, reason)
        VALUES (?, ?, ?, ?, 'Lost card replacement')
    ");
    $log->bind_param("sssi", $studentID, $oldUID, $newUID, $adminID);
    $log->execute();

    $conn->commit();
    echo "success";

} catch (Exception $e) {
    $conn->rollback();
    echo "Failed to re-register RFID";
}