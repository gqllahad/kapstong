<?php
require_once("../../kapstongConnection.php");

$studentID = $_POST['studentID'] ?? '';

if (!empty($studentID)) {

    $sql = "UPDATE users SET isVerified = 'VERIFIED' WHERE studentID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $studentID);
    $stmt->execute();

    echo "success";
}
