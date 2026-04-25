<?php
session_start();
header('Content-Type: application/json');
require_once("../../kapstongConnection.php");

$studentID = $_SESSION['studentID'];

$sql = $conn->query("
    SELECT taskID, title, status, due_date, completed_at
    FROM student_tasks
    WHERE studentID = '$studentID'
     ORDER BY date_created DESC
");

$data = [];

while ($row = $sql->fetch_assoc()) {
    $row['status'] = strtoupper(trim($row['status']));
    $data[] = $row;
}

echo json_encode($data);
