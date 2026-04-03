<?php

session_start();
require_once("../../kapstongConnection.php");
require_once("../../functions.php");

if (!isset($_SESSION['studentID'])) {
    exit("Unauthorized");
}

$studentID = $_SESSION['studentID'];

$documents = getStudentDocuments($conn, $studentID);

if ($documents) {

    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/student_uploads/$studentID/";

    $idPath = $uploadDir . $documents['idUpload'];
    $regPath = $uploadDir . $documents['regFormUpload'];

    if (file_exists($idPath)) {
        unlink($idPath);
    }

    if (file_exists($regPath)) {
        unlink($regPath);
    }

    $stmt = $conn->prepare("DELETE FROM student_documents WHERE studentID=?");
    $stmt->bind_param("s", $studentID);
    $stmt->execute();

    $verifyStmt = $conn->prepare("UPDATE users SET isVerified='NOT VERIFIED' WHERE studentID=?");
    $verifyStmt->bind_param("s", $studentID);
    $verifyStmt->execute();
}

echo "success";
// echo 'NOT VERIFIED';
