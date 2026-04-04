<?php

session_start();
require_once("../../kapstongConnection.php");
require_once("../../functions.php");

if (!isset($_SESSION['studentID'])) {
    header("Location: ../../loginPhase.php?error=Unauthorized");
    exit();
}

$studentID = $_SESSION['studentID'];

if (isset($_POST['submitDocuments'])) {

    if (!isset($_FILES['idUpload']) || !isset($_FILES['regFormUpload'])) {
        header("Location: pendingStudentDashboard.php?error=Missing files");
        exit();
    }

    $idFile = $_FILES['idUpload'];
    $regFile = $_FILES['regFormUpload'];

    $allowedTypes = ['image/jpeg', 'image/png'];

    $uploadDir = "../../../uploads/student_uploads/$studentID/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $existing = getStudentDocuments($conn, $studentID);
    $idName = $existing['idUpload'] ?? null;
    $regName = $existing['regFormUpload'] ?? null;

    if (isset($_FILES['idUpload']) && $_FILES['idUpload']['error'] !== 4) {
        $idFile = $_FILES['idUpload'];
        if (!in_array($idFile['type'], $allowedTypes)) {
            header("Location: pendingStudentDashboard.php?error=ID+must+be+JPG+or+PNG");
            exit();
        }
        $idExt = pathinfo($idFile['name'], PATHINFO_EXTENSION);
        $idName = "student_id." . $idExt;
        move_uploaded_file($idFile['tmp_name'], $uploadDir . $idName);
    }

    if (isset($_FILES['regFormUpload']) && $_FILES['regFormUpload']['error'] !== 4) {
        $regFile = $_FILES['regFormUpload'];
        if (!in_array($regFile['type'], $allowedTypes)) {
            header("Location: pendingStudentDashboard.php?error=Registration+form+must+be+JPG+or+PNG");
            exit();
        }
        $regExt = pathinfo($regFile['name'], PATHINFO_EXTENSION);
        $regName = "registration_form." . $regExt;
        move_uploaded_file($regFile['tmp_name'], $uploadDir . $regName);
    }

    // if (
    //     !move_uploaded_file($idFile['tmp_name'], $uploadDir . $idName) ||
    //     !move_uploaded_file($regFile['tmp_name'], $uploadDir . $regName)
    // ) {

    //     header("Location: pendingStudentDashboard.php?error=Upload failed");
    //     exit();
    // }

    if ($existing) {
        $stmt = $conn->prepare("
            UPDATE student_documents 
            SET idUpload=?, regFormUpload=?, status='PENDING' 
            WHERE studentID=?
        ");
        $stmt->bind_param("sss", $idName, $regName, $studentID);
    } else {
        $stmt = $conn->prepare("
            INSERT INTO student_documents (studentID, idUpload, regFormUpload, status) 
            VALUES (?, ?, ?, 'PENDING')
        ");
        $stmt->bind_param("sss", $studentID, $idName, $regName);
    }

    $stmt->execute();

    $verifyStmt = $conn->prepare("UPDATE users SET isVerified = 'PENDING' WHERE studentID=?");
    $verifyStmt->bind_param("s", $studentID);
    $verifyStmt->execute();
}

header("Location: pendingStudentDashboard.php");
exit();
