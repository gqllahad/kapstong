<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../../PHPMailer/src/PHPMailer.php';
require '../../../PHPMailer/src/SMTP.php';
require '../../../PHPMailer/src/Exception.php';

require_once("../../kapstongConnection.php");

function sendWarningEmail($studentEmail, $studentName, $subject, $message)
{
    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        $mail->Username = 'madrigalinigojones@gmail.com';
        $mail->Password = 'auvgdrtezpbblwqi';

        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom(
            'madrigalinigojones@gmail.com',
            'OJT Monitoring System'
        );

        $mail->addAddress($studentEmail);

        $mail->isHTML(true);

        $mail->Subject = $subject;

        $mail->Body = "
            <h2>OJT Warning Notice</h2>

            <p>Hello <b>$studentName</b>,</p>

            <p>$message</p>

            <br>

            <p>Please coordinate with your supervisor immediately.</p>

            <br>

            <small>
                This is an automated email from OJT Monitoring System.
            </small>
        ";

        $mail->send();

    } catch (Exception $e) {

        error_log(
            'Warning Email Error: ' .
            $mail->ErrorInfo
        );
    }
}

function alreadyWarned($conn, $studentID, $warningType)
{
    $sql = "
        SELECT warningID
        FROM warning_logs
        WHERE studentID = ?
        AND warning_type = ?
        AND DATE(sent_at) = CURDATE()
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ss",
        $studentID,
        $warningType
    );

    $stmt->execute();

    $result = $stmt->get_result();

    return $result->num_rows > 0;
}

function insertWarningLog($conn, $studentID, $warningType)
{
    $sql = "
        INSERT INTO warning_logs
        (
            studentID,
            warning_type
        )
        VALUES (?, ?)
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ss",
        $studentID,
        $warningType
    );

    $stmt->execute();
}


$sqlInactive = "

SELECT
    s.studentID,
    s.name,
    s.email,
    MAX(a.log_date) as last_attendance

FROM ojtstudent s

INNER JOIN student_supervisor ss
    ON ss.studentID = s.studentID

LEFT JOIN attendance_logs a
    ON a.studentID = s.studentID

WHERE ss.status = 'ACTIVE'

GROUP BY s.studentID

HAVING last_attendance IS NULL
OR last_attendance < DATE_SUB(CURDATE(), INTERVAL 3 DAY)

";

$resultInactive = $conn->query($sqlInactive);

while ($row = $resultInactive->fetch_assoc()) {

    $warningType = "INACTIVE_ATTENDANCE";

    if (
        alreadyWarned(
            $conn,
            $row['studentID'],
            $warningType
        )
    ) {
        continue;
    }

    $subject = "Inactive Attendance Warning";

    $message = "
        Our system detected that you have
        no recent attendance records
        for the past 3 days.

        Please continue your OJT attendance
        to avoid further warnings.
    ";

    sendWarningEmail(
        $row['email'],
        $row['name'],
        $subject,
        $message
    );

    insertWarningLog(
        $conn,
        $row['studentID'],
        $warningType
    );
}

$sqlTasks = "

SELECT
    s.studentID,
    s.name,
    s.email,
    t.title,
    t.due_date

FROM student_tasks t

INNER JOIN ojtstudent s
    ON s.studentID = t.studentID

INNER JOIN student_supervisor ss
    ON ss.studentID = s.studentID

WHERE ss.status = 'ACTIVE'

AND t.status IN ('NOT STARTED', 'IN PROGRESS')

AND t.due_date < CURDATE()

";

$resultTasks = $conn->query($sqlTasks);

while ($row = $resultTasks->fetch_assoc()) {

    $warningType = "OVERDUE_TASK";

    if (
        alreadyWarned(
            $conn,
            $row['studentID'],
            $warningType
        )
    ) {
        continue;
    }

    $subject = "Overdue Task Warning";

    $message = "
        Your task:
        <b>{$row['title']}</b>

        is already overdue.

        Please complete and submit it
        immediately to your supervisor.
    ";

    sendWarningEmail(
        $row['email'],
        $row['name'],
        $subject,
        $message
    );

    insertWarningLog(
        $conn,
        $row['studentID'],
        $warningType
    );
}