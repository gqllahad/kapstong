<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

require_once("kapstongConnection.php");
require_once("functions.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);


if (isset($_POST['login-submit'])) {
    $userEmail = filter_var($_POST["loginEmail"], FILTER_VALIDATE_EMAIL);

    if (!$userEmail) {
        header("Location: loginPhase.php?warning=Invalid+email+or+password");
        exit();
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
            ini_set('session.cookie_secure', 0);

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

            $ip = getUserIP();

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

            header("Location: trackerMain.php?login=Logging In..");
            exit();
        } else {

            header("Location: loginPhase.php?warning=Incorrect+Password");
            exit();
        }
    } else {

        header("Location: loginPhase.php?warning=Invalid+email+or+password");
        exit();
    }
}

if (isset($_POST['signupForm'])) {
    session_start();

    //Student Information 
    $fName = $_POST['firstName'];
    $lName = $_POST['lastName'];
    $mName = $_POST['middleName'];
    $fullName = $lName . " " .  $fName . " " . $mName;

    $email = filter_var($_POST['signEmail'], FILTER_VALIDATE_EMAIL);

    if (!$email) {
        header("Location: signupStudent.php?error=Invalid+email");
        exit();
    }

    $mobile = $_POST['rawTel'];
    $gender = $_POST['gender'];
    $birth = $_POST['signBirth'];

    $province = $_POST['province'];
    $city = $_POST['city'];
    $barangay = $_POST['barangay'];
    $street = $_POST['street'];

    $fullAddress = $street . " " . $barangay . " " . $city . ", " . $province;

    // Academic Information
    $course = $_POST['signCourse'];
    $level = $_POST['signLevel'];
    $semester = $_POST['signSemester'];
    $schoolYear = $_POST['signSY'];

    // Account Details
    $studentID = $_POST['studentID'];
    $studentID = trim($studentID);

    $password = password_hash($_POST['signPassword'], PASSWORD_DEFAULT);

    $sqlCheck = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ? OR studentID = ?");

    $sqlCheck->bind_param("ss", $email, $studentID);
    $sqlCheck->execute();
    $sqlCheck->bind_result($checkCount);
    $sqlCheck->fetch();
    $sqlCheck->close();

    if ($checkCount > 0) {
        header("Location: signupStudent.php?error=ID+Already+Exists.");
        exit();
    }

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
    "barangay" => $barangay,
    "street" => $street,
    "fullAddress" => $fullAddress,

    "course" => $course,
    "level" => $level,
    "semester" => $semester,
    "schoolYear" => $schoolYear,

    "studentID" => $studentID,
    "password" => $password
    ];

    $otp = rand(100000, 999999);

    $_SESSION['signup_otp'] = $otp;
    $_SESSION['otp_expiry'] = time() + 300;

    $mail = new PHPMailer(true);

try {

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;

    $mail->Username = 'madrigalinigojones@gmail.com';
    $mail->Password = 'auvgdrtezpbblwqi';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('madrigalinigojones@gmail.com', 'OJT System');

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

    header('Location: verifyOTP.php');
    exit();

    } catch (Exception $e) {

        header("Location: signupStudent.php?error=OTP+Email+Failed");
        exit();
    }

};
