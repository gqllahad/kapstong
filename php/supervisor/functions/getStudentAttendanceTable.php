<?php
require_once("../../auth/supervisor_auth.php");
require_once("../../kapstongConnection.php");

$studentID = $_GET['studentID'] ?? '';

$sql = "SELECT 
            attendance_logs.studentID,
            users.name,
            attendance_logs.log_date,
            attendance_logs.time_in,
            attendance_logs.time_out,
            attendance_logs.status,
            attendance_logs.total_hours
        FROM attendance_logs
        INNER JOIN users 
            ON attendance_logs.studentID = users.studentID
        WHERE attendance_logs.studentID = '$studentID'
        ORDER BY attendance_logs.log_date DESC";

$result = $conn->query($sql);

$output = '';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        $output .= "
        <tr>
            <td>{$row['studentID']}</td>
            <td>{$row['name']}</td>
            <td>" . date("M d, Y", strtotime($row['log_date'])) . "</td>
            <td>{$row['time_in']}</td>
            <td>{$row['time_out']}</td>
            <td>{$row['status']}</td>
            <td>{$row['total_hours']}</td>
        </tr>";
    }
} else {
    $output .= "
    <tr>
        <td colspan='7' style='text-align:center;'>No attendance found</td>
    </tr>";
}

echo $output;
