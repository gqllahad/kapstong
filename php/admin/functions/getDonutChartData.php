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

$labels = [];
$values = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = ucfirst($row['status']);
    $values[] = $row['total'];
}

echo json_encode([
    "labels" => $labels,
    "values" => $values
]);
?>