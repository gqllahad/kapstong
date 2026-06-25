<?php

session_start();

header('Content-Type: application/json');

require_once("../../auth/student_auth.php");
require_once("../../Shared/kapstongConnection.php");

if (!isset($_SESSION['studentID'])) {

    echo json_encode([

        "status" => "error",

        "message" => "Unauthorized"
    ]);

    exit();
}

$studentID = $_SESSION['studentID'];

if (
    !isset($_POST['report_type']) ||
    !isset($_POST['stageID'])
) {

    echo json_encode([

        "status" => "error",

        "message" => "Missing required fields"
    ]);

    exit();
}

$report_type = $_POST['report_type'];

$stageID = $_POST['stageID'];

$title = $_POST['title'] ?? '';

$content = $_POST['content'] ?? '';

$superID = 1;

$allowedTypes = [

    'image/jpeg',
    'image/png',
    'application/pdf',

    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
];

$uploadDir = "../../../uploads/student_reports/$studentID/";

if (!is_dir($uploadDir)) {

    mkdir($uploadDir, 0777, true);
}

$fileName = null;

if (!empty($_FILES['attachment']['name'])) {

    $tmpName = $_FILES['attachment']['tmp_name'];

    $type = $_FILES['attachment']['type'];

    $originalName = $_FILES['attachment']['name'];

    $size = $_FILES['attachment']['size'];

    if (!in_array($type, $allowedTypes)) {

        echo json_encode([

            "status" => "error",

            "message" => "Invalid file type"
        ]);

        exit();
    }

    if ($size > 5000000) {

        echo json_encode([

            "status" => "error",

            "message" => "File exceeds 5MB limit"
        ]);

        exit();
    }

    $ext = pathinfo(
        $originalName,
        PATHINFO_EXTENSION
    );

    $fileName =
        "report_" .
        time() .
        "." .
        $ext;

    if (
        !move_uploaded_file(
            $tmpName,
            $uploadDir . $fileName
        )
    ) {

        echo json_encode([

            "status" => "error",

            "message" => "File upload failed"
        ]);

        exit();
    }
}

$stmt = $conn->prepare("

    INSERT INTO student_reports
    (
        studentID,
        superID,
        stageID,
        report_type,
        title,
        content,
        attachment
    )

    VALUES

    (?, ?, ?, ?, ?, ?, ?)

");

$stmt->bind_param(

    "siissss",

    $studentID,
    $superID,
    $stageID,
    $report_type,
    $title,
    $content,
    $fileName
);

if ($stmt->execute()) {

    echo json_encode([

        "status" => "success",

        "message" => "Report submitted successfully"
    ]);

} else {

    echo json_encode([

        "status" => "error",

        "message" => "Database insert failed"
    ]);
}