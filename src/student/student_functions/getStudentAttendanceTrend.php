<?php
session_start();
require_once("../../auth/student_auth.php");
require_once("../../Shared/kapstongConnection.php");
require_once("../../Shared/functions.php");

header('Content-Type: application/json');

$studentID = $_SESSION['studentID'] ?? null;

if (!$studentID) {
    echo json_encode(["error" => "No studentID"]);
    exit;
}

$query = "
SELECT 
    log_date,
    status
FROM attendance_logs
WHERE studentID = ?
AND log_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
ORDER BY log_date ASC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $studentID);
$stmt->execute();

$result = $stmt->get_result();

$days = [];

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $days[$date] = 0;
}

while ($row = $result->fetch_assoc()) {

    $value = 0;

    if ($row['status'] == 'present') $value = 1;
    if ($row['status'] == 'late') $value = 0.5;
    if ($row['status'] == 'absent') $value = 0;

    $days[$row['log_date']] = $value;
}

echo json_encode([
    "labels" => array_map(fn($d) => date("M d", strtotime($d)), array_keys($days)),
    "values" => array_values($days)
]);
?>