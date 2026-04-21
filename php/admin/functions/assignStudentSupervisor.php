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

    $superID = $_POST['superID'] ?? null;
    $studentIDs = $_POST['studentIDs'] ?? [];
    $assigned_by = $_SESSION['user_id'];

    if (!$superID || empty($studentIDs)) {
        echo json_encode([
            "status" => "error",
            "message" => "Missing required data"
        ]);
        exit();
    }

    $conn->begin_transaction();

    try {

        foreach ($studentIDs as $studentID) {

            $check = $conn->prepare("
                SELECT COUNT(*) 
                FROM student_supervisor
                WHERE studentID = ? 
                AND status = 'ACTIVE'
            ");

            $check->bind_param("s", $studentID);
            $check->execute();
            $check->bind_result($count);
            $check->fetch();
            $check->close();

            if ($count > 0) {
                continue;
            }

            $stmt = $conn->prepare("
                INSERT INTO student_supervisor
                (studentID, superID, assigned_by, status)
                VALUES (?, ?, ?, 'ACTIVE')
            ");

            $stmt->bind_param(
                "sii",
                $studentID,
                $superID,
                $assigned_by
            );

            $stmt->execute();

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
            $action = "Assign Student";
            $module = "assignment";
            $description = "Assigned student $studentID to supervisor ID $superID";
            $target_type = "assignment";
            $target_id = $studentID;

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
        }

        $conn->commit();

        echo json_encode([
            "status" => "success",
            "message" => "Students assigned successfully"
        ]);
    } catch (Exception $e) {

        $conn->rollback();

        echo json_encode([
            "status" => "error",
            "message" => "Database error"
        ]);
    }
}
