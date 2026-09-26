<?php
// header('Content-Type: application/json');
// require_once("../../auth/supervisor_auth.php");
// require_once("../../kapstongConnection.php");

// $sql = "
//     SELECT 
//         a.log_date,

//         SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) AS present_count,
//         SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) AS late_count,

//         (SELECT COUNT(*) FROM users WHERE role = 'student') -
//         SUM(CASE WHEN a.status IN ('present','late') THEN 1 ELSE 0 END) AS absent_count

//     FROM attendance_logs a
//     GROUP BY a.log_date
//     ORDER BY a.log_date ASC
// ";

// $result = $conn->query($sql);

// $labels = [];
// $present = [];
// $late = [];
// $absent = [];

// while ($row = $result->fetch_assoc()) {

//     $labels[] = $row['log_date'];
//     $present[] = (int)$row['present_count'];
//     $late[] = (int)$row['late_count'];
//     $absent[] = (int)$row['absent_count'];
// }

// echo json_encode([
//     "labels" => $labels,
//     "present" => $present,
//     "late" => $late,
//     "absent" => $absent
// ]);

// header('Content-Type: application/json');

// require_once("../../auth/supervisor_auth.php");
// require_once("../../Shared/kapstongConnection.php");
// require_once("../../Shared/functions.php");

// $month = $_GET['month'] ?? date('Y-m');

// $startDate = date('Y-m-01', strtotime($month));
// $endDate = date('Y-m-t', strtotime($month));

// $userID = $_SESSION['user_id'];

// $superID = getSupervisorIDByUserID($conn, $userID);



// $studentQuery = "

//     SELECT COUNT(*) AS total_students

//     FROM student_supervisor

//     WHERE superID = ?

//     AND status = 'ACTIVE'

// ";

// $stmtStudents = $conn->prepare($studentQuery);

// $stmtStudents->bind_param("i", $superID);

// $stmtStudents->execute();

// $totalStudents =
//     $stmtStudents
//     ->get_result()
//     ->fetch_assoc()['total_students'];


// $calStmt = $conn->prepare("
//     SELECT event_date, type 
//     FROM calendar_events 
//     WHERE event_date BETWEEN ? AND ?
// ");
// $calStmt->bind_param("ss", $startDate, $endDate);
// $calStmt->execute();
// $calResult = $calStmt->get_result();

// $calendarOverrides = [];
// while ($calRow = $calResult->fetch_assoc()) {
//     $calendarOverrides[$calRow['event_date']] = $calRow['type'];
// }


// function getDayType($dateStr, $calendarOverrides) {
//     if (isset($calendarOverrides[$dateStr])) {
//         return $calendarOverrides[$dateStr]; 
//     }
//     $weekday = date('w', strtotime($dateStr));
//     if ($weekday == 0 || $weekday == 6) {
//         return 'NO_WORK'; 
//     }
//     return 'WORKDAY'; 
// }


// $days = [];

// $current = strtotime($startDate);

// $last = strtotime($endDate);

// while ($current <= $last) {

//     $date = date('Y-m-d', $current);
//     $dayType = getDayType($date, $calendarOverrides);

//     $days[$date] = [

//         'present' => 0,
//         'late' => 0,
//         'absent' => $totalStudents

//     ];

//     $current = strtotime("+1 day", $current);
// }



// $sql = "

//     SELECT

//         a.log_date,

//         SUM(
//             CASE
//                 WHEN a.status = 'present'
//                 THEN 1
//                 ELSE 0
//             END
//         ) AS present_count,

//         SUM(
//             CASE
//                 WHEN a.status = 'late'
//                 THEN 1
//                 ELSE 0
//             END
//         ) AS late_count,

//         SUM(
//             CASE
//                 WHEN a.status = 'excused'
//                 THEN 1
//                 ELSE 0
//             END
//         ) AS excused_count

//     FROM attendance_logs a

//     INNER JOIN student_supervisor ss
//         ON a.studentID = ss.studentID

//     WHERE ss.superID = ?

//     AND ss.status = 'ACTIVE'

//     AND a.log_date BETWEEN ? AND ?

//     GROUP BY a.log_date

//     ORDER BY a.log_date ASC

// ";

// $stmt = $conn->prepare($sql);

// $stmt->bind_param(
//     "iss",
//     $superID,
//     $startDate,
//     $endDate
// );

// $stmt->execute();

// $result = $stmt->get_result();



// while ($row = $result->fetch_assoc()) {

//     $present = (int)$row['present_count'];

//     $late = (int)$row['late_count'];

//     $excusedCount = (int)$row['excused_count'];

//     $dayType = getDayType($row['log_date'], $calendarOverrides);
//     $isNoWork = ($dayType === 'NO_WORK');

//    $days[$row['log_date']] = [

//     'present' => $present,

//     'late' => $late,

