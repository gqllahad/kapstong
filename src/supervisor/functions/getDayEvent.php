<?php

    require_once("../../auth/supervisor_auth.php");
    require_once("../../Shared/kapstongConnection.php");
    
    $date = $_POST['date'] ?? '';
    $stmt = $conn->prepare("SELECT type, label, hour_multiplier FROM calendar_events WHERE event_date = ?");
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    header('Content-Type: application/json');

    echo json_encode($row ?: ['type' => 'WORKDAY', 'label' => '', 'hour_multiplier' => '1.0']);

?>