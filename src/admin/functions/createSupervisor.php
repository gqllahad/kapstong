<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../../PHPMailer/src/PHPMailer.php';
require '../../../PHPMailer/src/SMTP.php';
require '../../../PHPMailer/src/Exception.php';

session_start();
require_once("../../Shared/kapstongConnection.php");
require_once("../../auth/admin_auth.php");
require_once("../../Shared/functions.php");

header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $mobile = trim($_POST['mobile'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $position = trim($_POST['position'] ?? '');

    if (!$email) {
        echo json_encode(["status" => "error", "message" => "Invalid email"]);
        exit();
    }

    $generatedPassword = "SUP-" . strtoupper(bin2hex(random_bytes(3))); 
    $hashedPassword = password_hash($generatedPassword, PASSWORD_DEFAULT);
     
    $check = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->bind_result($count);
    $check->fetch();
    $check->close();

    if ($count > 0) {
        echo json_encode(["status" => "error", "message" => "Email already exists"]);
        exit();
    }

    $conn->begin_transaction();

    try {

       $stmtUser = $conn->prepare("
            INSERT INTO users 
            (name, email, password, mobileNumber, role, isVerified, status, studentID, mustChangePassword)
            VALUES (?, ?, ?, ?, 'supervisor', 'VERIFIED', 'ACTIVE', NULL, 1)
        ");

$stmtUser->bind_param("ssss", $name, $email, $hashedPassword, $mobile);
$stmtUser->execute();

        $userID = $conn->insert_id;

        $stmtSupervisor = $conn->prepare("
            INSERT INTO supervisor (name, email, number, department, company_name, position)
            VALUES (?, ?, ?, ?,?,?)
        ");

        $stmtSupervisor->bind_param("ssssss", $name, $email, $mobile, $department, $company, $position);
        $stmtSupervisor->execute();

        $superID = $conn->insert_id;

        $updateUser = $conn->prepare("
            UPDATE users 
            SET superID = ? 
            WHERE userID = ?
        ");

        $updateUser->bind_param("ii", $superID, $userID);
        $updateUser->execute();

        $assigned_by = $_SESSION['user_id'];
        $ip = getUserIP();

        $log = $conn->prepare("
            INSERT INTO activity_log
            (
                userID,
                role,
                action,
                module,
                description,
                target_type,
                target_id,
                ip_address
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $role = "ADMIN";
        $action = "Create Supervisor";
        $module = "USER MANAGEMENT";
        $description = "Admin created supervisor: $name (Supervisor ID: $superID)";
        $target_type = "supervisor";
        $target_id = $superID;

        $log->bind_param(
            "isssssss",
            $assigned_by,
            $role,
            $action,
            $module,
            $description,
            $target_type,
            $target_id,
            $ip
        );

        $log->execute();

        $conn->commit();

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
            $mail->Subject = "Supervisor Account Created";

            $mail->Body = "
                <h2>Welcome to the System</h2>
                <p>Your supervisor account has been created.</p>

                <p><b>Email:</b> $email</p>
                <p><b>Temporary Password:</b> <strong>$generatedPassword</strong></p>

                <br>
                <p>Please login and change your password immediately.</p>
            ";

            $mail->send();

        } catch (Exception $e) {
            error_log("Email failed: " . $mail->ErrorInfo);
        }

        echo json_encode(["status" => "success",  "message" => "Supervisor created successfully", "superID" => $superID]);
    } catch (Exception $e) {

        $conn->rollback();
        echo json_encode(["status" => "error", "message" => "Database error"]);
    }
}
