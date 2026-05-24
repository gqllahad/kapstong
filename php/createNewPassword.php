<?php
session_start();

if(!isset($_SESSION['verified_reset'])) {
    header("Location: forgotPassword.php");
    exit();
}


$resetPasswordError = $_SESSION['reset_password_error'] ?? null;
unset($_SESSION['reset_password_error']);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create New Password</title>

    <link rel="stylesheet" href="../css/loginPhase.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

</head>

<body class="reset-body">
    <div id="toast" class="toast"></div>

<section class="reset-log-container">

    <div class="login-container">

        <div class="login-header">

            <h2>🔐 Create New Password</h2>

            <p>Choose a strong password for your account</p>

        </div>

        <form action="updateNewPassword.php" method="POST" id="resetPasswordForm">

            <div class="reset-login-box">

                <input
                    type="password"
                    name="newPassword"
                    id="newPassword"
                    placeholder=" "
                    required
                >

                <span>New Password</span>

                <i class="bx bx-show toggle-password" data-target="newPassword"></i>

            </div>

            <div class="reset-login-box">

                <input
                    type="password"
                    name="confirmPassword"
                    id="confirmPassword"
                    placeholder=" "
                    required
                >

                <span>Confirm Password</span>

                 <i class="bx bx-show toggle-password" data-target="confirmPassword"></i>

            </div>

            <div class="password-strength" id="passwordStrength"></div>

            <p id="passwordError" class="password-error"></p>

            <div class="login-button">

                <input
                    type="submit"
                    value="Update Password"
                    class="login-submit"
                >

            </div>

        </form>

    </div>

</section>

    <div id="resetLoadingScreen" class="loading-screen">
            <div class="logo-loader">
            <img src="../kapstongImage/logo.jpg" class="logo-img-loading">
            </div>
            <p>Loading..</p>
        </div>

</body>

<script src="../js/resetPasswordValidation.js"></script>

</html>