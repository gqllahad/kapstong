<?php

session_start();
require_once("kapstongConnection.php");
require_once("functions.php");

date_default_timezone_set('Asia/Manila');

$timeInStart = getAttendanceSetting($conn, 'morning_time_in', '07:50:00');

$lateGraceMinutes = (int)getAttendanceSetting($conn, 'late_threshold_minutes ', 5);

$lateTime = date(
    "H:i:s",
    strtotime($timeInStart . " +{$lateGraceMinutes} minutes")
);

// $timeInEnd   = getAttendanceSetting($conn, 'late_time', '08:05:00');
// $lateTime   = getAttendanceSetting($conn, 'late_time', '08:05:00');

$timeOutMorningTime = getAttendanceSetting($conn, 'morning_time_out', '12:00:00');

$timeInAfternoonTime = getAttendanceSetting($conn, 'afternoon_time_out', '13:00:00');
$timeOutAfternoonTime = getAttendanceSetting($conn, 'afternoon_time_out', '17:00:00');

$MIN_WORK_MINUTES = (int)getAttendanceSetting(
    $conn,
    'minimum_work_minutes',
    5
);

$MIN_BREAK_MINUTES = (int)getAttendanceSetting(
    $conn,
    'minimum_break_minutes',
    5
);

$MAX_HOURS_PER_DAY = (int)getAttendanceSetting(
    $conn,
    'max_hours_per_day',
    8
);

$snackBreakTime = (int)getAttendanceSetting(
    $conn,
    'snack_break_minutes',
    15
);

$lunchBreakTime = (int)getAttendanceSetting(
    $conn,
    'lunch_break_minutes',
    60
);

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

    // logout of work
    if ($current_time >= $timeOutAfternoonTime) {

        if ($row && $row['time_out'] == null) {

            $stmtOut = $conn->prepare("
                UPDATE attendance_logs
                SET time_out = NOW()
                WHERE attendanceID = ?
            ");

            $stmtOut->bind_param("i", $row['attendanceID']);
            $stmtOut->execute();

            $_SESSION['status'] = "⛔ TIME OUT recorded.";

            exit();
        }

        echo "Workday ended.";
        exit();
    }

    // time in
    if (!$row) {

        if ($isFirstTimeToday) {

    if (
        $current_time < $timeInStart ||
        $current_time > $lateTime
    ) {
            $_SESSION['status'] =
                "⛔ Time-in allowed only between $timeInStart - $lateTime";

            echo $_SESSION['status'];
            exit();
        }

        $status = ($current_time > $lateTime)
            ? "late"
            : "present";

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

         $currentHour = date("H:i:s");

        if (
             $currentHour >= $timeOutMorningTime &&
             $currentHour <= $timeInAfternoonTime
        ) {

        if ($break_minutes < $lunchBreakTime) {

                $remaining = ceil($lunchBreakTime - $break_minutes);

                $_SESSION['status'] =
                    "🍱 Lunch break ongoing. Wait {$remaining} more minutes.";

                echo $_SESSION['status'];
                exit();
            }

            $breakType = "LUNCH BREAK";
        }else {

            if ($break_minutes < $MIN_BREAK_MINUTES) {

                $remaining = ceil($MIN_BREAK_MINUTES - $break_minutes);

                $_SESSION['status'] =
                    "☕ Break ongoing. Wait {$remaining} more minutes.";

                echo $_SESSION['status'];
                exit();
            }

            $breakType = "SHORT BREAK";
        }

        $status = "present";

        $stmtNew = $conn->prepare("
            INSERT INTO attendance_logs
            (studentID, log_date, time_in, status, rfid_uid)
            VALUES (?, CURDATE(), NOW(), ?, ?)
        ");

        $stmtNew->bind_param("sss", $studentID, $status, $rfid);
        $stmtNew->execute();

        $_SESSION['status'] =
            "✅ RETURNED FROM {$breakType}";

        echo $_SESSION['status'];
            exit();

        // if ($break_minutes < $MIN_BREAK_MINUTES) {
        //     $remaining = ceil($MIN_BREAK_MINUTES - $break_minutes);
        //     $_SESSION['status'] = " Wait {$remaining} more minutes..";
        // } else {

        //     $status = ($current_time > "08:05:00") ? "late" : "present";

        //     $stmtNew = $conn->prepare("
        //         INSERT INTO attendance_logs (studentID, log_date, time_in, status, rfid_uid)
        //         VALUES (?, CURDATE(), NOW(), ?, ?)
        //     ");
        //     $stmtNew->bind_param("sss", $studentID, $status, $rfid);
        //     $stmtNew->execute();

        //     $_SESSION['status'] = " NEW SESSION STARTED!";
        // }
    }
}
echo $_SESSION['status'] ?? "UNKNOWN STATUS";
exit();
?>
