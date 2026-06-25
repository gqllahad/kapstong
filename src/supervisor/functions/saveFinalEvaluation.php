<?php

session_start();
require_once("../../Shared/kapstongConnection.php");
require_once("../../Mail/mailer.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "UNAUTHORIZED"]);
    exit();
}

$superID = $_SESSION['user_id'];
$studentID = $_POST['studentID'] ?? null;

if (!$studentID) {
    echo json_encode(["success" => false, "message" => "Missing studentID"]);
    exit();
}

$attendance = $_POST['attendance'] ?? 0;
$progress = $_POST['progress'] ?? 0;
$tasks = $_POST['tasks'] ?? 0;
$final = $_POST['final'] ?? 0;

$ethics = $_POST['ethics'] ?? 0;
$communication = $_POST['communication'] ?? 0;
$initiative = $_POST['initiative'] ?? 0;
$discipline = $_POST['discipline'] ?? 0;

$recommendation = $_POST['recommendation'] ?? '';
$remarks = $_POST['remarks'] ?? '';
$rec_title = $_POST['rec_title'] ?? '';
$rec_text = $_POST['rec_text'] ?? '';

try {

    $stmt = $conn->prepare("
        INSERT INTO final_evaluation (
            studentID,
            superID,
            attendance_score,
            progress_score,
            task_score,
            final_score,
            ethics_rating,
            communication_rating,
            initiative_rating,
            discipline_rating,
            final_recommendation,
            final_remarks,
            recommendation_title,
            recommendation_text,
            status
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'FINALIZED')
    ");

    $stmt->bind_param(
        "siddddiiiissss",
        $studentID,
        $superID,
        $attendance,
        $progress,
        $tasks,
        $final,
        $ethics,
        $communication,
        $initiative,
        $discipline,
        $recommendation,
        $remarks,
        $rec_title,
        $rec_text
    );

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    $notifStmt = $conn->prepare("
        INSERT INTO notifications (userID, title, message, type)
        VALUES (?, ?, ?, ?)
    ");

    $title = "Final Evaluation Released";
    $message = "Your OJT final evaluation has been completed by your supervisor.";
    $type = "EVALUATION";

    $notifStmt->bind_param("ssss", $studentID, $title, $message, $type);
    $notifStmt->execute();

    $emailStmt = $conn->prepare("
        SELECT email, name
        FROM users
        WHERE studentID = ?
    ");

    $emailStmt->bind_param("s", $studentID);
    $emailStmt->execute();
    $student = $emailStmt->get_result()->fetch_assoc();

    if ($student) {
        sendEvaluationEmail(
            $student['email'],
            $student['name'],
            $final,
            $recommendation
        );
    }

    echo json_encode(["success" => true]);

} catch (Throwable $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}