<?php

require_once("../../auth/student_auth.php");
require_once("../../Shared/kapstongConnection.php");
require '../../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$status = $_GET['status'] ?? '';
$dateFrom = $_GET['dateFrom'] ?? '';
$dateTo = $_GET['dateTo'] ?? '';

$studentID = $_SESSION['studentID'];

$sql = "
    SELECT 
        attendance_logs.*,
        ojtstudent.name,
        ojtstudent.course,
        ojtstudent.yearLevel
    FROM attendance_logs
    LEFT JOIN ojtstudent 
        ON ojtstudent.studentID = attendance_logs.studentID
    WHERE attendance_logs.studentID = ?
";

$params = [$studentID];
$types = "s";

if (!empty($status)) {
    $sql .= " AND attendance_logs.status = ?";
    $types .= "s";
    $params[] = $status;
}

if (!empty($dateFrom)) {
    $sql .= " AND attendance_logs.log_date >= ?";
    $types .= "s";
    $params[] = $dateFrom;
}

if (!empty($dateTo)) {
    $sql .= " AND attendance_logs.log_date <= ?";
    $types .= "s";
    $params[] = $dateTo;
}

$sql .= " ORDER BY attendance_logs.log_date DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$html = '
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
table { width: 100%; border-collapse: collapse; }
th { background: #2563eb; color: #fff; padding: 8px; }
td { border: 1px solid #ddd; padding: 6px; }
</style>

<h2>Attendance Report</h2>
<p>Total Records: '.$result->num_rows.'</p>

<table>
<thead>
<tr>
    <th>Student</th>
    <th>Course</th>
    <th>Date</th>
    <th>Time In</th>
    <th>Time Out</th>
    <th>Status</th>
    <th>Hours</th>
</tr>
</thead>
<tbody>
';

while ($row = $result->fetch_assoc()) {

    $timeIn = $row['first_time_in']
        ? date('h:i A', strtotime($row['first_time_in']))
        : '--';

    $timeOut = $row['final_time_out']
        ? date('h:i A', strtotime($row['final_time_out']))
        : '--';

    $html .= "
    <tr>
        <td>{$row['name']}</td>
        <td>{$row['course']}</td>
        <td>".date('M d, Y', strtotime($row['log_date']))."</td>
        <td>{$timeIn}</td>
        <td>{$timeOut}</td>
        <td>".ucfirst($row['status'])."</td>
        <td>{$row['total_hours']} hrs</td>
    </tr>";
}

$html .= '
</tbody>
</table>
';

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("Attendance_Report.pdf", ["Attachment" => true]);

exit;