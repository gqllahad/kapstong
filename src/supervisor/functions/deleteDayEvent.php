<?php

    require_once("../../auth/supervisor_auth.php");
    require_once("../../Shared/kapstongConnection.php");

    $date = $_POST['date'] ?? '';

    $superID = $_SESSION['user_id'] ?? null;

    $stmt = $conn->prepare("
        DELETE FROM calendar_events
        WHERE event_date = ?
    ");

    $stmt->bind_param("s", $date);

    $stmt->execute();

    $logStmt = $conn->prepare("
        INSERT INTO activity_log (
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

    $role = "supervisor";
    $action = "DELETE";
    $module = "calendar";
    $description = "Cleared calendar event for {$date}";
    $targetType = "system";
    $targetID = $date;
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;

    $logStmt->bind_param(
        "isssssss",
        $superID,
        $role,
        $action,
        $module,
        $description,
        $targetType,
        $targetID,
        $ipAddress
    );

    $logStmt->execute();

    header('Content-Type: application/json');

    echo json_encode([
        'status' => 'success'
    ]);

?>