<?php
require_once("../kapstongConnection.php");

$invalidEmail = false;

if (isset($_GET['error'])) {
    $invalidEmail = true;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Forgot Password</title>

    <link
        rel="stylesheet"
        href="../../css/loginPhase.css">

</head>

<body class="reset-body">
    <div class="hero-overlay"></div>

    <?php if ($invalidEmail): ?>
        <div class="reset-modal-backdrop" id="accessDenied">
            <div class="reset-modal-box">
                <h2>Email Not Found</h2>
                <p>
                    The email address you entered is not registered in the system.
                    Please check your email and try again.
                </p>
                <button id="invalidEmail">
                    Back to Reset Password
                </button>
            </div>
        </div>
    <?php endif; ?>

    <section class="reset-log-container scroll-reveal">
        <div class="forgot-hero-overlay"></div>

        <div class="login-container">

            <div class="login-header">

                <section class="sign-logo">

                    <img
                        src="../../kapstongImage/logo.jpg"
                        alt="logo"
                        id="logoRedirect">

                </section>

                <h2>Reset Password</h2>

                <p>
                    Enter your registered email
                </p>

            </div>

            <form
                action="sendResetOTP.php"
                method="POST" id="submitEmail">

                <div class="reset-login-box">

                    <input
                        type="email"
                        name="resetEmail"
                        placeholder=" "
                        required>

                    <span>Email</span>

                </div>

                <div class="login-button">

                    <input
                        type="submit"
                        value="Send OTP"
                        name="send-reset"
                        class="login-submit">

                </div>

            </form>

        </div>

    </section>

    <div id="backLoadingScreen" class="loading-screen">
        <div class="logo-loader">
            <img src="../../kapstongImage/logo.jpg" class="logo-img-loading">
        </div>
        <p>Going back..</p>
    </div>

    <div id="submitLoadingScreen" class="loading-screen">
        <div class="logo-loader">
            <img src="../../kapstongImage/logo.jpg" class="logo-img-loading">
        </div>
        <p>Verifying..</p>
    </div>

</body>
<script src="../../js/resetPassword.js"></script>

</html>