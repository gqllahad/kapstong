<?php
session_start();
require_once("../../kapstongConnection.php");

if (!isset($_POST['editInfoStudent'])) {
    header("Location: pendingStudentDashboard.php");
    exit();
}

$studentID = $_SESSION['studentID'];
$role = $_SESSION['role'];

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

    $ip = getUserIP();

    $act_log = $conn->prepare("
        INSERT INTO activity_log 
        (userID, role, action, module, description, target_type, target_id, ip_address)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $action = "Edit Document Information";
    $module = "document";
    $description = "Student Edited Information";

    $target_type = "student";
    $target_id = $studentID;

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
} else {
    $_SESSION['error'] = "No changes detected.";
}

header("Location: pendingStudentDashboard.php");
exit();
