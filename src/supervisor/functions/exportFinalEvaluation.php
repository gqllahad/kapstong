<?php

require_once("../../auth/supervisor_auth.php");
require_once("../../Shared/kapstongConnection.php");
require '../../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$studentID = $_GET['studentID'] ?? null;
$superID = $_SESSION['user_id'];

if (!$studentID) {
    die("Missing studentID");
}

$stmt = $conn->prepare("
    SELECT 
        fe.*,
        os.name AS student_name,
        os.studentID
    FROM final_evaluation fe
    INNER JOIN ojtstudent os
        ON fe.studentID = os.studentID
    WHERE fe.studentID = ?
      AND fe.superID = ?
    ORDER BY fe.created_at DESC
    LIMIT 1
");

$stmt->bind_param("si", $studentID, $superID);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("No evaluation found");
}

function f($n) {
    return number_format((float)$n, 1);
}

function parseEvaluationText($text) {

    $text = trim($text);

    $sections = [
        "title" => "",
        "score" => "",
        "recommendation" => "",
        "strengths" => [],
        "suggestions" => []
    ];

    $lines = preg_split("/\r\n|\n|\r/", $text);
    $mode = "recommendation";

    foreach ($lines as $line) {

        $line = trim($line);
        if ($line === "") continue;

        if (preg_match('/\d+(\.\d+)?%/', $line)) {
            $sections["score"] = $line;
            continue;
        }

        if (stripos($line, "Strengths") !== false) {
            $mode = "strengths";
            continue;
        }

        if (stripos($line, "Suggestions") !== false) {
            $mode = "suggestions";
            continue;
        }

        if (stripos($line, "Recommendation") !== false && empty($sections["recommendation"])) {
            $sections["recommendation"] = $line;
            continue;
        }

        if (empty($sections["title"])) {
            $sections["title"] = $line;
            continue;
        }

        if ($mode === "strengths") {
            $sections["strengths"][] = $line;
        } elseif ($mode === "suggestions") {
            $sections["suggestions"][] = $line;
        } else {
            $sections["recommendation"] .= " " . $line;
        }
    }

    return $sections;
}

$eval = parseEvaluationText($data['recommendation_text']);

$html = '

<style>

@page { margin: 30px; }

body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 11px;
    color: #0f172a;
}

.header {
    text-align: center;
    margin-bottom: 15px;
}

.header h1 {
    margin: 0;
    color: #2563eb;
}

.header p {
    margin: 3px 0;
    color: #64748b;
}

.card {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px;
    margin-bottom: 12px;
}

.title {
    font-weight: bold;
    color: #1d4ed8;
    margin-bottom: 6px;
}

.grid {
    width: 100%;
}

.grid td {
    padding: 6px;
    font-size: 11px;
}

.badge {
    background: #2563eb;
    color: white;
    padding: 6px 10px;
    border-radius: 6px;
    display: inline-block;
}

.section {
    margin-top: 10px;
}

ul {
    margin: 6px 0;
    padding-left: 18px;
}

.recommendation-content {
    line-height: 1.6;
    font-size: 11px;
    color: #1f2937;
}

.recommendation-content p {
    margin: 6px 0;
}

.recommendation-content ul {
    margin: 6px 0;
    padding-left: 18px;
}

.recommendation-content li {
    margin-bottom: 4px;
}

</style>

<div class="header">
    <h1>OJT Final Evaluation Report</h1>
    <p>'.$data['student_name'].' ('.$data['studentID'].')</p>
    <p>Generated: '.date("F d, Y h:i A").'</p>
</div>

<div class="card">
    <div class="title">Final Score</div>
    <span class="badge">'.f($data['final_score']).'%</span>
</div>

<div class="card">
    <div class="title">Performance Breakdown</div>
    <table class="grid">
        <tr>
            <td>Attendance</td><td>'.f($data['attendance_score']).'%</td>
        </tr>
        <tr>
            <td>Progress</td><td>'.f($data['progress_score']).'%</td>
        </tr>
        <tr>
            <td>Tasks</td><td>'.f($data['task_score']).'%</td>
        </tr>
    </table>
</div>

<div class="card">
    <div class="title">Supervisor Ratings</div>
    <table class="grid">
        <tr><td>Work Ethics</td><td>'.$data['ethics_rating'].'</td></tr>
        <tr><td>Communication</td><td>'.$data['communication_rating'].'</td></tr>
        <tr><td>Initiative</td><td>'.$data['initiative_rating'].'</td></tr>
        <tr><td>Discipline</td><td>'.$data['discipline_rating'].'</td></tr>
    </table>
</div>

<div class="card">
    <div class="title">Recommendation</div>
    <p><b>'.$data['final_recommendation'].'</b></p>
</div>

<div class="card">
    <div class="title">Remarks</div>
    <p>'.$data['final_remarks'].'</p>
</div>

';

$html .= '

<div class="card">

    <div class="title">Evaluation Summary</div>

    <p style="margin-top:8px; line-height:1.5;">
        '.$eval['recommendation'].'
    </p>

</div>

<div class="card">

    <div class="title">Key Strengths</div>
    <ul>
';

foreach ($eval['strengths'] as $s) {
    $html .= '<li>'.$s.'</li>';
}

$html .= '
    </ul>
</div>

<div class="card">

    <div class="title">Suggestions</div>
    <ul>
';

foreach ($eval['suggestions'] as $s) {
    $html .= '<li>'.$s.'</li>';
}

$html .= '
    </ul>
</div>
';

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream(
    "Final_Evaluation_".$studentID.".pdf",
    ["Attachment" => true]
);

exit;