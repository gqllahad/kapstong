<?php

session_start();
require_once("../../kapstongConnection.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "UNAUTHORIZED"]);
    exit();
}

$studentID = $_POST['studentID'] ?? null;
$superID = $_SESSION['user_id'];

if (!$studentID) {
    echo json_encode(["success" => false, "message" => "Missing studentID"]);
    exit();
}

$stmt = $conn->prepare("
    SELECT 
        fe.*,
        os.name AS student_name,
        os.email AS student_email
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

$result = $stmt->get_result();
$data = $result->fetch_assoc();

if ($data) {
     $data['attendance_score'] = round((float)$data['attendance_score'], 1);
    $data['progress_score']   = round((float)$data['progress_score'], 1);
    $data['task_score']       = round((float)$data['task_score'], 1);
    $data['final_score']      = round((float)$data['final_score'], 1);

    $data['ethics_rating']        = (int)$data['ethics_rating'];
    $data['communication_rating'] = (int)$data['communication_rating'];
    $data['initiative_rating']    = (int)$data['initiative_rating'];
    $data['discipline_rating']    = (int)$data['discipline_rating'];

    echo json_encode([
        "success" => true,
        "evaluation" => $data
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "No evaluation found"
    ]);
}