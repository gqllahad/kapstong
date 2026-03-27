<?php
require_once("kapstongConnection.php");

header('Content-Type: application/json');

// Example: count users per role
$sql = "SELECT prg_acro, prg_name FROM program order by prg_acro";
$result = $conn->query($sql);

$prg_acro = [];
$prg_name = [];

while ($row = $result->fetch_assoc()) {
    $prg_acro[] = $row['prg_acro'];
    $prg_name[] = $row['prg_name'];
}

echo json_encode([
    "acro" => $prg_acro,
    "values" => $prg_name
]);
