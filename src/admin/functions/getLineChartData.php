<?php
// require_once("../../kapstongConnection.php");
// require_once("../../auth/admin_auth.php");

// header('Content-Type: application/json');

// $labels = [];
// $values = [];

// $days = [];

// for ($i = 29; $i >= 0; $i--) {
//     $date = date('Y-m-d', strtotime("-$i days"));
//     $days[$date] = 0;
// }

// $query = "
//     SELECT 
//         log_date,
//         COUNT(*) as total
//     FROM attendance_logs
//     WHERE log_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
//     GROUP BY log_date
//     ORDER BY log_date ASC
// ";

// $result = $conn->query($query);

// while ($row = $result->fetch_assoc()) {
//     $days[$row['log_date']] = $row['total'];
// }

// foreach ($days as $date => $total) {
//     $labels[] = date('M, d', strtotime($date));
//     $values[] = $total;
// }

// echo json_encode([
//     "labels" => $labels,
//     "values" => $values
// ]);

require_once("../../kapstongConnection.php");
require_once("../../auth/admin_auth.php");

header('Content-Type: application/json');

$month = $_GET['month'] ?? date('Y-m');

$startDate = date('Y-m-01', strtotime($month));
$endDate   = date('Y-m-t', strtotime($month));

// --- Zero-fill every day in the month ---
$days = [];
$current = strtotime($startDate);
$last    = strtotime($endDate);

while ($current <= $last) {
    $date = date('Y-m-d', $current);
    $days[$date] = 0;
    $current = strtotime("+1 day", $current);
}

// --- Query ---
$query = "
    SELECT 
        log_date,
        COUNT(*) as total
    FROM attendance_logs
    WHERE log_date BETWEEN '$startDate' AND '$endDate'
    GROUP BY log_date
    ORDER BY log_date ASC
";

$result = $conn->query($query);

if (!$result) {
    echo json_encode(["error" => $conn->error]);
    exit;
}

while ($row = $result->fetch_assoc()) {
    $days[$row['log_date']] = (int) $row['total'];
}

// --- Group into 5-day chunks ---
$labels = [];
$values = [];

$dateKeys = array_keys($days);
$chunks   = array_chunk($dateKeys, 5);

foreach ($chunks as $chunk) {
    $chunkStart = reset($chunk);
    $chunkEnd   = end($chunk);

    $chunkTotal = 0;
    foreach ($chunk as $date) {
        $chunkTotal += $days[$date];
    }

    // Short label: "Jun 01–05"
    $labels[] = date('M d', strtotime($chunkStart)) . '–' . date('d', strtotime($chunkEnd));
    $values[] = $chunkTotal;
}

echo json_encode([
    "labels" => $labels,
    "values" => $values
]);
