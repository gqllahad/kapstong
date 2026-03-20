<?php
require_once("../../kapstongConnection.php");

header('Content-Type: application/json');

// Example: count users per role
$sql = "SELECT role, COUNT(*) as total FROM users GROUP BY role";
$result = $conn->query($sql);

$roles = [];
$counts = [];

while ($row = $result->fetch_assoc()) {
    $roles[] = $row['role'];
    $counts[] = (int)$row['total'];
}

echo json_encode([
    "labels" => $roles,
    "values" => $counts
]);
