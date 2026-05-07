<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

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
    $mail->addAddress('nyenye.arimado@gmail.com');

    $mail->isHTML(true);
    $mail->Subject = 'Test Email';
    $mail->Body = 'This is a test email from your OJT system. HAHAHAHAH';

    $mail->send();
    echo "Email sent successfully!";
} catch (Exception $e) {
    echo "Error: {$mail->ErrorInfo}";
}