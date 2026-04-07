<?php
session_start();
require_once("../../kapstongConnection.php");

if (!isset($_POST['editInfoStudent'])) {
    header("Location: pendingStudentDashboard.php");
    exit();
}

$studentID = $_SESSION['studentID'];

$fields = [];
$params = [];
$types = "";

// name
if (isset($_POST['fullName']) && $_POST['fullName'] !== "") {
    $fields[] = "name = ?";
    $params[] = trim($_POST['fullName']);
    $types .= "s";
}

// mobile
if (isset($_POST['mobileNumber']) && $_POST['mobileNumber'] !== "") {
    $fields[] = "mobileNumber = ?";
    $params[] = trim($_POST['mobileNumber']);
    $types .= "s";
}

// birthday
if (isset($_POST['birthDate']) && $_POST['birthDate'] !== "") {
    $fields[] = "birthDate = ?";
    $params[] = $_POST['birthDate'];
    $types .= "s";
}

// program
if (isset($_POST['course']) && $_POST['course'] !== "") {
    $fields[] = "course = ?";
    $params[] = trim($_POST['course']);
    $types .= "s";
}

// year level
if (isset($_POST['yearLevel']) && $_POST['yearLevel'] !== "") {
    $fields[] = "yearLevel = ?";
    $params[] = $_POST['yearLevel'];
    $types .= "s";
}

// sem
if (isset($_POST['semester']) && $_POST['semester'] !== "") {
    $fields[] = "semester = ?";
    $params[] = $_POST['semester'];
    $types .= "s";
}
// gender
if (isset($_POST['gender']) && $_POST['gender'] !== "") {
    $fields[] = "gender = ?";
    $params[] = $_POST['gender'];
    $types .= "s";
}

// address
if (isset($_POST['address']) && $_POST['address'] !== "") {
    $fields[] = "address = ?";
    $params[] = trim($_POST['address']);
    $types .= "s";
}

if (!empty($fields)) {

    $sql = "UPDATE ojtstudent SET " . implode(", ", $fields) . " WHERE studentID = ?";
    $params[] = $studentID;
    $types .= "s";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $_SESSION['profile_success'] = "Profile updated successfully!";
    } else {
        $_SESSION['profile_error'] = "Update failed!";
    }

    $stmt->close();
} else {
    $_SESSION['error'] = "No changes detected.";
}

header("Location: pendingStudentDashboard.php");
exit();
