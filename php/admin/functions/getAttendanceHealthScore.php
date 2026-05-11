<?php
require_once("../../kapstongConnection.php");

header('Content-Type: application/json');

$query = "
SELECT status, COUNT(*) as total
FROM attendance_logs
WHERE log_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
GROUP BY status
";

$result = $conn->query($query);

$present = 0;
$late = 0;
$absent = 0;
$excused = 0;

while ($row = $result->fetch_assoc()) {
    if ($row['status'] == 'present') $present = $row['total'];
    if ($row['status'] == 'late') $late = $row['total'];
    if ($row['status'] == 'absent') $absent = $row['total'];
    if ($row['status'] == 'excused') $excused = $row['total'];
}

$total = $present + $late + $absent + $excused;

if ($total == 0) {
    echo json_encode(["score" => 0]);
    exit;
}

$score = (
    ($present * 2) +
    ($late * 1) +
    ($excused * 1) +
    ($absent * -2)
) / ($total * 2) * 100;

$score = max(0, min(100, round($score)));

echo json_encode([
    "score" => $score,
    "present" => $present,
    "late" => $late,
    "absent" => $absent,
    "excused" => $excused
]);
?>