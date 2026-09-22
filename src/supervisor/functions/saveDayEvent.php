<?php

    require_once("../../auth/supervisor_auth.php");
    require_once("../../Shared/kapstongConnection.php");
    $date = $_POST['date'] ?? '';
    $type = $_POST['type'] ?? 'WORKDAY';
    $label = $_POST['label'] ?? '';
    $multiplier = $_POST['multiplier'] ?? '1.0';

    $stmt = $conn->prepare("
        INSERT INTO calendar_events (event_date, type, label, hour_multiplier, created_by)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE type = VALUES(type), label = VALUES(label), hour_multiplier = VALUES(hour_multiplier)
    ");
    $superID = $_SESSION['user_id'] ?? null;
    $stmt->bind_param("sssdi", $date, $type, $label, $multiplier, $superID);
    $stmt->execute();

    header('Content-Type: application/json');
    echo json_encode(['status' => 'success']);

?>