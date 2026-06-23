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

if (isset($_GET['email'])) {

    $studentEmail = $_GET['email'];
    if ($studentEmail) {
        $existsEmail = isEmailTaken($conn, $studentEmail);
        echo json_encode(["exists" => $existsEmail]);
    } else {
        echo json_encode(["exists" => false]);
    }
    exit;
}
