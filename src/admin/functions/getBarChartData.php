<?php
require_once("../../Shared/kapstongConnection.php");
require_once("../../auth/admin_auth.php");

header('Content-Type: application/json');

$query = "
    SELECT 
        DAYNAME(log_date) as day_name,
        COUNT(*) as total
    FROM attendance_logs
    WHERE log_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DAYNAME(log_date)
";

$result = $conn->query($query);

$dataMap = [
    "Monday" => 0,
    "Tuesday" => 0,
    "Wednesday" => 0,
    "Thursday" => 0,
    "Friday" => 0,
    "Saturday" => 0,
    "Sunday" => 0
];

while ($row = $result->fetch_assoc()) {
    $dataMap[$row['day_name']] = $row['total'];
}

echo json_encode([
    "labels" => array_keys($dataMap),
    "values" => array_values($dataMap)
]);
?>