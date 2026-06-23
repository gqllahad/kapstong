<?php
session_start();

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgotPassword.php");
    exit();
}

$resetOtpError = $_SESSION['reset_otp_error'] ?? null;
unset($_SESSION['reset_otp_error']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="../../css/otpPhase.css">
</head>

<body class="reset-body">


    <div class="toast" id="toast"></div>
    <div class="hero-overlay"></div>

    <div class="otp-container">

        <h2>🔐 Reset Password OTP</h2>

        <p>Enter the code sent to your email</p>

        <form action="checkResetOTP.php" method="POST" id="verifyOTP">

            <input
                type="text"
                name="otp"
                maxlength="6"
                placeholder="••••••"
                required>

            <button type="submit">
                Verify OTP
            </button>

            <button type="button" id="resendOtpBtn">
                Resend OTP
            </button>

        </form>

        <div class="note">
            Didn't receive the email? Check spam/junk folder.
        </div>

        <a href="../loginPhase.php" class="back-login">
            ← Back to Login
        </a>

    </div>

    <div id="verifyLoadingScreen" class="loading-screen">
        <div class="logo-loader">
            <img src="../../kapstongImage/logo.jpg" class="logo-img-loading">
        </div>
        <p>Loading..</p>
    </div>
</body>
<script>
    const verify = document.getElementById("verifyOTP");
    const resendBtn = document.getElementById("resendOtpBtn");
    const verifyLoadingScreen = document.getElementById("verifyLoadingScreen");

    let cooldown = 0;

    const toast = document.getElementById("toast");

    function showToast(message, type = "success") {

        toast.textContent = message;

        toast.className = "toast";

        toast.classList.add("show", type);

        setTimeout(() => {
            toast.classList.remove("show", type);
        }, 3000);
    }
    document.addEventListener("DOMContentLoaded", () => {

        const otpError = "<?= $resetOtpError ?>";

        if (otpError === "invalid") {
            showToast("Invalid OTP. Please try again.", "error");
        }

        if (otpError === "expired") {
            showToast("OTP expired. Please request a new code.", "error");
        }

    });

    verify.addEventListener("submit", () => {
        verifyLoadingScreen.classList.add("show");
    });

    resendBtn.addEventListener("click", () => {

        if (cooldown > 0) return;

        resendBtn.disabled = true;
        resendBtn.innerText = "Sending...";

        fetch("resendResetOTP.php", {
                method: "POST"
            })
            .then(res => res.json())
            .then(data => {

                if (data.status === "success") {

                    showToast("OTP resent successfully", "success");
                    startCooldown();

                } else {

                    showToast(data.message);

                    resendBtn.disabled = false;
                    resendBtn.innerText = "Resend OTP";
                }

            })
            .catch(() => {

                showToast("Something went wrong", "error");

                resendBtn.disabled = false;
                resendBtn.innerText = "Resend OTP";
            });
    });

    function startCooldown() {

        cooldown = 60;

        const interval = setInterval(() => {

            resendBtn.innerText = `Resend OTP (${cooldown}s)`;
            cooldown--;

            if (cooldown < 0) {

                clearInterval(interval);

                resendBtn.disabled = false;
                resendBtn.innerText = "Resend OTP";
            }

        }, 1000);
    }
</script>

</html>