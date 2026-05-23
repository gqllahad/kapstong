<?php

require_once("../../auth/supervisor_auth.php");
require_once("../../kapstongConnection.php");
require '../../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$superID = $_GET['superID'] ?? null;
$month   = $_GET['month'] ?? null;

if (!$superID || !$month) {
    die("Missing parameters");
}

$startDate = $month . "-01";
$endDate = date("Y-m-t", strtotime($startDate));

$imagePath = $_SERVER['DOCUMENT_ROOT'] . '/kapstong/kapstongImage/logo.jpg';

if (!file_exists($imagePath)) {
    die("Logo not found: " . $imagePath);
}

$imageData = base64_encode(file_get_contents($imagePath));
$src = 'data:image/jpeg;base64,' . $imageData;

$sql = "

    SELECT
        a.log_date,

        SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) AS present_count,
        SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) AS late_count,
        SUM(CASE WHEN a.status = 'excused' THEN 1 ELSE 0 END) AS excused_count

    FROM attendance_logs a

    INNER JOIN student_supervisor ss
        ON a.studentID = ss.studentID

    WHERE ss.superID = ?
    AND ss.status = 'ACTIVE'
    AND a.log_date BETWEEN ? AND ?

    GROUP BY a.log_date
    ORDER BY a.log_date ASC
";


$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $superID, $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();


$html = "
<style>

body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 11px;
    color: #1e293b;
}

.header-table {
    width: 100%;
    margin-bottom: 10px;
}

.logo {
    width: 70px;
}

.title {
    text-align: center;
}

.title h1 {
    margin: 0;
    color: #2563eb;
}

.title p {
    margin: 3px 0;
    color: #64748b;
    font-size: 11px;
}

h2 {
    text-align: center;
    margin-bottom: 5px;
}

p {
    text-align: center;
    margin-top: 0;
    color: #64748b;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

th {
    background: #1e3a8a;
    color: white;
    padding: 8px;
    font-size: 10px;
}

td {
    border: 1px solid #dbeafe;
    padding: 6px;
    font-size: 10px;
    text-align: center;
}

td {
    vertical-align: middle;
}

tr:nth-child(even) {
    background: #f8fafc;
}

.summary {
    text-align: center;
    margin-top: 10px;
    color: #475569;
}

</style>

<table class='header-table'>
<tr>
    <td width='80'>
        <img src='{$src}' class='logo'>
    </td>

    <td class='title'>
        <h1>OJT Monitoring System</h1>
        <p>Supervisor Attendance Report</p>
        <p>Generated: " . date('F d, Y h:i A') . "</p>
    </td>

    <td width='80'></td>
</tr>
</table>

<h2>Attendance Report</h2>
<p>Month: $month</p>
<p>Supervisor ID: $superID</p>

<table>
<thead>
<tr>
    <th>Date</th>
    <th>Present</th>
    <th>Late</th>
    <th>Excused</th>
</tr>
</thead>
<tbody>
";


$totalPresent = 0;
$totalLate = 0;
$totalExcused = 0;

while ($row = $result->fetch_assoc()) {

    $totalPresent += $row['present_count'];
    $totalLate += $row['late_count'];
    $totalExcused += $row['excused_count'];

    $html .= "
    <tr>
        <td>{$row['log_date']}</td>
        <td>{$row['present_count']}</td>
        <td>{$row['late_count']}</td>
        <td>{$row['excused_count']}</td>
    </tr>
    ";
}


$html .= "
</tbody>
</table>

<div class='summary'>
    Total Present: $totalPresent |
    Total Late: $totalLate |
    Total Excused: $totalExcused
</div>
";


$options = new Options();
$options->set("isRemoteEnabled", true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "landscape");
$dompdf->render();

$dompdf->stream(
    "Supervisor_Attendance_" . $superID . "_" . $month . ".pdf",
    ["Attachment" => true]
);

exit;

