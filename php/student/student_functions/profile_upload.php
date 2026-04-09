<?php
session_start();
require_once("../../kapstongConnection.php");
require_once("../../functions.php");

if (!isset($_SESSION['studentID'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$redirect = $_SERVER['HTTP_REFERER'] ?? '../../../index.php';
$studentID = $_SESSION['studentID'];

if (!isset($_FILES['profilePicture']) || $_FILES['profilePicture']['error'] !== 0) {
    header("Location: $redirect");
    exit();
}

$file = $_FILES['profilePicture'];
$allowedTypes = ['image/jpeg', 'image/png'];

if (!in_array($file['type'], $allowedTypes)) {
    header("Location: $redirect");
    exit();
}

$uploadDir = "../../../uploads/student_uploads/$studentID/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$ext = getStudentDocuments($conn, $studentID);

if ($ext && !empty($ext['profilePicture'])) {
    $oldFile = "../../../" . $ext['profilePicture'];
    if (file_exists($oldFile)) unlink($oldFile);
}
$profileName = $ext['profilePicture'] ?? null;

if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] !== 4) {
    $profileFile = $_FILES['profilePicture'];
    if (!in_array($profileFile['type'], $allowedTypes)) {
        header("Location: pendingStudentDashboard.php?error=ID+must+be+JPG+or+PNG");
        exit();
    }
    $profileExt = pathinfo($profileFile['name'], PATHINFO_EXTENSION);
    $profileName = "profile." . $profileExt;
    move_uploaded_file($profileFile['tmp_name'], $uploadDir . $profileName);
}

if ($ext) {
    $stmt = $conn->prepare("UPDATE student_documents SET profilePicture=? WHERE studentID=?");
    $stmt->bind_param("ss", $profileName, $studentID);
} else {
    $stmt = $conn->prepare("INSERT INTO student_documents (studentID, profilePicture) VALUES (?, ?)");
    $stmt->bind_param("ss", $studentID, $profileName);
}

if ($stmt->execute()) {
    $_SESSION['upload_success'] = "Profile uploaded successfully!";
    header("Location: ../studentDashboard.php");
    exit();
} else {
    $_SESSION['upload_error'] = "Upload failed!";
    header("Location: ../studentDashboard.php");
    exit();
}
