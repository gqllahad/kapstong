<?php
require_once("../../Shared/kapstongConnection.php");
require_once("../../auth/admin_auth.php");
require '../../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;


$course = $_GET['course'] ?? '';
$supervisor = $_GET['superID'] ?? '';


$where = [];
$types = '';
$params = [];

if(!empty($course)){
    $where[] = "course = ?";
    $params[] = $course;
    $types .= 's';
}

if(!empty($ssupervisor)){
    $where[] = "superID = ?";
    $params[] = $supervisor;
    $typess = 'i';
}

$whereSQL = '';

if (!empty($where)) {
    $whereSQL = 'WHERE ' . implode(' AND ', $where);
}


$sql = "
    SELECT
        fe.evaluationID,
        fe.studentID,
        s.name            AS student_name,
        s.course,
        s.yearLevel,
        fe.superID,
        sup.name           AS supervisor_name,
        fe.attendance_score,
        fe.progress_score,
        fe.task_score,
        fe.final_score,
        fe.ethics_rating,
        fe.communication_rating,
        fe.initiative_rating,
        fe.discipline_rating,
        fe.final_recommendation,
        fe.final_remarks,
        fe.recommendation_title,
        fe.recommendation_text,
        fe.status,
        fe.created_at
    FROM final_evaluation fe
    INNER JOIN ojtstudent s ON fe.studentID = s.studentID
    LEFT JOIN supervisor sup ON fe.superID = sup.superID
    $whereSQL
    ORDER BY s.course, s.name
";
 
$stmt = $conn->prepare($sql);
 
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
 
$stmt->execute();
$result = $stmt->get_result();
 
$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}
 
$stmt->close();
 
$courseLabel     = !empty($course) ? htmlspecialchars($course) : 'All Programs';
$generatedDate   = date('F d, Y h:i A');

$imagePath = $_SERVER['DOCUMENT_ROOT'] . '/kapstong/public/kapstongImage/logo.jpg';

if (!file_exists($imagePath)) {
    die("Logo not found: " . $imagePath);
}

$imageData = base64_encode(file_get_contents($imagePath));

$src = 'data:image/jpg;base64,' . $imageData;
 
$html = '
<html>
<head>
<style>
    @page{
    margin: 28px;
}

body{
    font-family: DejaVu Sans, sans-serif;
    color:#1e293b;
    font-size:11px;
}

.header{
    width:100%;
    margin-bottom:20px;
}

.header-table{
    width:100%;
    border:none;
}

.header-table td{
    border:none;
    vertical-align:middle;
}

.logo{
    width:70px;
}

.system-title{
    text-align:center;
}

.system-title h1{
    margin:0;
    font-size:22px;
    color:#2563eb;
}

.system-title p{
    margin:3px 0;
    font-size:11px;
    color:#64748b;
}

.summary-box{
    margin-bottom:15px;
    padding:10px 14px;
    background:#f8fafc;
    border:1px solid #cbd5e1;
    border-radius:6px;
}

.summary-box strong{
    color:#0f172a;
}

.report-table{
    width:100%;
    border-collapse:collapse;
}

.report-table th{
    background:#2563eb;
    color:white;
    padding:10px;
    font-size:10px;
    text-transform:uppercase;
    letter-spacing:0.4px;
}

.report-table td{
    border:1px solid #dbeafe;
    padding:8px;
    font-size:10px;
}

.report-table tr:nth-child(even){
    background:#f8fafc;
}

.footer{
    margin-top:18px;
    width:100%;
}

.footer-table{
    width:100%;
    border:none;
}

.footer-table td{
    border:none;
    font-size:10px;
    color:#64748b;
}

.text-right{
    text-align:right;
}

.no-data{
    text-align:center;
    padding:20px;
    color:#64748b;
}
</style>
</head>
<body>

<div class="header">

    <table class="header-table">

        <tr>

            <td width="80">

                
                
            <img src="'.$src.'" class="logo">

            </td>

            <td class="system-title">

                <h1>Final Evaluation Report</h1>

                <p>Evaluation Report</p>

                <p class="sub">Program: ' . $courseLabel . ' &nbsp;|&nbsp; Generated: ' . $generatedDate . '</p>

            </td>

            <td width="80"></td>

        </tr>

    </table>

</div>
    
 
    <table class="report-table">
        <thead>
            <tr>
                <th>Student</th>
                <th>Course</th>
                <th>Supervisor</th>
                <th>Attendance</th>
                <th>Progress</th>
                <th>Task</th>
                <th>Final Score</th>
                <th>Recommendation</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
';
 
if (empty($rows)) {
    $html .= '<tr><td colspan="9" style="text-align:center;">No records found.</td></tr>';
} else {
    foreach ($rows as $r) {
        $html .= '<tr>
            <td>' . htmlspecialchars($r['student_name']) . '</td>
            <td>' . htmlspecialchars($r['course']) . '</td>
            <td>' . htmlspecialchars($r['supervisor_name'] ?? 'N/A') . '</td>
            <td>' . htmlspecialchars($r['attendance_score']) . '</td>
            <td>' . htmlspecialchars($r['progress_score']) . '</td>
            <td>' . htmlspecialchars($r['task_score']) . '</td>
            <td>' . htmlspecialchars($r['final_score']) . '</td>
            <td>' . htmlspecialchars($r['final_recommendation']) . '</td>
            <td>' . htmlspecialchars($r['status']) . '</td>
        </tr>';
    }
}
 
$html .= '
        </tbody>
    </table>
</body>
</html>
';
 
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Arial');
 
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
 
$filename = 'Evaluation_Report_' . ($course ?: 'All') . '_' . date('Ymd_His') . '.pdf';
 
$dompdf->stream($filename, ['Attachment' => true]);






?>