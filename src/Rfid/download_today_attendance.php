<?php

session_start();

require_once("../Shared/kapstongConnection.php");
require_once("../auth/auth_guard.php");
require '../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$role = $_SESSION['role'] ?? null;
$superID = $_SESSION['superID'] ?? null;

$sql = "
    SELECT 
        u.name,
        a.studentID,
        a.first_time_in,
        a.final_time_out,
        a.total_hours,
        a.status,
        a.current_state,
        a.remarks
    FROM attendance_logs a
    INNER JOIN users u
        ON u.studentID = a.studentID
    WHERE a.log_date = CURDATE()
";

if ($role === "supervisor") {

    $sql .= "
        AND a.studentID IN (
            SELECT studentID
            FROM student_supervisor
            WHERE superID = ?
            AND status = 'ACTIVE'
        )
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $superID);
    $stmt->execute();

    $result = $stmt->get_result();

} else {

    $sql .= " ORDER BY a.first_time_in DESC";

    $result = $conn->query($sql);
}

$imagePath = $_SERVER['DOCUMENT_ROOT'] . '/kapstong/public/kapstongImage/logo.jpg';

if (!file_exists($imagePath)) {
    die("Logo not found: " . $imagePath);
}

$imageData = base64_encode(file_get_contents($imagePath));

$src = 'data:image/jpg;base64,' . $imageData;

$html = '

<style>

body{
    font-family: DejaVu Sans, sans-serif;
    font-size: 12px;
    color:#0f172a;
}

h1{
    text-align:center;
    color:#2563eb;
    margin-bottom:5px;
}

p{
    text-align:center;
    color:#64748b;
    margin-top:0;
    margin-bottom:20px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#2563eb;
    color:white;
    padding:10px;
    font-size:11px;
}

td{
    border:1px solid #e2e8f0;
    padding:8px;
    font-size:11px;
    text-align:center;
}

.status{
    font-weight:bold;
}

.logo{
    width:100px;
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

</style>

<div class="header">
<img src="'.$src.'" class="logo">

<h1>Today Attendance Report</h1>

<p>
    Generated: '.date("F d, Y h:i A").'
</p>

</div>

<table>

    <thead>
        <tr>
            <th>Student</th>
            <th>Student ID</th>
            <th>Time In</th>
            <th>Time Out</th>
            <th>Total Hours</th>
            <th>Status</th>
            <th>Current State</th>
            <th>Remarks</th>
        </tr>
    </thead>

    <tbody>
';

while ($row = $result->fetch_assoc()) {

    $timeIn = $row['first_time_in']
        ? date("h:i A", strtotime($row['first_time_in']))
        : '-';

    $timeOut = $row['final_time_out']
        ? date("h:i A", strtotime($row['final_time_out']))
        : '-';

    $html .= '

    <tr>

        <td>'.$row['name'].'</td>

        <td>'.$row['studentID'].'</td>

        <td>'.$timeIn.'</td>

        <td>'.$timeOut.'</td>

        <td>'.($row['total_hours'] ?? 0).' hrs</td>

        <td class="status">'.$row['status'].'</td>

        <td>'.$row['current_state'].'</td>

        <td>'.$row['remarks'].'</td>

    </tr>
    ';
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

$dompdf->stream(
    "Today_Attendance_Report.pdf",
    ["Attachment" => true]
);

exit();