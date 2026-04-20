<?php

session_start();
require_once("../../kapstongConnection.php");
require_once("../../functions.php");

if (!isset($_SESSION['studentID'])) {
    exit("Unauthorized");
}

$studentID = $_SESSION['studentID'];
$role = $_SESSION['role'];

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

    $stmt = $conn->prepare("UPDATE student_documents SET idUpload = NULL, regFormUpload = NULL, status = 'NOT UPLOADED' WHERE studentID=?");
    $stmt->bind_param("s", $studentID);

    if ($stmt->execute()) {
        $_SESSION['delete_success'] = "Upload Deleted successfully!";
    } else {
        $_SESSION['delete_error'] = "Deletion failed!";
    }

    $verifyStmt = $conn->prepare("UPDATE users SET isVerified='NOT VERIFIED' WHERE studentID=?");
    $verifyStmt->bind_param("s", $studentID);
    $verifyStmt->execute();

    $_SESSION['isVerified'] = 'NOT VERIFIED';

    $ip = getUserIP();

    $act_log = $conn->prepare("
        INSERT INTO activity_log 
        (userID, role, action, module, description, target_type, target_id, ip_address)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $action = "Remove Document";
    $module = "document";
    $description = "Student Remove Uploaded Document";

    $target_type = "student";
    $target_id = $row['studentID'];

    $act_log->bind_param(
        "isssssss",
        $userID,
        $role,
        $action,
        $module,
        $description,
        $target_type,
        $target_id,
        $ip
    );

    $act_log->execute();
}
// echo 'NOT VERIFIED';