//     'excused' => $excusedCount,

//     'absent' => $isNoWork ? 0 : max(0, $totalStudents - ($present + $late + $excusedCount)),
    
//     'is_no_work' => $isNoWork,

// ];
// }



// $labels = [];

// $present = [];

// $late = [];

// $absent = [];

// $excused = [];

// $noWorkFlags = [];

// foreach ($days as $date => $counts) {

//     $labels[] = date('M d', strtotime($date));

//     $present[] = $counts['present'];

//     $late[] = $counts['late'];

//     $absent[] = $counts['absent'];

//     $excused[] = $counts['excused'] ?? 0;

//     $noWorkFlags[] = $counts['is_no_work'];
// }



// echo json_encode([

//     "labels" => $labels,

//     "present" => $present,

//     "late" => $late,

//     "excused" => $excused,

//     "absent" => $absent,

//     "no_work" => $noWorkFlags,

// ]);

header('Content-Type: application/json');

require_once("../../auth/supervisor_auth.php");
require_once("../../Shared/kapstongConnection.php");
require_once("../../Shared/functions.php");

$month = $_GET['month'] ?? date('Y-m');

$startDate = date('Y-m-01', strtotime($month));
$endDate = date('Y-m-t', strtotime($month));

$userID = $_SESSION['user_id'];
$superID = getSupervisorIDByUserID($conn, $userID);

$studentQuery = "
    SELECT COUNT(*) AS total_students
    FROM student_supervisor
    WHERE superID = ? AND status = 'ACTIVE'
";
$stmtStudents = $conn->prepare($studentQuery);
$stmtStudents->bind_param("i", $superID);
$stmtStudents->execute();
$totalStudents = $stmtStudents->get_result()->fetch_assoc()['total_students'];

$calStmt = $conn->prepare("
    SELECT event_date, type 
    FROM calendar_events 
    WHERE event_date BETWEEN ? AND ?
");
$calStmt->bind_param("ss", $startDate, $endDate);
$calStmt->execute();
$calResult = $calStmt->get_result();

$calendarOverrides = [];
while ($calRow = $calResult->fetch_assoc()) {
    $calendarOverrides[$calRow['event_date']] = $calRow['type'];
}

function getDayType($dateStr, $calendarOverrides) {
    if (isset($calendarOverrides[$dateStr])) {
        return $calendarOverrides[$dateStr]; 
    }
    $weekday = date('w', strtotime($dateStr)); 
    if ($weekday == 0 || $weekday == 6) {
        return 'NO_WORK'; 
    }
    return 'WORKDAY'; 
}

$days = [];
$current = strtotime($startDate);
$last = strtotime($endDate);

while ($current <= $last) {
    $date = date('Y-m-d', $current);
    $dayType = getDayType($date, $calendarOverrides);

    $days[$date] = [
        'present' => 0,
        'late' => 0,
        'excused' => 0,
        'absent' => ($dayType === 'NO_WORK') ? 0 : $totalStudents,
        'is_no_work' => ($dayType === 'NO_WORK'),
    ];

    $current = strtotime("+1 day", $current);
}

$sql = "
    SELECT
        a.log_date,
        SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) AS present_count,
        SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) AS late_count,
        SUM(CASE WHEN a.status = 'excused' THEN 1 ELSE 0 END) AS excused_count
    FROM attendance_logs a
    INNER JOIN student_supervisor ss
        ON a.studentID = ss.studentID
    WHERE ss.superID = ?
    AND ss.status = 'ACTIVE'
    AND a.log_date BETWEEN ? AND ?
    GROUP BY a.log_date
    ORDER BY a.log_date ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $superID, $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $present = (int) $row['present_count'];
    $late = (int) $row['late_count'];
    $excusedCount = (int) $row['excused_count'];

    $dayType = getDayType($row['log_date'], $calendarOverrides);
    $isNoWork = ($dayType === 'NO_WORK');

    $days[$row['log_date']] = [
        'present' => $present,
        'late' => $late,
        'excused' => $excusedCount,
        'absent' => $isNoWork ? 0 : max(0, $totalStudents - ($present + $late + $excusedCount)),
        'is_no_work' => $isNoWork,
    ];
}

$labels = [];
$present = [];
$late = [];
$absent = [];
$excused = [];
$noWorkFlags = [];

foreach ($days as $date => $counts) {
    $labels[] = date('M d', strtotime($date));
    $present[] = $counts['present'];
    $late[] = $counts['late'];
    $absent[] = $counts['absent'];
    $excused[] = $counts['excused'];
    $noWorkFlags[] = $counts['is_no_work'];
}

echo json_encode([
    "labels" => $labels,
    "present" => $present,
    "late" => $late,
    "excused" => $excused,
    "absent" => $absent,
    "no_work" => $noWorkFlags, 
]);