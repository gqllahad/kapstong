<?php


require_once("../../auth/admin_auth.php");
require_once("../../kapstongConnection.php");
require '../../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$course = $_GET['course'] ?? '';
$year   = $_GET['year'] ?? '';

$where = [];
$params = [];
$types  = '';

if (!empty($course)) {
    $where[] = "course = ?";
    $params[] = $course;
    $types .= 's';
}

if (!empty($year)) {
    $where[] = "yearLevel = ?";
    $params[] = $year;
    $types .= 's';
}

$whereSQL = '';

if (!empty($where)) {
    $whereSQL = 'WHERE ' . implode(' AND ', $where);
}


$sql = "
    SELECT 
        studentID,
        name,
        email,
        course,
        yearLevel,
        semester,
        schoolYear,
        gender,
        mobileNumber
    FROM ojtstudent
    $whereSQL
    ORDER BY name ASC
";


$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

$result = $stmt->get_result();


$filterText = [];

if (!empty($course)) {
    $filterText[] = "Course: " . htmlspecialchars($course);
}

if (!empty($year)) {
    $filterText[] = "Year Level: " . htmlspecialchars($year);
}

$filterDisplay = !empty($filterText)
    ? implode(' | ', $filterText)
    : 'All Students';


$imagePath = $_SERVER['DOCUMENT_ROOT'] . '/kapstong/kapstongImage/logo.jpg';

if (!file_exists($imagePath)) {
    die("Logo not found: " . $imagePath);
}

$imageData = base64_encode(file_get_contents($imagePath));

$src = 'data:image/jpg;base64,' . $imageData;



    $html = '

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

<div class="header">

    <table class="header-table">

        <tr>

            <td width="80">

                
                
            <img src="'.$src.'" class="logo">

            </td>

            <td class="system-title">

                <h1>OJT Monitoring System</h1>

                <p>Student Masterlist Report</p>

                <p>
                    Generated on '.date('F d, Y h:i A').'
                </p>

            </td>

            <td width="80"></td>

        </tr>

    </table>

</div>

<div class="summary-box">

    <strong>Filters Applied:</strong>
    '.$filterDisplay.'

</div>

<table class="report-table">

<thead>

<tr>
    <th>Student ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Course</th>
    <th>Year</th>
    <th>Semester</th>
    <th>School Year</th>
    <th>Gender</th>
    <th>Mobile</th>
</tr>

</thead>

<tbody>
';

$totalStudents = 0;

if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

        $totalStudents++;

        $studentID   = htmlspecialchars($row['studentID'] ?? '-');
        $name        = htmlspecialchars($row['name'] ?? '-');
        $email       = htmlspecialchars($row['email'] ?? '-');
        $course      = htmlspecialchars($row['course'] ?? '-');
        $yearLevel   = htmlspecialchars($row['yearLevel'] ?? '-');
        $semester    = htmlspecialchars($row['semester'] ?? '-');
        $schoolYear  = htmlspecialchars($row['schoolYear'] ?? '-');
        $gender      = htmlspecialchars($row['gender'] ?? '-');
        $mobile      = htmlspecialchars($row['mobileNumber'] ?? '-');

        $html .= "

        <tr>
            <td>{$studentID}</td>
            <td>{$name}</td>
            <td>{$email}</td>
            <td>{$course}</td>
            <td>{$yearLevel}</td>
            <td>{$semester}</td>
            <td>{$schoolYear}</td>
            <td>{$gender}</td>
            <td>{$mobile}</td>
        </tr>

        ";
    }

} else {

    $html .= "

    <tr>
        <td colspan='9' class='no-data'>
            No students found.
        </td>
    </tr>

    ";
}

$html .= "

</tbody>

</table>

<div class='footer'>

    <table class='footer-table'>

        <tr>

            <td>
                OJT Monitoring System Report
            </td>

            <td class='text-right'>
                Total Students: {$totalStudents}
            </td>

        </tr>

    </table>

</div>

";

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'landscape');

$dompdf->render();

$dompdf->stream(
    'OJT_All_Student_Data_' . date('Y-m-d_H-i-s') . '.pdf',
    ['Attachment' => true]
);

exit;
?>