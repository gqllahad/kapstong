<?php
session_start();
require_once("kapstongConnection.php");
require_once("functions.php");

header('Content-Type: application/json');

if (!isset($_SESSION['signup_data'])) {
    echo json_encode(["status" => "error", "message" => "Session expired"]);
    exit();
}

$data = $_SESSION['signup_data'];

$otp = rand(100000, 999999);
$_SESSION['signup_otp'] = $otp;
$_SESSION['otp_expiry'] = time() + 300;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

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
    $mail->addAddress($data['email']);

    $mail->isHTML(true);
    $mail->Subject = 'Your OTP Code';
    $mail->Body = "
        <h3>Email Verification</h3>
        <p>Your OTP code is:</p>
        <h2>$otp</h2>
        <p>This code will expire in 5 minutes.</p>
    ";

    $mail->send();

    echo json_encode(["status" => "success", "message" => "OTP resent"]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Email failed"]);
}