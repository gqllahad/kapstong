<?php
header('Content-Type: application/json');
require_once("../../auth/supervisor_auth.php");
require_once("../../kapstongConnection.php");

$sql = "
    SELECT 
        DATE(log_date) AS log_date,

        SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) AS present_count,
        SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) AS late_count,
        SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) AS absent_count

    FROM attendance_logs

    GROUP BY DATE(log_date)
    ORDER BY DATE(log_date) ASC
";

$result = $conn->query($sql);

$labels = [];
$present = [];
$late = [];
$absent = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['log_date'];
    $present[] = (int)$row['present_count'];
    $late[] = (int)$row['late_count'];
    $absent[] = (int)$row['absent_count'];
}

echo json_encode([
    "labels" => $labels,
    "present" => $present,
    "late" => $late,
    "absent" => $absent
]);
