<?php

session_start();
require_once("../Shared/kapstongConnection.php");

if(!isset($_SESSION['verified_reset'])) {
    header("Location: forgotPassword.php");
    exit();
}

if(isset($_POST['newPassword'], $_POST['confirmPassword'])) {

    if($_POST['newPassword'] !== $_POST['confirmPassword']) {

        $_SESSION['reset_password_error'] = "mismatch";

        header("Location: createNewPassword.php");
        exit();
    }

    $email = $_SESSION['reset_email'];

    $hashed = password_hash($_POST['newPassword'], PASSWORD_DEFAULT);

    $sql = $conn->prepare("
        UPDATE users
        SET password = ?
        WHERE email = ?
    ");

    $sql->bind_param("ss", $hashed, $email);

    $sql->execute();

    unset($_SESSION['verified_reset'], $_SESSION['reset_email'], $_SESSION['reset_otp'], $_SESSION['reset_expiry']);

    $_SESSION['auth_success'] = ['type' => 'password_reset', 'time' => time()];

    header("Location: ../Session/accountSuccess.php");
    exit();
}
?>