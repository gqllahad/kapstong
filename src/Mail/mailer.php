<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../PHPMailer/src/Exception.php';

function sendEvaluationEmail($toEmail, $studentName, $finalScore, $recommendationText)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        $mail->Username = 'madrigalinigojones@gmail.com';
        $mail->Password = 'auvgdrtezpbblwqi';

        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('madrigalinigojones@gmail.com', 'Granby OJT System Tracking System');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Final Evaluation Result';

        $mail->Body = "
            <h2>Final Evaluation Completed</h2>
            <p>Good day <b>{$studentName}</b>,</p>

            <p>Your OJT final evaluation has been completed by your supervisor.</p>

            <p><b>Final Score:</b> {$finalScore}%</p>

            <p><b>Remarks:</b> {$recommendationText}</p>

            <br>
            <p>You may now log in to view your full evaluation report.</p>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}