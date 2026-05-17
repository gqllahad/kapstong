<?php
require_once("../kapstongConnection.php");

$sql = "
    SELECT 
        u.name,
        a.studentID,
        a.first_time_in,
        a.final_time_out,
        a.total_hours,
        a.status,
        a.remarks,
        a.current_state,
        a.log_date
    FROM attendance_logs a
    JOIN users u ON u.studentID = a.studentID
    WHERE a.log_date = CURDATE()
    ORDER BY a.first_time_in DESC
";

$result = $conn->query($sql);

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);