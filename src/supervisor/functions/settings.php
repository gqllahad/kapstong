<?php
session_start();
require_once("../../kapstongConnection.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'supervisor') {
    header("Location: ../../loginPhase.php");
    exit();
}

if (isset($_POST['changePasswordSupervisor'])) {

    $userID = $_SESSION['user_id'];

    $oldPassword = $_POST['oldPassword'] ?? '';
    $newPassword = $_POST['newPassword'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    $redirect = $_SERVER['HTTP_REFERER'] ?? '../../index.php';

    if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: $redirect");
        exit();
    }

    if ($newPassword !== $confirmPassword) {
        $_SESSION['error'] = "New password and confirmation do not match.";
        header("Location: $redirect");
        exit();
    }

    $stmt = $conn->prepare("SELECT password FROM users WHERE userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['error'] = "User not found.";
        header("Location: $redirect");
        exit();
    }

    $row = $result->fetch_assoc();

    if (!password_verify($oldPassword, $row['password'])) {
        $_SESSION['error'] = "Old password is incorrect.";
        header("Location: $redirect");
        exit();
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $update = $conn->prepare("UPDATE users SET password = ? WHERE userID = ?");
    $update->bind_param("si", $hashedPassword, $userID);

    if ($update->execute()) {
        $_SESSION['success'] = "Password successfully changed!";
    } else {
        $_SESSION['error'] = "Failed to update password.";
    }

    header("Location: $redirect");
    exit();
}

// force
if (isset($_POST['forceChangePasswordSupervisor'])) {

    $userID = $_POST['userID'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($newPassword !== $confirmPassword) {
        die("Password mismatch");
    }

    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        UPDATE users 
        SET password = ?, mustChangePassword = 0 
        WHERE userID = ?
    ");

    $stmt->bind_param("si", $hashed, $userID);
    $stmt->execute();

    header("Location: ../supervisorDashboard.php");
    exit();
}