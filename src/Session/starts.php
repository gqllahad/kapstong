<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../PHPMailer/src/PHPMailer.php';
require '../../PHPMailer/src/SMTP.php';
require '../../PHPMailer/src/Exception.php';

require_once("../Shared/kapstongConnection.php");
require_once("../Shared/functions.php");
require_once("../Shared/config.php");

function backWithError($msg) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['signup_error'] = $msg;
    header("Location: ../Signup/signupStudent.php");
    exit();
}

// error_reporting(E_ALL);
// ini_set('display_errors', 1);


if (isset($_POST['login-submit'])) {
    $userEmail = filter_var($_POST["loginEmail"], FILTER_VALIDATE_EMAIL);

    if (!$userEmail) {
        header("Location: loginPage.php?warning=Invalid+email+or+password");
        exit();
    }

    $ip = getUserIP();
    // $email = $userEmail;

    //     $stmt_brute = $conn->prepare("
    //         SELECT attempts, last_attempt 
    //         FROM login_attempts 
    //         WHERE email = ? AND ip_address = ?
    //     ");
    //     $stmt_brute->bind_param("ss", $email, $ip);
    //     $stmt_brute->execute();
    //     $attemptData = $stmt_brute->get_result()->fetch_assoc();

    //     if ($attemptData && $attemptData['attempts'] >= 5) {

    //         if (!empty($attemptData['last_attempt'])) {
    //             $lastAttempt = strtotime($attemptData['last_attempt']);
    //             $lockTime = 10 * 60;

    //             if (time() - $lastAttempt < $lockTime) {
    //                 header("Location: loginPhase.php?warning=Too many attempts. Try again later.");
    //                 exit();
    //             }
    //         }
    //     }

    // $stmt_brute = $conn->prepare("
    //     SELECT id, attempts, last_attempt 
    //     FROM login_attempts 
    //     WHERE email = ?
    // ");
    // $stmt_brute->bind_param("s", $userEmail);
    // $stmt_brute->execute();
    // $attemptData = $stmt_brute->get_result()->fetch_assoc();

    // $lockTime = 10 * 60; 
    // $maxAttempts = 5;

    $stmt_brute = $conn->prepare("
        SELECT id, attempts, 
            TIMESTAMPDIFF(SECOND, last_attempt, NOW()) AS elapsed_seconds
        FROM login_attempts 
        WHERE email = ?
    ");
    $stmt_brute->bind_param("s", $userEmail);
    $stmt_brute->execute();
    $attemptData = $stmt_brute->get_result()->fetch_assoc();

    $lockTime = 10 * 60;
    $maxAttempts = 5;

    // if ($attemptData && $attemptData['attempts'] >= $maxAttempts) {

    // $elapsed = max(0, (int) $attemptData['elapsed_seconds']);
//         $lastAttempt = strtotime($attemptData['last_attempt']);
// $elapsed = time() - $lastAttempt;

//     if ($elapsed < $lockTime) {
//     header("Location: loginPage.php?locked=1&email=" . urlencode($userEmail));
//     exit();
// }
    
    //  $reset = $conn->prepare("UPDATE login_attempts SET attempts = 0 WHERE id = ?");
    //     $reset->bind_param("i", $attemptData['id']);
    //     $reset->execute();


//     $reset = $conn->prepare("DELETE FROM login_attempts WHERE id = ?");
// $reset->bind_param("i", $attemptData['id']);
// $reset->execute();
    // }

    // new updated
    if ($attemptData && $attemptData['attempts'] >= $maxAttempts) {
        $elapsed = max(0, (int) $attemptData['elapsed_seconds']);

        if ($elapsed < $lockTime) {
            header("Location: loginPage.php?locked=1&email=" . urlencode($userEmail));
            exit();
        }

        $reset = $conn->prepare("UPDATE login_attempts SET attempts = 0 WHERE id = ?");
        $reset->bind_param("i", $attemptData['id']);
        $reset->execute();
    }

    $userPassword = $_POST["loginPassword"];

    $sql_prep = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $sql_prep->bind_param("s", $userEmail);
    $sql_prep->execute();

    $result = $sql_prep->get_result();

    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();

        if (password_verify($userPassword, $row['password'])) {

            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_strict_mode', 1);
            ini_set('session.cookie_secure', 0); // zero to 1 pag prodcution na 

            session_start();
            session_regenerate_id(true);

            $_SESSION['user_id'] = $row['userID'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['isVerified'] = $row['isVerified'];
            $_SESSION['mobileNumber'] = $row['mobileNumber'];

            if ($_SESSION['role'] === "student") {
                $_SESSION['studentID'] = $row["studentID"];
            }

            if ($_SESSION['role'] === "supervisor") {
                $_SESSION['superID'] = $row["superID"];
            }



            $stmt = $conn->prepare("
        INSERT INTO activity_log 
        (userID, role, action, module, description, target_type, target_id, ip_address)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

            $action = "User Login";
            $module = "AUTHENTICATION";
            $description = "User successfully logged in";

            $target_type = "user";
            $target_id = $row['userID'];

            $stmt->bind_param(
                "isssssss",
                $row['userID'],
                $row['role'],
                $action,
                $module,
                $description,
                $target_type,
                $target_id,
                $ip
            );

            $stmt->execute();

            // $delete = $conn->prepare("
            //     DELETE FROM login_attempts 
            //     WHERE email = ? AND ip_address = ?
            // ");
            // $delete->bind_param("ss", $email, $ip);
            // $delete->execute();

            $delete = $conn->prepare("DELETE FROM login_attempts WHERE email = ?");
            $delete->bind_param("s", $userEmail);
            $delete->execute();

            header("Location: trackerMain.php?login=Logging In..");
            exit();
        } else {

            // $check = $conn->prepare("
            //     SELECT id 
            //     FROM login_attempts 
            //     WHERE email = ? AND ip_address = ?
            // ");
            // $check->bind_param("ss", $email, $ip);
            // $check->execute();
            // $res = $check->get_result();

            // if ($row = $res->fetch_assoc()) {

            //     $update = $conn->prepare("
            //         UPDATE login_attempts 
            //         SET attempts = attempts + 1,
            //             last_attempt = NOW()
            //         WHERE id = ?
            //     ");
            //     $update->bind_param("i", $row['id']);
            //     $update->execute();

            // } else {

            //     $insert = $conn->prepare("
            //         INSERT INTO login_attempts (email, ip_address, attempts, last_attempt)
            //         VALUES (?, ?, 1, NOW())
            //     ");
            //     $insert->bind_param("ss", $email, $ip);
            //     $insert->execute();
            // }

            $check = $conn->prepare("SELECT id FROM login_attempts WHERE email = ?");
            $check->bind_param("s", $userEmail);
            $check->execute();
            $res = $check->get_result();

            if ($existingRow = $res->fetch_assoc()) {

                $update = $conn->prepare("
                    UPDATE login_attempts 
                    SET attempts = attempts + 1,
                        ip_address = ?,
                        last_attempt = NOW()
                    WHERE id = ?
                ");
                $update->bind_param("si", $ip, $existingRow['id']);
                $update->execute();

                } else {

                $insert = $conn->prepare("
                    INSERT INTO login_attempts (email, ip_address, attempts, last_attempt)
                    VALUES (?, ?, 1, NOW())
                ");
                $insert->bind_param("ss", $userEmail, $ip);
                $insert->execute();
            }

            header("Location: loginPage.php?warning=Incorrect+Password");
            exit();
        }
    } else {

        header("Location: loginPage.php?warning=Invalid+email+or+password");
        exit();
    }
}


if (isset($_POST['signupForm'])) {
    session_start();

    $fName = mb_strtoupper(trim($_POST['firstName'] ?? ''), 'UTF-8');
    $lName = mb_strtoupper(trim($_POST['lastName'] ?? ''), 'UTF-8');
    $mName = mb_strtoupper(trim($_POST['middleName'] ?? ''), 'UTF-8');
    if ($mName === '') $mName = 'N/A';

    if ($fName === '' || $lName === '') backWithError('Name is required');
    $nameRe = "/^[A-ZÑ\s.\-']+$/u";
    if (!preg_match($nameRe, $fName) || !preg_match($nameRe, $lName)
        || ($mName !== 'N/A' && !preg_match($nameRe, $mName))) {
        backWithError('Name contains invalid characters');
    }
    $fullName = $lName . " " . $fName . " " . $mName;

    $email = filter_var(trim($_POST['signEmail'] ?? ''), FILTER_VALIDATE_EMAIL);
    if (!$email) backWithError('Invalid email');
    $email = strtolower($email);

    $mobile = preg_replace('/\D/', '', $_POST['signTel'] ?? '');
    if (!preg_match('/^09\d{9}$/', $mobile)) backWithError('Invalid mobile number');

    $gender = $_POST['gender'] ?? '';
    if (!in_array($gender, ['Male', 'Female', 'Others'], true)) backWithError('Invalid sex');

    $birth = $_POST['signBirth'] ?? '';
    $dob = DateTime::createFromFormat('Y-m-d', $birth);
    if (!$dob || $dob->format('Y-m-d') !== $birth || $dob->diff(new DateTime())->y < 15) {
        backWithError('You must be at least 15 years old');
    }

    $province = 'Cavite'; 
    $city = trim($_POST['signAddress'] ?? '');
    if ($city === '' || mb_strlen($city) > 150) backWithError('Address is required');
    $fullAddress = $city . ", " . $province;

    $course = trim($_POST['signCourse'] ?? '');
    if ($course === '') backWithError('Please select a program');

    $level = $_POST['signLevel'] ?? '';
    if (!in_array($level, ['1st year', '2nd year', '3rd year', '4th year'], true)) backWithError('Invalid year level');

    $semester = $_POST['signSemester'] ?? '';
    if (!in_array($semester, ['1st semester', '2nd semester'], true)) backWithError('Invalid semester');

    $y = (int)date('Y');
    $schoolYear = ((int)date('n') >= 6) ? "$y-" . ($y + 1) : ($y - 1) . "-$y";

    $studentID = trim($_POST['studentID'] ?? '');
    if ($studentID === '' || strlen($studentID) > 30) backWithError('Student ID is required');

    $rawPass = $_POST['signPassword'] ?? '';
    if (strlen($rawPass) < 8 || !preg_match('/[A-Za-z]/', $rawPass) || !preg_match('/\d/', $rawPass)) {
        backWithError('Password must be 8+ characters with letters and numbers');
    }
    if ($rawPass !== ($_POST['signConfirmPassword'] ?? '')) backWithError('Passwords do not match');
    $password = password_hash($rawPass, PASSWORD_DEFAULT);

    $sqlCheck = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ? OR studentID = ?");
    $sqlCheck->bind_param("ss", $email, $studentID);
    $sqlCheck->execute();
    $sqlCheck->bind_result($checkCount);
    $sqlCheck->fetch();
    $sqlCheck->close();

    if ($checkCount > 0) backWithError('Email or Student ID already exists');

    $_SESSION['signup_data'] = [

        "firstName" => $fName,
        "lastName" => $lName,
        "middleName" => $mName,
        "fullName" => $fullName,

        "email" => $email,
        "mobile" => $mobile,
        "gender" => $gender,
        "birth" => $birth,

        "province" => $province,
        "city" => $city,
        "fullAddress" => $fullAddress,

        "course" => $course,
        "level" => $level,
        "semester" => $semester,
        "schoolYear" => $schoolYear,

        "studentID" => $studentID,
        "password" => $password
    ];

    $otp = random_int(100000, 999999);

    $_SESSION['signup_otp'] = $otp;
    $_SESSION['otp_expiry'] = time() + 300;

    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom(MAIL_USERNAME, 'OJT System');

        $mail->addAddress($email);

        $mail->isHTML(true);

        $mail->Subject = 'OTP Verification';

        $mail->Body = "
        <h2>Email Verification</h2>

        <p>Your OTP Code is:</p>

        <h1>$otp</h1>

        <p>This expires in 5 minutes.</p>
    ";

        $mail->send();

        header('Location: ../Otp/verifyOTP.php');
        exit();
    } catch (Exception $e) {

        backWithError('OTP email failed. Please try again.');
    }
};
