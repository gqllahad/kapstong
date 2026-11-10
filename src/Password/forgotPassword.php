<?php
require_once("../Shared/kapstongConnection.php");

$invalidEmail = false;

if (isset($_GET['error'])) {
    $invalidEmail = true;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>

    <link rel="stylesheet" href="../../public/css/otpPhase.css?v=124">
    <link rel="stylesheet" href="../../public/css/resetFlow.css?v=1">
</head>

<body class="reset-body">

    <?php if ($invalidEmail): ?>
        <div class="reset-modal-backdrop" id="accessDenied">
            <div class="reset-modal-box">
                <div class="otp-icon danger">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <path d="m15 9-6 6" />
                        <path d="m9 9 6 6" />
                    </svg>
                </div>
                <h2>Email not found</h2>
                <p>
                    That email address isn't registered in the system.
                    Check it and try again.
                </p>
                <button id="invalidEmail">
                    Back to reset password
                </button>
            </div>
        </div>
    <?php endif; ?>

    <section class="otp-wrap scroll-reveal">

        <div class="otp-container login-container">

            <div class="login-header">

                <section class="sign-logo otp-icon">
                    <img
                        src="../../public/kapstongImage/logo.jpg"
                        alt="logo"
                        id="logoRedirect"
                        class="badge-logo">
                </section>

                <h2>Reset password</h2>

                <p>Enter your registered email and we'll send you a 6-digit code.</p>

            </div>

            <form
                action="../Otp/sendResetOTP.php"
                method="POST"
                id="submitEmail">

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

        <a href="../Session/loginPhase.php" class="back-login">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m12 19-7-7 7-7" />
                <path d="M19 12H5" />
            </svg>
            Back to log in
        </a>

    </section>

    <div id="backLoadingScreen" class="loading-screen">
        <div class="logo-loader">
            <img src="../../public/kapstongImage/logo.jpg" class="logo-img-loading">
        </div>
        <p>Going back..</p>
    </div>

    <div id="submitLoadingScreen" class="loading-screen">
        <div class="logo-loader">
            <img src="../../public/kapstongImage/logo.jpg" class="logo-img-loading">
        </div>
        <p>Verifying..</p>
    </div>

</body>
<script src="../../public/js/resetPassword.js"></script>

</html>