<?php

session_start();
require_once("kapstongConnection.php");

date_default_timezone_set('Asia/Manila');

$MIN_WORK_MINUTES = 5;
$MIN_BREAK_MINUTES = 5;
$MAX_HOURS_PER_DAY = 8;

$current_time = date("H:i:s");
$now = time();

if (isset($_POST['rfid'])) {

    $rfid = trim($_POST['rfid']);

    if (empty($rfid)) {
        die("⚠️ RFID CANNOT BE EMPTY");
    }

    $stmt = $conn->prepare("
        SELECT userID, studentID, name, role 
        FROM users 
        WHERE rfid_uid = ?
    ");
    $stmt->bind_param("s", $rfid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        die("❌ RFID NOT REGISTERED");
    }

    $user = $result->fetch_assoc();
    $studentID = $user['studentID'];

    $_SESSION['role'] = $user['role'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['studentID'] = $studentID;

    $stmtLast = $conn->prepare("
        SELECT * FROM attendance_logs
        WHERE studentID = ? AND log_date = CURDATE()
        ORDER BY attendanceID DESC
        LIMIT 1
    ");

    $stmtLast->bind_param("s", $studentID);
    $stmtLast->execute();
    $row = $stmtLast->get_result()->fetch_assoc();

    $stmtCount = $conn->prepare("
        SELECT COUNT(*) as total
        FROM attendance_logs
        WHERE studentID = ? AND log_date = CURDATE()
    ");

    $stmtCount->bind_param("s", $studentID);
    $stmtCount->execute();
    $count = $stmtCount->get_result()->fetch_assoc();

    $isFirstTimeToday = ($count['total'] == 0);

    // time in
    if (!$row) {

        $status = "PRESENT";

        if ($isFirstTimeToday) {
            $status = ($current_time > "08:05:00") ? "LATE" : "ON TIME";
        } else {
            $status = "SESSION";
        }

        $stmtIn = $conn->prepare("
    INSERT INTO attendance_logs 
        (studentID, log_date, time_in, status, rfid_uid)
        VALUES (?, CURDATE(), NOW(), ?, ?)
        ");

        $stmtIn->bind_param("sss", $studentID, $status, $rfid);
        $stmtIn->execute();

        $_SESSION['status'] = "✅ TIME IN SUCCESS ($status)";
    }

//    time out
    else if ($row['time_out'] == null) {

          $timeIn = new DateTime($row['time_in']);
    $nowDT = new DateTime();

    $diff = $timeIn->diff($nowDT);

    $minutes_worked = ($diff->h * 60) + $diff->i;

    if ($minutes_worked < $MIN_WORK_MINUTES) {
        $_SESSION['status'] = "⚠️ Minimum {$MIN_WORK_MINUTES} minutes required";
    } else {

        $stmtOut = $conn->prepare("
            UPDATE attendance_logs
            SET time_out = NOW()
            WHERE attendanceID = ?
        ");

        $stmtOut->bind_param("i", $row['attendanceID']);
        $stmtOut->execute();

        $_SESSION['status'] = "TIME OUT SUCCESS!";
    }
    }

    // new session
    else {

        $last_timeout = strtotime($row['time_out']);
        $break_minutes = ($now - $last_timeout) / 60;

        if ($break_minutes < $MIN_BREAK_MINUTES) {
            $remaining = ceil($MIN_BREAK_MINUTES - $break_minutes);
            $_SESSION['status'] = " Wait {$remaining} more minutes..";
        } else {

            $status = ($current_time > "08:05:00") ? "late" : "present";

            $stmtNew = $conn->prepare("
                INSERT INTO attendance_logs (studentID, log_date, time_in, status, rfid_uid)
                VALUES (?, CURDATE(), NOW(), ?, ?)
            ");
            $stmtNew->bind_param("sss", $studentID, $status, $rfid);
            $stmtNew->execute();

            $_SESSION['status'] = " NEW SESSION STARTED!";
        }
    }
}
echo $_SESSION['status'] ?? "UNKNOWN STATUS";
exit();
?>
