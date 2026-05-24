<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

header('Content-Type: application/json');

if (!isset($_SESSION['reset_email'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Session expired"
    ]);
    exit();
}

$email = $_SESSION['reset_email'];

$otp = rand(100000, 999999);

$_SESSION['reset_otp'] = $otp;
$_SESSION['reset_expiry'] = time() + 300;

$mail = new PHPMailer(true);

try {

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'madrigalinigojones@gmail.com';
    $mail->Password = 'auvgdrtezpbblwqi';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('madrigalinigojones@gmail.com', 'Granby OJT System');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = "Resend OTP";
    $mail->Body = "
        <h3>Your new OTP code</h3>
        <h1>$otp</h1>
        <p>This code is valid for 5 minutes.</p>
    ";

    $mail->send();

    echo json_encode([
        "status" => "success",
        "message" => "OTP resent successfully"
    ]);

} catch (Exception $e) {

    echo json_encode([
        "status" => "error",
        "message" => "Failed to send OTP"
    ]);
}
?>