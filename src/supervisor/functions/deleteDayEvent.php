<?php

    require_once("../../auth/supervisor_auth.php");
    require_once("../../Shared/kapstongConnection.php");

    header('Content-Type: application/json');

    $date    = $_POST['date'] ?? '';
    $userID  = $_SESSION['user_id'] ?? null;
    $ownerID = (int) ($_SESSION['superID'] ?? 0);

    if (!$ownerID) {
        echo json_encode(['status' => 'error', 'message' => 'Supervisor account not linked.']);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM calendar_events WHERE event_date = ? AND superID = ?");
    $stmt->bind_param("si", $date, $ownerID);
    $stmt->execute();

    $logStmt = $conn->prepare("
        INSERT INTO activity_log (userID, role, action, module, description, target_type, target_id, ip_address)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $role        = "supervisor";
    $action      = "DELETE";
    $module      = "calendar";
    $description = "Cleared calendar event for {$date}";
    $targetType  = "system";
    $targetID    = $date;
    $ipAddress   = $_SERVER['REMOTE_ADDR'] ?? null;

    $logStmt->bind_param("isssssss", $userID, $role, $action, $module, $description, $targetType, $targetID, $ipAddress);
    $logStmt->execute();

    echo json_encode(['status' => 'success']);