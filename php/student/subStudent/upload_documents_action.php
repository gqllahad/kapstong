<?php

session_start();
require_once("../../kapstongConnection.php");
require_once("../../functions.php");

// Security check
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

    if (!in_array($idFile['type'], $allowedTypes) || !in_array($regFile['type'], $allowedTypes)) {
        header("Location: pendingStudentDashboard.php?error=Only JPG and PNG allowed");
        exit();
    }

    $uploadDir = "../../../uploads/student_uploads/$studentID/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $idExt = pathinfo($idFile['name'], PATHINFO_EXTENSION);
    $regExt = pathinfo($regFile['name'], PATHINFO_EXTENSION);

    $idName = time() . "_id." . $idExt;
    $regName = time() . "_reg." . $regExt;

    if (
        !move_uploaded_file($idFile['tmp_name'], $uploadDir . $idName) ||
        !move_uploaded_file($regFile['tmp_name'], $uploadDir . $regName)
    ) {

        header("Location: pendingStudentDashboard.php?error=Upload failed");
        exit();
    }

    $existing = getStudentDocuments($conn, $studentID);

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

    $_SESSION['isVerified'] = 'PENDING';

    header("Location: pendingStudentDashboard.php?success=Documents submitted");
    exit();
}

header("Location: pendingStudentDashboard.php");
exit();
