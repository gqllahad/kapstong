<?php

    require_once("../../auth/supervisor_auth.php");
    require_once("../../Shared/kapstongConnection.php");

    header('Content-Type: application/json');

    $date       = $_POST['date'] ?? '';
    $type       = $_POST['type'] ?? 'WORKDAY';
    $label      = $_POST['label'] ?? '';
    $multiplier = (float) ($_POST['multiplier'] ?? 1.0);

    $userID  = $_SESSION['user_id'] ?? null;
    $ownerID = (int) ($_SESSION['superID'] ?? 0);
   

    if (!$ownerID) {
        echo json_encode(['status' => 'error', 'message' => 'Supervisor account not linked.']);
        exit;
    }

     $students = array_filter((array)($_POST['students'] ?? []));
    if (!$students) $students = ['']; 

    // $stmt = $conn->prepare("
    //     INSERT INTO calendar_events (event_date, superID, type, label, hour_multiplier, created_by)
    //     VALUES (?, ?, ?, ?, ?, ?)
    //     ON DUPLICATE KEY UPDATE type = VALUES(type), label = VALUES(label), hour_multiplier = VALUES(hour_multiplier)
    // ");
    // $stmt->bind_param("sissdi", $date, $ownerID, $type, $label, $multiplier, $userID);
    // $stmt->execute();
    if ($students !== ['']) {
        $chk = $conn->prepare("SELECT 1 FROM student_supervisor WHERE studentID = ? AND superID = ? AND status = 'ACTIVE'");
        foreach ($students as $s) {
            $chk->bind_param("si", $s, $ownerID);
            $chk->execute();
            if ($chk->get_result()->num_rows === 0) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid student.']);
                exit;
            }
        }
    }

    $conn->begin_transaction();

    $del = $conn->prepare("DELETE FROM calendar_events WHERE event_date = ? AND superID = ?");
    $del->bind_param("si", $date, $ownerID);
    $del->execute();

    $ins = $conn->prepare("
        INSERT INTO calendar_events (event_date, superID, studentID, type, label, hour_multiplier, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    foreach ($students as $s) {
        $ins->bind_param("sisssdi", $date, $ownerID, $s, $type, $label, $multiplier, $userID);
        $ins->execute();
    }

    $conn->commit();

    $logStmt = $conn->prepare("
        INSERT INTO activity_log (userID, role, action, module, description, target_type, target_id, ip_address)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $role        = "supervisor";
    $action      = "SAVE";
    $module      = "calendar";
    $description = "Saved calendar event for {$date} ({$type})";
    $targetType  = "system";
    $targetID    = $date;
    $ipAddress   = $_SERVER['REMOTE_ADDR'] ?? null;

    $logStmt->bind_param("isssssss", $userID, $role, $action, $module, $description, $targetType, $targetID, $ipAddress);
    $logStmt->execute();

    echo json_encode(['status' => 'success']);