<?php
session_start();
require_once("../Shared/kapstongConnection.php");

header('Content-Type: application/json');

$role = $_SESSION['role'] ?? null;
$superID = $_SESSION['superID'] ?? null;

if (!$role) {
    echo json_encode([]);
    exit();
}

$sql = "
    SELECT 
        u.name,
        a.studentID,
        a.first_time_in,
        a.lunch_break_out,
        a.lunch_break_in,
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

$params = [];
$types = "";

if ($role === "supervisor") {
    $sql .= "
        AND a.studentID IN (
            SELECT studentID 
            FROM student_supervisor
            WHERE superID = ?
            AND status = 'ACTIVE'
        )
    ";
    $types .= "i";
    $params[] = $superID;
} elseif ($role !== "ADMIN") {
    echo json_encode([]);
    exit();
}

$sql .= " ORDER BY a.first_time_in DESC";

$stmt = $conn->prepare($sql);

if ($types) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
exit();