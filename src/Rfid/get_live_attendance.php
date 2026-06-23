<?php
session_start();
require_once("../kapstongConnection.php");

$role = $_SESSION['role'] ?? null;
$superID = $_SESSION['superID'] ?? null;

header('Content-Type: application/json');

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
";

if ($role === "ADMIN") {

    $sql .= " ORDER BY a.first_time_in DESC";
}
elseif ($role === "supervisor") {

    $sql .= "
        AND a.studentID IN (
            SELECT studentID 
            FROM student_supervisor
            WHERE superID = ?
            AND status = 'ACTIVE'
        )
        ORDER BY a.first_time_in DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $superID);
    $stmt->execute();

    $result = $stmt->get_result();
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
    exit();
}
else {
    echo json_encode([]);
    exit();
}

$result = $conn->query($sql);

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);