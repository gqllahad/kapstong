<?php
session_start();
require_once("../../kapstongConnection.php");

header('Content-Type: application/json');

$superID = $_SESSION['superID'] ?? null;

if (!$superID) {
    echo json_encode([
        "labels" => [],
        "completed" => [],
        "pending" => [],
        "progress" => []
    ]);
    exit;
}

$sql = "
SELECT 
    ss.studentID,

    COUNT(st.taskID) AS total_tasks,

    SUM(
        CASE 
            WHEN st.status IN ('SUBMITTED', 'APPROVED') 
            THEN 1 ELSE 0 
        END
    ) AS completed_tasks,

    SUM(
        CASE 
            WHEN st.status IN ('NOT STARTED', 'IN PROGRESS', 'REJECTED') 
            THEN 1 ELSE 0 
        END
    ) AS pending_tasks

FROM student_supervisor ss

LEFT JOIN student_tasks st 
    ON ss.studentID = st.studentID
    AND st.superID = ss.superID

WHERE ss.superID = ?
AND ss.status = 'ACTIVE'

GROUP BY ss.studentID
ORDER BY ss.studentID ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $superID);
$stmt->execute();
$result = $stmt->get_result();

$labels = [];
$completed = [];
$pending = [];
$progress = [];

while ($row = $result->fetch_assoc()) {

    $labels[] = $row['studentID'];
    $completed[] = (int)$row['completed_tasks'];
    $pending[] = (int)$row['pending_tasks'];

    $total = (int)$row['total_tasks'];
    $done = (int)$row['completed_tasks'];

    $progress[] = $total > 0 ? round(($done / $total) * 100, 2) : 0;
}

echo json_encode([
    "labels" => $labels,
    "completed" => $completed,
    "pending" => $pending,
    "progress" => $progress
]);
