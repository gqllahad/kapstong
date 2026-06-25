<?php

session_start();
require_once("../../auth/student_auth.php");
require_once("../../Shared/kapstongConnection.php");
require_once("../../Shared/functions.php");

header('Content-Type: application/json');

$studentID = $_GET['studentID'] ?? null;

$query = "
SELECT 
    status,
    COUNT(*) as total
FROM student_tasks
WHERE studentID = ?
GROUP BY status
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $studentID);
$stmt->execute();

$result = $stmt->get_result();

$labels = [];
$values = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['status'];
    $values[] = $row['total'];
}

echo json_encode([
    "labels" => $labels,
    "values" => $values
]);
?>