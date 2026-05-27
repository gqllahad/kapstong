<?php

session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../PHPMailer/src/PHPMailer.php';
require '../../PHPMailer/src/SMTP.php';
require '../../PHPMailer/src/Exception.php';

require_once("../kapstongConnection.php");

if(isset($_POST['send-reset'])) {

    $email = filter_var(
        $_POST['resetEmail'],
        FILTER_VALIDATE_EMAIL
    );

    if(!$email) {

        header(
            "Location: forgotPassword.php?error=Invalid+Email"
        );

        exit();
    }

    $sql = $conn->prepare("
        SELECT email
        FROM users
        WHERE email = ?
    ");

    $sql->bind_param("s", $email);

    $sql->execute();

    $result = $sql->get_result();

    if($result->num_rows > 0) {

        $otp = rand(100000, 999999);

        $_SESSION['reset_otp'] = $otp;

        $_SESSION['reset_email'] = $email;

        $_SESSION['reset_expiry'] = time() + 300;

        $mail = new PHPMailer(true);

        try {

            $mail->isSMTP();

            $mail->Host = 'smtp.gmail.com';

            $mail->SMTPAuth = true;

            $mail->Username =
            'madrigalinigojones@gmail.com';

            $mail->Password =
            'auvgdrtezpbblwqi';

            $mail->SMTPSecure = 'tls';

            $mail->Port = 587;

            $mail->setFrom(
                'madrigalinigojones@gmail.com',
                'Granby OJT System'
            );

            $mail->addAddress($email);

            $mail->isHTML(true);

            $mail->Subject =
            'Password Reset OTP';

            $mail->Body = "
                <h2>Password Reset Request</h2>

                <p>Your OTP code is:</p>

                <h1>$otp</h1>

                <p>
                    This OTP expires in 5 minutes.
                </p>
            ";

            $mail->send();

            header(
                "Location: verifyResetOTP.php"
            );

            exit();

        } catch (Exception $e) {

            die($mail->ErrorInfo);
        }

    } else {

        header(
            "Location: forgotPassword.php?error=Email+Not+Found"
        );

        exit();
    }
}
?>