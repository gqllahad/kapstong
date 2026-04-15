<?php
require_once("../../kapstongConnection.php");

$studentID = $_POST['studentID'] ?? '';

if (empty($studentID)) {
    exit("Invalid student");
}

if (!empty($studentID)) {

    $userStmt = $conn->prepare("
    UPDATE users 
    SET isVerified = 'VERIFIED' 
    WHERE studentID = ?
");
    $userStmt->bind_param("s", $studentID);
    $userStmt->execute();

    $docStmt = $conn->prepare("
    UPDATE student_documents 
    SET status = 'APPROVED' 
    WHERE studentID = ?
");
    $docStmt->bind_param("s", $studentID);
    $docStmt->execute();
}
