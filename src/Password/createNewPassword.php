<?php
session_start();

if (!isset($_SESSION['verified_reset'])) {
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

    <link rel="stylesheet" href="../../public/css/otpPhase.css?v=124">
    <link rel="stylesheet" href="../../public/css/resetFlow.css?v=1">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body class="reset-body">
    <div id="toast" class="toast"></div>

    <section class="otp-wrap">

        <div class="otp-container login-container">

            <div class="otp-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
            </div>

            <div class="login-header">
                <h2>Create new password</h2>
                <p>Choose a strong password for your account.</p>
            </div>

            <form action="updateNewPassword.php" method="POST" id="resetPasswordForm">

                <div class="reset-login-box">
                    <input
                        type="password"
                        name="newPassword"
                        id="newPassword"
                        placeholder=" "
                        required>
                    <span>New password</span>
                    <i class="bx bx-show toggle-password" data-target="newPassword"></i>
                </div>

                <div class="reset-login-box">
                    <input
                        type="password"
                        name="confirmPassword"
                        id="confirmPassword"
                        placeholder=" "
                        required>
                    <span>Confirm password</span>
                    <i class="bx bx-show toggle-password" data-target="confirmPassword"></i>
                </div>

                <div class="password-strength" id="passwordStrength"></div>

                <p id="passwordError" class="password-error"></p>

                <div class="login-button">
                    <input
                        type="submit"
                        value="Update password"
                        class="login-submit">
                </div>

            </form>

        </div>

    </section>

    <div id="resetLoadingScreen" class="loading-screen">
        <div class="logo-loader">
            <img src="../../public/kapstongImage/logo.jpg" class="logo-img-loading">
        </div>
        <p>Loading..</p>
    </div>

</body>

<script src="../../public/js/resetPasswordValidation.js"></script>
<script>
   
    const resetPasswordError = <?= json_encode($resetPasswordError) ?>;
    const resetErrorMessages = {
        mismatch: "Passwords do not match."
    };
    if (resetPasswordError) {
        const t = document.getElementById("toast");
        t.textContent = resetErrorMessages[resetPasswordError] || resetPasswordError;
        t.className = "toast error show";
        setTimeout(() => t.classList.remove("show"), 3000);
    }
</script>

</html>