<?php
require_once("../../kapstongConnection.php");

$studentID = $_POST['studentID'] ?? null;
$rfid = $_POST['rfid'] ?? null;
$no_rfid = $_POST['no_rfid'] ?? null;
$message = '';


if (!$studentID) {
    exit("Invalid student");
}

if (!empty($rfid)) {

    $check = $conn->prepare("SELECT studentID FROM users WHERE rfid_uid = ?");
    $check->bind_param("s", $rfid);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        exit("RFID already registered to another student!");
    }

    $userStmt = $conn->prepare("
        UPDATE users 
        SET isVerified = 'VERIFIED',
            rfid_uid = ?,
            rfid_status = 'Registered'
        WHERE studentID = ?
    ");
    $userStmt->bind_param("ss", $rfid, $studentID);
    $userStmt->execute();

    $message = "Approved with RFID!";
}

elseif (!empty($no_rfid)) {

    $userStmt = $conn->prepare("
        UPDATE users 
        SET isVerified = 'VERIFIED',
            rfid_status = 'Not Registered'
        WHERE studentID = ?
    ");
    $userStmt->bind_param("s", $studentID);
    $userStmt->execute();

    $message = "Approved without RFID!";
}

else {
    exit("No approval action specified.");
}


 // student_documents
    $docStmt = $conn->prepare("
    UPDATE student_documents 
    SET status = 'APPROVED' 
    WHERE studentID = ?
");
    $docStmt->bind_param("s", $studentID);
    $docStmt->execute();

    // student_progress
    $checkStmt = $conn->prepare("
        SELECT progressID FROM student_progress WHERE studentID = ?
    ");
    $checkStmt->bind_param("s", $studentID);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows == 0) {

        $progressStmt = $conn->prepare("
            INSERT INTO student_progress (
                studentID,
                required_hours,
                completed_hours,
                completion_status
            )
            VALUES (?, 500, 0, 'ONGOING')
        ");
        $progressStmt->bind_param("s", $studentID);
        $progressStmt->execute();
    }

    echo $message;

// if (!empty($studentID)) {
//     $userStmt = $conn->prepare("
//     UPDATE users 
//     SET isVerified = 'VERIFIED' 
//     WHERE studentID = ?
// ");
//     $userStmt->bind_param("s", $studentID);
//     $userStmt->execute();

   
// }
