<?php
header('Content-Type: application/json');
require_once("../../kapstongConnection.php");

$superID = $_POST['superID'] ?? NULL;

$attendance = $_POST['attendance'] ?? 0;
$progress = $_POST['progress'] ?? 0;
$task = $_POST['task'] ?? 0;

$total = $attendance + $progress + $task;

if ($total > 0) {
    $attendance = ($attendance / $total);
    $progress = ($progress / $total);
    $task = ($task / $total);
}

$stmt = $conn->prepare(
    "
    UPDATE evaluation_settings
    SET attendance_weight = ?,
        progress_weight = ?,
        task_weight = ?
    WHERE superID " . ($superID ? "= ?" : "IS NULL")
);

if ($superID) {
    $stmt->bind_param("dddi", $attendance, $progress, $task, $superID);
} else {
    $stmt->bind_param("ddd", $attendance, $progress, $task);
}

$stmt->execute();

echo json_encode([
    "status" => "success",
    "message" => "Evaluation settings updated"
]);
