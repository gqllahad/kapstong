<?php

require_once("../../auth/admin_auth.php");
require_once("../../kapstongConnection.php");
require '../../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$department = $_GET['department'] ?? '';
$status     = $_GET['status'] ?? '';

$where = [];
$params = [];
$types  = '';

if (!empty($department)) {
    $where[] = "s.department = ?";
    $params[] = $department;
    $types .= 's';
}

$whereSQL = '';

if (!empty($where)) {
    $whereSQL = 'WHERE ' . implode(' AND ', $where);
}

$sql = "
    SELECT 
        s.superID,
        s.name,
        s.email,
        s.number,
        s.department,
        COUNT(ss.studentID) AS total_students
    FROM supervisor s
    LEFT JOIN student_supervisor ss 
        ON s.superID = ss.superID 
        AND ss.status = 'ACTIVE'
    $whereSQL
    GROUP BY s.superID
    ORDER BY s.name ASC
";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

$result = $stmt->get_result();

$filterText = [];

if (!empty($department)) {
    $filterText[] = "Department: " . htmlspecialchars($department);
}

$filterDisplay = !empty($filterText)
    ? implode(' | ', $filterText)
    : 'All Supervisors';

$imagePath = $_SERVER['DOCUMENT_ROOT'] . '/kapstong/kapstongImage/logo.jpg';

if (!file_exists($imagePath)) {
    die("Logo not found: " . $imagePath);
}

$imageData = base64_encode(file_get_contents($imagePath));
$src = 'data:image/jpeg;base64,' . $imageData;


$html = '

<style>

@page { margin: 28px; }

body{
    font-family: DejaVu Sans, sans-serif;
    font-size:11px;
    color:#1e293b;
}

.header-table, .footer-table{
    width:100%;
}

.logo{ width:70px; }

.title{
    text-align:center;
}

.title h1{
    margin:0;
    color:#2563eb;
}

.title p{
    margin:3px 0;
    color:#64748b;
    font-size:11px;
}

.summary{
    background:#f8fafc;
    padding:10px;
    border:1px solid #cbd5e1;
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
    font-size:10px;
    color:#64748b;
}

</style>

<div>

<table class="header-table">
<tr>

<td width="80">
    <img src="'.$src.'" class="logo">
</td>

<td class="title">
    <h1>OJT Monitoring System</h1>
    <p>Supervisor Masterlist Report</p>
    <p>Generated: '.date('F d, Y h:i A').'</p>
</td>

<td width="80"></td>

</tr>
</table>

</div>

<div class="summary">
<strong>Filters:</strong> '.$filterDisplay.'
</div>

<table>

<thead>
<tr>
    <th>Supervisor ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Contact</th>
    <th>Department</th>
    <th>Assigned Students</th>
</tr>
</thead>

<tbody>
';

$total = 0;

if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

        $total++;

        $html .= '

        <tr>
            <td>'.$row['superID'].'</td>
            <td>'.$row['name'].'</td>
            <td>'.$row['email'].'</td>
            <td>'.$row['number'].'</td>
            <td>'.$row['department'].'</td>
            <td>'.$row['total_students'].'</td>
        </tr>

        ';
    }

} else {

    $html .= '
    <tr>
        <td colspan="6" style="text-align:center;">
            No supervisors found
        </td>
    </tr>';
}

$html .= '

</tbody>

</table>

<div class="footer">
    Total Supervisors: '.$total.'
</div>

';

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

$dompdf->stream(
    'All_Supervisor_Data_' . date('Y-m-d_H-i-s') . '.pdf',
    ['Attachment' => true]
);

exit;
?>