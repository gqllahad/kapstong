<?php
session_start();

if (isset($_POST['otp'])) {

    $enteredOTP = $_POST['otp'];

    if (time() > $_SESSION['reset_expiry']) {

        $_SESSION['reset_otp_error'] = "expired";

        header("Location: verifyResetOTP.php");
        exit();
    }

    if ($enteredOTP != $_SESSION['reset_otp']) {

        $_SESSION['reset_otp_error'] = "invalid";

        header("Location: verifyResetOTP.php");
        exit();
    }
    
    $_SESSION['verified_reset'] = true;

    header("Location: createNewPassword.php");
    exit();
}
?>