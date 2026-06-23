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

header('Content-Type: application/json');

require_once("../../auth/supervisor_auth.php");
require_once("../../kapstongConnection.php");
require_once("../../functions.php");

$month = $_GET['month'] ?? date('Y-m');

$startDate = date('Y-m-01', strtotime($month));
$endDate = date('Y-m-t', strtotime($month));

$userID = $_SESSION['user_id'];

$superID = getSupervisorIDByUserID($conn, $userID);



$studentQuery = "

    SELECT COUNT(*) AS total_students

    FROM student_supervisor

    WHERE superID = ?

    AND status = 'ACTIVE'

";

$stmtStudents = $conn->prepare($studentQuery);

$stmtStudents->bind_param("i", $superID);

$stmtStudents->execute();

$totalStudents =
    $stmtStudents
    ->get_result()
    ->fetch_assoc()['total_students'];



$days = [];

$current = strtotime($startDate);

$last = strtotime($endDate);

while ($current <= $last) {

    $date = date('Y-m-d', $current);

    $days[$date] = [

        'present' => 0,
        'late' => 0,
        'absent' => $totalStudents

    ];

    $current = strtotime("+1 day", $current);
}



$sql = "

    SELECT

        a.log_date,

        SUM(
            CASE
                WHEN a.status = 'present'
                THEN 1
                ELSE 0
            END
        ) AS present_count,

        SUM(
            CASE
                WHEN a.status = 'late'
                THEN 1
                ELSE 0
            END
        ) AS late_count,

        SUM(
            CASE
                WHEN a.status = 'excused'
                THEN 1
                ELSE 0
            END
        ) AS excused_count

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

$stmt->bind_param(
    "iss",
    $superID,
    $startDate,
    $endDate
);

$stmt->execute();

$result = $stmt->get_result();



while ($row = $result->fetch_assoc()) {

    $present = (int)$row['present_count'];

    $late = (int)$row['late_count'];

    $excusedCount = (int)$row['excused_count'];

   $days[$row['log_date']] = [

    'present' => $present,

    'late' => $late,

    'excused' => $excusedCount,

    'absent' => max(
        0,
        $totalStudents - (
            $present +
            $late +
            $excusedCount
        )
    )

];
}



$labels = [];

$present = [];

$late = [];

$absent = [];

$excused = [];

foreach ($days as $date => $counts) {

    $labels[] = date('M d', strtotime($date));

    $present[] = $counts['present'];

    $late[] = $counts['late'];

    $absent[] = $counts['absent'];

    $excused[] = $counts['excused'] ?? 0;
}



echo json_encode([

    "labels" => $labels,

    "present" => $present,

    "late" => $late,

    "excused" => $excused,

    "absent" => $absent

]);