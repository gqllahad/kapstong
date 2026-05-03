<?php

session_start();
require_once("../../kapstongConnection.php");
require_once("../../functions.php");

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $mobile = trim($_POST['mobile']);
    $department = trim($_POST['department']);
    $passwordRaw = $_POST['password'];

    if (!$email) {
        echo json_encode(["status" => "error", "message" => "Invalid email"]);
        exit();
    }

    $password = password_hash($passwordRaw, PASSWORD_DEFAULT);

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
            (name, email, password, mobileNumber, role, isVerified, status, studentID)
            VALUES (?, ?, ?, ?, 'supervisor', 'VERIFIED', 'ACTIVE', NULL)
        ");

        $stmtUser->bind_param("ssss", $name, $email, $password, $mobile);
        $stmtUser->execute();

        $userID = $conn->insert_id;

        $stmtSupervisor = $conn->prepare("
            INSERT INTO supervisor (name, email, number, department)
            VALUES (?, ?, ?, ?)
        ");

        $stmtSupervisor->bind_param("ssss", $name, $email, $mobile, $department);
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
        $module = "user_management";
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

        echo json_encode(["status" => "success",  "message" => "Supervisor created successfully", "superID" => $superID]);
    } catch (Exception $e) {

        $conn->rollback();
        echo json_encode(["status" => "error", "message" => "Database error"]);
    }
}
