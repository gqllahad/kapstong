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


// profile picture change
if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === 0) {
    $file = $_FILES['profileImage'];
    $fileType = mime_content_type($file['tmp_name']);

    if ($fileType !== 'image/jpeg') {
        header("Location: ../studentDashboard.php?error=Profile+must+be+jpg+or+jpeg!");
        exit();
    }

    // Optional: file size limit
    if ($file['size'] > 2 * 1024 * 1024) {
        header("Location: ../studentDashboard.php?error=File+is+too+large.+Max+2MB.");
        exit();
    }

    $newName = uniqid('profile_') . '.jpg';
    $uploadDir = __DIR__ . '/uploads/';
    $destination = $uploadDir . $newName;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // Save $newName or full path in your DB for this student
        // Example: $db->query("UPDATE students SET profile_pic='$newName' WHERE id='$studentId'");
        echo "Upload successful!";
    } else {
        echo "Upload failed. Try again.";
    }
}
