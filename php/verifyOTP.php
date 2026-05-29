<?php
session_start();

require_once("kapstongConnection.php");
require_once("functions.php");

if (isset($_POST['otp'])) {

    $userOTP = $_POST['otp'];

    if (time() > $_SESSION['otp_expiry']) {

        $_SESSION['otp_error'] = "expired";
        header("Location: verifyOTP.php");
        exit();
    }

    if ($userOTP == $_SESSION['signup_otp']) {

        $data = $_SESSION['signup_data'];

        $fullName = $data['fullName'];
        $email = $data['email'];
        $mobile = $data['mobile'];
        $birth = $data['birth'];
        $gender = $data['gender'];

        $course = $data['course'];
        $level = $data['level'];
        $semester = $data['semester'];
        $schoolYear = $data['schoolYear'];

        $studentID = $data['studentID'];
        $password = $data['password'];

        $fullAddress = $data['fullAddress'];

        $conn->begin_transaction();

        try {
            $sqlInsertUsers = $conn->prepare("INSERT INTO users (name, email, mobileNumber, studentID, password) VALUES (?, ?, ?, ?, ?)");
            $sqlInsertStudent = $conn->prepare("INSERT INTO ojtstudent (studentID, email, birthDate, mobileNumber, gender, course, yearLevel, name, address, semester, schoolYear) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $sqlInsertStudent->bind_param(
                "sssssssssss",
                $studentID,
                $email,
                $birth,
                $mobile,
                $gender,
                $course,
                $level,
                $fullName,
                $fullAddress,
                $semester,
                $schoolYear
            );

            $sqlInsertUsers->bind_param(
                "sssss",
                $fullName,
                $email,
                $mobile,
                $studentID,
                $password
            );

            $sqlInsertUsers->execute();
            $sqlInsertStudent->execute();

            $ip = getUserIP();

            $log = $conn->prepare("
            INSERT INTO activity_log
            (
                userID,
                role,
                action,
                module,
                description,
                target_type,
                target_id,
                ip_address
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

            $role = "student";
            $action = "Student Signup";
            $module = "AUTHENTICATION";
            $description = "New student account registered with Student ID $studentID";
            $target_type = "student";
            $target_id = $studentID;

            $newUserID = $conn->insert_id;

            $log->bind_param(
                "isssssss",
                $newUserID,
                $role,
                $action,
                $module,
                $description,
                $target_type,
                $target_id,
                $ip
            );

            $log->execute();

            $conn->commit();

            unset($_SESSION['signup_otp']);
            unset($_SESSION['signup_data']);
            unset($_SESSION['otp_expiry']);

            header("Location: loginPhase.php?success=Account+created+successfully!#log-container");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            header("Location: signupStudent.php?error=Database+error");
            exit();
        }
    } else {

        $_SESSION['otp_error'] = "invalid";
        header("Location: verifyOTP.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Verify OTP</title>
    <link rel="stylesheet" href="../css/otpPhase.css?v=123">
</head>

<body class="reset-body">
    <?php
    $otpError = $_SESSION['otp_error'] ?? null;
    unset($_SESSION['otp_error']);
    ?>

    <div id="toast" class="toast"></div>

    <div class="otp-container">

        <h2>🔐 Email Verification</h2>
        <p>Enter the 6-digit code sent to your email</p>

        <form method="POST">

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
            Didn't receive the email? Check spam/junk folder.<br><br>
            If still not received, contact support or try using a different email.
        </div>

        <a href="loginPhase.php" class="back-login">
            ← Back to Login
        </a>
    </div>

</body>
<script>
    const resendBtn = document.getElementById("resendOtpBtn");

    let cooldown = 0;


    resendBtn.addEventListener("click", () => {

        if (cooldown > 0) return;

        resendBtn.disabled = true;
        resendBtn.innerText = "Sending...";

        fetch("resendOTP.php", {
                method: "POST"
            })
            .then(res => res.json())
            .then(data => {

                if (data.status === "success") {

                    startCooldown();

                } else {
                    alert(data.message);
                    resendBtn.disabled = false;
                    resendBtn.innerText = "Resend OTP";
                }

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

    document.addEventListener("DOMContentLoaded", () => {

        const toast = document.getElementById("toast");

        function showToast(message) {
            toast.textContent = message;
            toast.classList.add("show");

            setTimeout(() => {
                toast.classList.remove("show");
            }, 3000);
        }

        const otpError = "<?= $otpError ?>";

        if (otpError === "invalid") {
            showToast(" Invalid OTP. Please try again.");
        }

        if (otpError === "expired") {
            showToast(" OTP expired. Please request a new code.");
        }

    });
</script>

</html>