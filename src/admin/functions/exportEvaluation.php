<?php
require_once("../../Shared/kapstongConnection.php");
require_once("../../auth/admin_auth.php");
require_once("../../Shared/functions.php");
require_once("../../../vendor/autoload.php");
require_once("../../Shared/config.php");

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Dompdf\Dompdf;

require_once __DIR__ . '/../../../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../../../PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../../../PHPMailer/src/Exception.php';

header('Content-Type: application/json');

$id = (int)($_POST['evaluationID'] ?? 0);
$row = $id ? getEvaluationData($conn, $id) : null;

if (!$row) {
    echo json_encode(["status" => "error", "message" => "Evaluation not found"]);
    exit;
}

// ── Build evaluation PDF ──
$dompdf = new Dompdf();
$dompdf->loadHtml(buildEvaluationDocument($row));
$dompdf->setPaper('A4');
$dompdf->render();
$evaluationPdf = $dompdf->output();

// ── Build certificate PDF (if a template exists) ──
$certificatePdf = generateCertificatePdf(
    $conn,
    $row['student_name'],
    $row['studentID'],
    $row['course'] ?? ''
);

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
    $mail->addAddress(MAIL_USERNAME); // coordinator
    $mail->Subject = "OJT Final Evaluation - {$row['student_name']}";

    $bodyText = "Attached is the final OJT evaluation of {$row['student_name']} ({$row['studentID']}).";
    if ($certificatePdf) {
        $bodyText .= " A certificate of completion is also included.";
    }
    $mail->Body = $bodyText;

    $mail->addStringAttachment($evaluationPdf, "Evaluation_{$row['studentID']}.pdf");

    if ($certificatePdf) {
        $mail->addStringAttachment($certificatePdf, "Certificate_{$row['studentID']}.pdf");
    }

    $mail->send();

    $message = $certificatePdf
        ? "Evaluation and certificate sent to coordinator"
        : "Evaluation sent to coordinator (no certificate template found)";

    echo json_encode(["status" => "success", "message" => $message]);
} catch (Exception $ex) {
    echo json_encode(["status" => "error", "message" => "Failed to send email"]);
}