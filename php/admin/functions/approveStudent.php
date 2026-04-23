<?php
require_once("../../kapstongConnection.php");

$studentID = $_POST['studentID'] ?? '';

if (empty($studentID)) {
    exit("Invalid student");
}

if (!empty($studentID)) {

    // ojtstudnet
    $userStmt = $conn->prepare("
    UPDATE users 
    SET isVerified = 'VERIFIED' 
    WHERE studentID = ?
");
    $userStmt->bind_param("s", $studentID);
    $userStmt->execute();

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
                attendance_days,
                completion_status
            )
            VALUES (?, 500, 0, 0, 'ONGOING')
        ");
        $progressStmt->bind_param("s", $studentID);
        $progressStmt->execute();
    }
}
