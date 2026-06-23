<?php
require_once("../../auth/supervisor_auth.php");
require_once("../../kapstongConnection.php");

$studentID = $_GET['studentID'] ?? '';

$sql = "SELECT 
            student_tasks.title,
            student_tasks.description,
            student_tasks.status,
            student_tasks.due_date,
            student_tasks.date_created
        FROM student_tasks
        WHERE student_tasks.studentID = '$studentID'
        ORDER BY student_tasks.date_created DESC";

$result = $conn->query($sql);

$output = '';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        $statusColor = '#6B7280';

        switch ($row['status']) {
            case 'APPROVED':
                $statusColor = '#059669';
                break;
            case 'SUBMITTED':
                $statusColor = '#2563EB';
                break;
            case 'IN PROGRESS':
                $statusColor = '#F59E0B';
                break;
            case 'REJECTED':
                $statusColor = '#DC2626';
                break;
        }

        $output .= "
        <tr>
            <td style='font-weight:600;'>{$row['title']}</td>
            <td>" . substr($row['description'], 0, 50) . "...</td>
            <td>" . date("M d, Y", strtotime($row['due_date'])) . "</td>
            <td style='color: {$statusColor}; font-weight:600;'>
                {$row['status']}
            </td>
            <td>" . date("M d, Y", strtotime($row['date_created'])) . "</td>
        </tr>";
    }
} else {
    $output .= "
    <tr>
        <td colspan='5' style='text-align:center;'>No tasks found</td>
    </tr>";
}

echo $output;
