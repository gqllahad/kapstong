<?php
require_once("../../kapstongConnection.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);


if (isset($_POST['changePasswordStudent'])) {

    $email = $_POST['email'] ?? '';

    $oldPassword = $_POST['oldPassword'] ?? '';

    $newPassword = $_POST['newPassword'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    $redirect = $_SERVER['HTTP_REFERER'] ?? '../../../index.php';

    if (empty($email) || empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: $redirect");
    }

    if ($newPassword !== $confirmPassword) {
        $_SESSION['error'] = "New password and confirmation do not match.";
        header("Location: $redirect");
        exit;
    }

    $sql_change_prep = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $sql_change_prep->bind_param("s", $email);
    $sql_change_prep->execute();

    $result = $sql_change_prep->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['error'] = "User not found.";
        header("Location: $redirect");
        exit;
    }

    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();

        if (!password_verify($oldPassword, $row['password'])) {
            header("Location: $redirect");
            exit;
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update_stmt->bind_param("ss", $hashedPassword, $email);

        if ($update_stmt->execute()) {
            echo "Password successfully changed!";
        } else {
            echo "Error updating password. Please try again.";
        }
    } else {
        die("User not found.");
    }

    header("Location: $redirect");
    exit;
};
