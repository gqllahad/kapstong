<?php
require_once("../../auth/supervisor_auth.php");
require_once("../../Shared/kapstongConnection.php");

$studentID = $_GET['studentID'] ?? '';

if (empty($studentID)) {
    echo "<tr><td colspan='7' style='text-align:center;'>Invalid student ID</td></tr>";
    exit();
}

$sql = "
    SELECT 
        attendance_logs.studentID,
        users.name,
        attendance_logs.log_date,
        attendance_logs.first_time_in,
        attendance_logs.final_time_out,
        attendance_logs.status,
        attendance_logs.total_hours
    FROM attendance_logs
    INNER JOIN users 
        ON attendance_logs.studentID = users.studentID
    WHERE attendance_logs.studentID = ?
    ORDER BY attendance_logs.log_date DESC
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("s", $studentID);
$stmt->execute();

$result = $stmt->get_result();

$output = '';

if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

        $timeIn = $row['first_time_in']
            ? date("h:i A", strtotime($row['first_time_in']))
            : '--';

        $timeOut = $row['final_time_out']
            ? date("h:i A", strtotime($row['final_time_out']))
            : '--';

        $hours = $row['total_hours']
            ? number_format($row['total_hours'], 2)
            : '0.00';

        $output .= "
        <tr>
            <td>{$row['studentID']}</td>
            <td>{$row['name']}</td>
            <td>" . date("M d, Y", strtotime($row['log_date'])) . "</td>
            <td>{$timeIn}</td>
            <td>{$timeOut}</td>
            <td>{$row['status']}</td>
            <td>{$hours}</td>
        </tr>";
    }

} else {
    $output .= "
    <tr>
        <td colspan='7' style='text-align:center;'>No attendance found</td>
    </tr>";
}

echo $output;