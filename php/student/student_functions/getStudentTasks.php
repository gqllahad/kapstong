<?php
session_start();
header('Content-Type: application/json');
require_once("../../kapstongConnection.php");

$studentID = $_SESSION['studentID'];

$sql = $conn->prepare("
    SELECT taskID, title, status, due_date, completed_at
    FROM student_tasks
    WHERE studentID = ?
    ORDER BY date_created DESC
");

$sql->bind_param("s", $studentID);
$sql->execute();

$result = $sql->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $row['status'] = strtoupper(trim($row['status']));
    $data[] = $row;
}

echo json_encode($data);
