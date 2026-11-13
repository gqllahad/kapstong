<?php

    require_once("../../auth/supervisor_auth.php");
    require_once("../../Shared/kapstongConnection.php");

    header('Content-Type: application/json');

    $date    = $_POST['date'] ?? '';
    $ownerID = (int) ($_SESSION['superID'] ?? 0);

    $stmt = $conn->prepare("
        SELECT type, label, hour_multiplier, studentID
        FROM calendar_events
        WHERE event_date = ? AND superID = ?
    ");
    $stmt->bind_param("si", $date, $ownerID);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    if (!$rows) {
        echo json_encode(['type' => 'WORKDAY', 'label' => '', 'hour_multiplier' => '1.0', 'students' => []]);
        exit;
    }

    $students = array_values(array_filter(array_column($rows, 'studentID'), fn($s) => $s !== ''));

    echo json_encode([
        'type'            => $rows[0]['type'],
        'label'           => $rows[0]['label'],
        'hour_multiplier' => $rows[0]['hour_multiplier'],
        'students'        => $students   
    ]);