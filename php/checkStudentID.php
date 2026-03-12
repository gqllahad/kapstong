<?php
include("kapstongConnection.php");
include("functions.php");

if (isset($_GET['id'])) {

    $studentID = $_GET['id'];
    if ($studentID) {
        $exists = isStudentIDTaken($conn, $studentID);
        echo json_encode(["exists" => $exists]);
    } else {
        echo json_encode(["exists" => false]);
    }
    exit;
}
