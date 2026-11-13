<?php

    require_once("../../auth/supervisor_auth.php");
    require_once("../../Shared/kapstongConnection.php");

    header('Content-Type: application/json');

    $ownerID = (int) ($_SESSION['superID'] ?? 0);

    $stmt = $conn->prepare("
        SELECT o.studentID, o.name
        FROM student_supervisor ss
        JOIN ojtstudent o ON o.studentID = ss.studentID
        WHERE ss.superID = ? AND ss.status = 'ACTIVE'
        ORDER BY o.name
    ");
    $stmt->bind_param("i", $ownerID);
    $stmt->execute();

    echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));