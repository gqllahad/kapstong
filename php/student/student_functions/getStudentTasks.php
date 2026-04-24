<?php
session_start();
header('Content-Type: application/json');
require_once("../../kapstongConnection.php");

$studentID = $_SESSION['studentID'];

$sql = $conn->query("
    SELECT taskID, title, status
    FROM student_tasks
    WHERE studentID = '$studentID'
");

$data = [];

while ($row = $sql->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
