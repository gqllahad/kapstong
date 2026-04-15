<?php
require_once("../../kapstongConnection.php");
require_once("../../functions.php");

$studentID = $_POST['studentID'] ?? '';
$reason = $_POST['reason'] ?? '';
$note = $_POST['note'] ?? '';

if (empty($studentID) || empty($reason)) {
    exit("Invalid! Try Again.");
}

$documents = getStudentDocuments($conn, $studentID);

if ($documents) {

    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/student_uploads/$studentID/";

    if (!empty($documents['idUpload'])) {
        $idPath = $uploadDir . $documents['idUpload'];
        if (file_exists($idPath)) {
            unlink($idPath);
        }
    }

    if (!empty($documents['regFormUpload'])) {
        $regPath = $uploadDir . $documents['regFormUpload'];
        if (file_exists($regPath)) {
            unlink($regPath);
        }
    }

    $stmt = $conn->prepare("
        UPDATE student_documents 
        SET idUpload=NULL,
            regFormUpload=NULL,
            status='NOT UPLOADED',
            rejectReason=?,
            rejectNote=?
        WHERE studentID=?
    ");

    $stmt->bind_param("sss", $reason, $note, $studentID);
    $stmt->execute();
}

$stmt = $conn->prepare("
        UPDATE users 
        SET isVerified = 'NOT VERIFIED' 
        WHERE studentID = ?
    ");
$stmt->bind_param("s", $studentID);
$stmt->execute();
