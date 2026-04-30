<?php
session_start();
require_once("../../auth/supervisor_auth.php");
require_once("../../kapstongConnection.php");

header('Content-Type: application/json');

$superID = $_SESSION['superID'] ?? null;

if (!$superID) {
    echo json_encode([
        "labels" => [],
        "values" => []
    ]);
    exit;
}

$sql = "
SELECT 
    status,
    COUNT(*) AS total
FROM attendance_logs
WHERE studentID IN (
    SELECT studentID 
    FROM student_supervisor
    WHERE superID = ?
    AND status = 'ACTIVE'
)
GROUP BY status
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $superID);
$stmt->execute();
$result = $stmt->get_result();

$labels = [];
$values = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = ucfirst($row['status']);
    $values[] = (int)$row['total'];
}

echo json_encode([
    "labels" => $labels,
    "values" => $values
]);
