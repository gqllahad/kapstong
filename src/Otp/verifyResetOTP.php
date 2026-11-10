<?php
session_start();

if (!isset($_SESSION['reset_email'])) {
    header("Location: ../Password/forgotPassword.php");
    exit();
}

$resetOtpError = $_SESSION['reset_otp_error'] ?? null;
unset($_SESSION['reset_otp_error']);

$resetEmail = htmlspecialchars($_SESSION['reset_email'], ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Verify OTP</title>
    <link rel="stylesheet" href="../../public/css/otpPhase.css?v=124">
</head>

<body class="reset-body">

    <div class="toast" id="toast"></div>

    <div class="otp-wrap">
        <div class="otp-container">

            <div class="otp-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />
                    <path d="m9 12 2 2 4-4" />
                </svg>
            </div>

            <h2>Enter verification code</h2>
            <p>We sent a 6-digit reset code to</p>
            <span class="otp-email"><?= $resetEmail ?></span>

            <form action="checkResetOTP.php" method="POST" id="verifyOTP">
                <label class="otp-label" for="otp-1">Security code</label>

                <div class="otp-boxes" id="otpBoxes">
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                        <input
                            type="text"
                            class="otp-box"
                            id="otp-<?= $i ?>"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            maxlength="1"
                            autocomplete="<?= $i === 1 ? 'one-time-code' : 'off' ?>"
                            aria-label="Digit <?= $i ?>">
                    <?php endfor; ?>
                </div>

                <input type="hidden" name="otp" id="otpValue">

                <button type="submit">Verify &amp; Continue</button>
            </form>

            <div class="otp-footer">
                <div class="resend-row">
                    Didn't receive the email?
                    <button type="button" id="resendOtpBtn">Resend code</button>
                    <span id="resendTimer"></span>
                </div>

                <div class="note">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 16v-4" />
                        <path d="M12 8h.01" />
                    </svg>
                    Be sure to check your spam or junk folder.
                </div>
            </div>
        </div>

        <a href="../Session/loginPhase.php" class="back-login">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m12 19-7-7 7-7" />
                <path d="M19 12H5" />
            </svg>
            Back to log in
        </a>
    </div>

    <div id="verifyLoadingScreen" class="loading-screen">
        <div class="logo-loader">
            <img src="../../public/kapstongImage/logo.jpg" class="logo-img-loading">
        </div>
        <p>Loading..</p>
    </div>

    <script>
        const verify = document.getElementById("verifyOTP");
        const verifyLoadingScreen = document.getElementById("verifyLoadingScreen");
        const boxes = Array.from(document.querySelectorAll(".otp-box"));
        const hidden = document.getElementById("otpValue");
        const boxWrap = document.getElementById("otpBoxes");
        const resendBtn = document.getElementById("resendOtpBtn");
        const timerEl = document.getElementById("resendTimer");
        const toast = document.getElementById("toast");

        function showToast(message, type = "success") {
            toast.textContent = message;
            toast.className = "toast";
            toast.classList.add("show", type);
            setTimeout(() => toast.classList.remove("show", type), 3000);
        }

        const otpError = "<?= $resetOtpError ?>";
        if (otpError === "invalid") showToast("Invalid code. Please try again.", "error");
        if (otpError === "expired") showToast("Code expired. Request a new one.", "error");

        boxes[0].focus();

        boxes.forEach((box, i) => {
            box.addEventListener("input", () => {
                box.value = box.value.replace(/\D/g, "").slice(-1);
                box.classList.toggle("filled", box.value !== "");
                if (box.value && i < boxes.length - 1) boxes[i + 1].focus();
            });

            box.addEventListener("keydown", (e) => {
                if (e.key === "Backspace" && !box.value && i > 0) {
                    boxes[i - 1].value = "";
                    boxes[i - 1].classList.remove("filled");
                    boxes[i - 1].focus();
                } else if (e.key === "ArrowLeft" && i > 0) {
                    boxes[i - 1].focus();
                } else if (e.key === "ArrowRight" && i < boxes.length - 1) {
                    boxes[i + 1].focus();
                }
            });

            box.addEventListener("focus", () => box.select());

            box.addEventListener("paste", (e) => {
                e.preventDefault();
                const digits = (e.clipboardData.getData("text") || "").replace(/\D/g, "").slice(0, 6);
                digits.split("").forEach((d, idx) => {
                    boxes[idx].value = d;
                    boxes[idx].classList.add("filled");
                });
                boxes[Math.min(digits.length, 5)].focus();
            });
        });

        verify.addEventListener("submit", (e) => {
            const code = boxes.map(b => b.value).join("");

            if (code.length !== 6) {
                e.preventDefault();
                boxWrap.classList.remove("shake");
                void boxWrap.offsetWidth;
                boxWrap.classList.add("shake");
                showToast("Enter all 6 digits.", "error");
                return;
            }

            hidden.value = code;
            verifyLoadingScreen.classList.add("show");
        });

        let cooldownInterval = null;

        const cooldownKey = "resetOtpCooldownEnd";

        function startCooldown(seconds = 60) {
            sessionStorage.setItem(cooldownKey, Date.now() + seconds * 1000);
            runCooldown(seconds);
        }

        function runCooldown(seconds) {
            let left = seconds;
            resendBtn.disabled = true;

            const render = () => {
                const m = Math.floor(left / 60);
                const s = String(left % 60).padStart(2, "0");
                timerEl.textContent = `(${m}:${s})`;
            };
            render();

            clearInterval(cooldownInterval);
            cooldownInterval = setInterval(() => {
                left--;
                if (left <= 0) {
                    clearInterval(cooldownInterval);
                    resendBtn.disabled = false;
                    timerEl.textContent = "";
                } else {
                    render();
                }
            }, 1000);
        }

        resendBtn.addEventListener("click", () => {
            if (resendBtn.disabled) return;

            resendBtn.disabled = true;
            resendBtn.textContent = "Sending...";

            fetch("resendResetOTP.php", { method: "POST" })
                .then(res => res.json())
                .then(data => {
                    resendBtn.textContent = "Resend code";
                    if (data.status === "success") {
                        showToast("New code sent.", "success");
                        startCooldown();
                    } else {
                        showToast(data.message || "Could not resend code.", "error");
                        resendBtn.disabled = false;
                    }
                })
                .catch(() => {
                    resendBtn.textContent = "Resend code";
                    resendBtn.disabled = false;
                    showToast("Something went wrong.", "error");
                });
        });

        const savedEnd = sessionStorage.getItem(cooldownKey);
        if (savedEnd === null) {
            startCooldown();
        } else {
            const remaining = Math.ceil((Number(savedEnd) - Date.now()) / 1000);
            if (remaining > 0) runCooldown(remaining);
        }
    </script>
</body>

</html>