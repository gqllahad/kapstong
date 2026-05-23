<?php

require_once("../../auth/admin_auth.php");
require_once("../../kapstongConnection.php");
require '../../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$month = $_GET['month'] ?? date('Y-m');

$startDate = date('Y-m-01', strtotime($month));
$endDate = date('Y-m-t', strtotime($month));

$days = [];

$current = strtotime($startDate);
$last = strtotime($endDate);

while ($current <= $last) {

    $date = date('Y-m-d', $current);

    $days[$date] = 0;

    $current = strtotime("+1 day", $current);
}

$query = "
    SELECT 
        log_date,
        COUNT(*) as total
    FROM attendance_logs
    WHERE log_date BETWEEN '$startDate' AND '$endDate'
    GROUP BY log_date
    ORDER BY log_date ASC
";

$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {

    $days[$row['log_date']] = $row['total'];
}

$totalAttendance = array_sum($days);

$imagePath = $_SERVER['DOCUMENT_ROOT'] . '/kapstong/kapstongImage/logo.jpg';

if (!file_exists($imagePath)) {
    die("Logo not found: " . $imagePath);
}

$imageData = base64_encode(file_get_contents($imagePath));

$src = 'data:image/jpg;base64,' . $imageData;

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

.header h1{
    margin:0;
    color:#2563eb;
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

.logo{
    width:70px;
}

</style>

<div class="header">
             
        <img src="'.$src.'" class="logo">

    <h1>Attendance Analytics Report</h1>

    <p>Monthly Attendance Summary</p>

    <p>
        '.date('F Y', strtotime($month)).'
    </p>

</div>

<div class="summary">

    <strong>Total Attendance Records:</strong>
    '.$totalAttendance.'

</div>

<table>

<thead>
<tr>
    <th>Date</th>
    <th>Attendance Records</th>
</tr>
</thead>

<tbody>

';

foreach ($days as $date => $total) {

    $html .= '

    <tr>
        <td>'.date('F d, Y', strtotime($date)).'</td>
        <td>'.$total.'</td>
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

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream(
    "Attendance_Report_".$month.".pdf",
    ["Attachment" => true]
);

exit;
?>