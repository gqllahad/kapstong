<?php

require_once("../../Shared/kapstongConnection.php");
require_once("../../auth/admin_auth.php");
require '../../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$course = $_GET['course'] ?? '';
$status = $_GET['status'] ?? '';
$superID = $_GET['superID'] ?? '';
$dateFrom = $_GET['dateFrom'] ?? '';
$dateTo = $_GET['dateTo'] ?? '';

$sql = "
    SELECT 
        attendance_logs.*,
        ojtstudent.name,
        ojtstudent.course,
        ojtstudent.yearLevel,
        users.name AS supervisor_name
    FROM attendance_logs
    LEFT JOIN ojtstudent 
        ON ojtstudent.studentID = attendance_logs.studentID
    LEFT JOIN student_supervisor
        ON student_supervisor.studentID = attendance_logs.studentID
        AND student_supervisor.status = 'ACTIVE'
    LEFT JOIN users
        ON users.userID = student_supervisor.superID
    WHERE 1=1
";

$params = [];
$types = "";

if (!empty($course)) {
    $sql .= " AND ojtstudent.course = ?";
    $types .= "s";
    $params[] = $course;
}

if (!empty($status)) {
    $sql .= " AND attendance_logs.status = ?";
    $types .= "s";
    $params[] = $status;
}

if (!empty($superID)) {
    $sql .= " AND student_supervisor.superID = ?";
    $types .= "i";
    $params[] = $superID;
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

$sql .= "
    ORDER BY attendance_logs.log_date DESC,
             attendance_logs.first_time_in DESC
";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$imagePath = $_SERVER['DOCUMENT_ROOT'] . '/kapstong/kapstongImage/logo.jpg';

if (!file_exists($imagePath)) {
    die("Logo not found: " . $imagePath);
}

$imageData = base64_encode(file_get_contents($imagePath));
$src = 'data:image/jpeg;base64,' . $imageData;

$html = '

<style>

@page {
    margin: 30px;
}

body{
    font-family: DejaVu Sans, sans-serif;
    color:#0f172a;
    font-size:11px;
}

.header{
    text-align:center;
    margin-bottom:20px;
}

.logo{
    width:70px;
    margin-bottom:10px;
}

.header h1{
    margin:0;
    color:#2563eb;
    font-size:18px;
}

.header p{
    margin:4px 0;
    color:#64748b;
}

.summary{
    background:#f8fafc;
    border:1px solid #cbd5e1;
    padding:12px;
    border-radius:8px;
    margin-bottom:15px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#2563eb;
    color:white;
    padding:10px;
    font-size:10px;
}

td{
    border:1px solid #dbeafe;
    padding:8px;
    font-size:10px;
}

tr:nth-child(even){
    background:#f8fafc;
}

.footer{
    margin-top:15px;
    text-align:right;
    color:#64748b;
    font-size:10px;
}

</style>

<div class="header">

    <img src="'.$src.'" class="logo">

    <h1>Attendance Report</h1>

    <p>Filtered Attendance Summary</p>

    <p>'.date("F d, Y").'</p>

</div>

<div class="summary">
    <strong>Total Records:</strong> '.$result->num_rows.'
</div>

<table>

<thead>
<tr>
    <th>Student</th>
    <th>Course</th>
    <th>Supervisor</th>
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

    $timeIn = !empty($row['first_time_in'])
        ? date('h:i A', strtotime($row['first_time_in']))
        : '--';

    $timeOut = !empty($row['final_time_out'])
        ? date('h:i A', strtotime($row['final_time_out']))
        : '--';

    $html .= '

    <tr>
        <td>'.$row['name'].'</td>
        <td>'.$row['course'].'</td>
        <td>'.$row['supervisor_name'].'</td>
        <td>'.date("F d, Y", strtotime($row['log_date'])).'</td>
        <td>'.$timeIn.'</td>
        <td>'.$timeOut.'</td>
        <td>'.ucfirst($row['status']).'</td>
        <td>'.$row['total_hours'].' hrs</td>
    </tr>

    ';
}

$html .= '

</tbody>
</table>

<div class="footer">
Generated: '.date('F d, Y h:i A').'
</div>

';

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("Attendance_Report.pdf", ["Attachment" => true]);

exit;
?>