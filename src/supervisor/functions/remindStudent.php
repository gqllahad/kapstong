<?php

header('Content-Type: application/json');
require_once("../../auth/supervisor_auth.php");
require_once("../../Shared/kapstongConnection.php");
require_once("../../Shared/functions.php");
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once("../../Shared/config.php");

require '../../../PHPMailer/src/PHPMailer.php';
require '../../../PHPMailer/src/SMTP.php';
require '../../../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

header('Content-Type: application/json');

enforceSessionTimeout('supervisor');
$superID = getSupervisorIDByUserID($conn, $_SESSION['user_id']);

$studentID = $_POST['studentID'] ?? '';
$alertType = $_POST['alertType'] ?? '';

$typeMap = [
    'warning'  => 'INACTIVE_ATTENDANCE',
    'critical' => 'INACTIVE_ATTENDANCE',
    'danger'   => 'OVERDUE_TASK'
];

if (!isset($typeMap[$alertType])) {
    echo json_encode(["status" => "error", "message" => "Invalid type"]);
    exit;
}
$warningType = $typeMap[$alertType];

$stmt = $conn->prepare("SELECT 1 FROM student_supervisor WHERE studentID = ? AND superID = ? AND status = 'ACTIVE'");
$stmt->bind_param("si", $studentID, $superID);
$stmt->execute();
if (!$stmt->get_result()->fetch_assoc()) {
    echo json_encode(["status" => "error", "message" => "Not your student"]);
    exit;
}

$stmt = $conn->prepare("SELECT 1 FROM warning_logs WHERE studentID = ? AND warning_type = ? AND sent_at > NOW() - INTERVAL 24 HOUR");
$stmt->bind_param("ss", $studentID, $warningType);
$stmt->execute();
if ($stmt->get_result()->fetch_assoc()) {
    echo json_encode(["status" => "error", "message" => "Already reminded in the last 24 hours"]);
    exit;
}

$stmt = $conn->prepare("SELECT name, email FROM ojtstudent WHERE studentID = ?");
$stmt->bind_param("s", $studentID);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    echo json_encode(["status" => "error", "message" => "Student not found"]);
    exit;
}

$isTask  = $warningType === 'OVERDUE_TASK';
$subject = $isTask ? 'Reminder: You have an overdue task' : 'Reminder: Please log your attendance';
$message = $isTask
    ? 'You have an overdue OJT task. Please submit it as soon as possible.'
    : 'No recent attendance was recorded. Please check your OJT status.';

$notifType = 'reminder'; 
$stmt = $conn->prepare("INSERT INTO notifications (userID, title, message, type, is_read, created_at) VALUES (?, ?, ?, ?, 0, NOW())");
$stmt->bind_param("ssss", $studentID, $subject, $message, $notifType);
$stmt->execute();


if (!empty($student['email'])) {
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->setFrom(MAIL_USERNAME, 'Granby OJT System Tracking System');
        $mail->addAddress($student['email'], $student['name']);
        $mail->Subject = $subject;
        $mail->Body    = "Hi {$student['name']}, " . $message;
        $mail->send();
    } catch (Exception $e) {
        error_log("Remind email failed: " . $e->getMessage());
    }
}

$stmt = $conn->prepare("INSERT INTO warning_logs (studentID, superID, warning_type) VALUES (?, ?, ?)");
$stmt->bind_param("sis", $studentID, $superID, $warningType);
$stmt->execute();

echo json_encode(["status" => "ok"]);