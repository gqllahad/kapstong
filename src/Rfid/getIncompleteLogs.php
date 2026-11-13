<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once("../auth/auth_guard.php");
require_once("../Shared/kapstongConnection.php");

requireRole(['ADMIN', 'supervisor']);
header('Content-Type: application/json');

$role    = $_SESSION['role'];
$superID = $_SESSION['superID'] ?? null;

$sql = "
    SELECT l.attendanceID, l.studentID, o.name, l.log_date,
           l.first_time_in, l.lunch_break_out, l.lunch_break_in,l.ack_at, l.ack_time_out, l.ack_note
    FROM attendance_logs l
    LEFT JOIN ojtstudent o ON o.studentID = l.studentID
    WHERE l.current_state = 'INCOMPLETE' AND l.final_time_out IS NULL";

if ($role === 'supervisor') {
    $sql .= " AND EXISTS (SELECT 1 FROM student_supervisor ss
                          WHERE ss.studentID = l.studentID
                            AND ss.superID = ? AND ss.status = 'ACTIVE')";
}
$sql .= " ORDER BY l.log_date DESC";

$stmt = $conn->prepare($sql);
if ($role === 'supervisor') $stmt->bind_param("i", $superID);
$stmt->execute();

echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));