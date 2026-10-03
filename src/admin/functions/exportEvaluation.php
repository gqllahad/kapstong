<?php
require_once("../../Shared/kapstongConnection.php");
require_once("../../auth/admin_auth.php");
require_once("../../Shared/functions.php");
require_once("../../../vendor/autoload.php");

require_once("../../Shared/config.php");

use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../../../PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../../../PHPMailer/src/Exception.php';


use Dompdf\Dompdf;
use PHPMailer\PHPMailer\PHPMailer;

header('Content-Type: application/json');

$id = (int)($_POST['evaluationID'] ?? 0);
$row = $id ? getEvaluationData($conn, $id) : null;

if (!$row) {
    echo json_encode(["status" => "error", "message" => "Evaluation not found"]);
    exit;
}

$dompdf = new Dompdf();
$dompdf->loadHtml(buildEvaluationDocument($row));
$dompdf->setPaper('A4');
$dompdf->render();
$pdf = $dompdf->output();

try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_USERNAME;
    $mail->Password   = MAIL_PASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom(MAIL_USERNAME, 'OJT Monitoring System');
    $mail->addAddress(MAIL_USERNAME); //coordinator
    $mail->Subject = "OJT Final Evaluation - {$row['student_name']}";
    $mail->Body    = "Attached is the final OJT evaluation of {$row['student_name']} ({$row['studentID']}).";
    $mail->addStringAttachment($pdf, "Evaluation_{$row['studentID']}.pdf");
    $mail->send();

    echo json_encode(["status" => "success", "message" => "Evaluation sent to coordinator"]);
} catch (Exception $ex) {
    echo json_encode(["status" => "error", "message" => "Failed to send email"]);
}