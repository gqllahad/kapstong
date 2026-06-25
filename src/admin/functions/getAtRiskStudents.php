<?php
// require_once("../../kapstongConnection.php");
// require_once("../../auth/admin_auth.php");

// header('Content-Type: application/json');

// $query = "
// SELECT 
//     u.studentID,
//     o.name,

//     -- attendance risk
//     SUM(a.status = 'absent') AS absents,
//     SUM(a.status = 'late') AS lates,

//     -- task risk
//     SUM(t.due_date < CURDATE() AND t.status != 'APPROVED') AS overdue_tasks,

//     -- progress
//     p.completed_hours,
//     p.required_hours

// FROM users u

// LEFT JOIN ojtstudent o 
//     ON u.studentID = o.studentID

// LEFT JOIN attendance_logs a 
//     ON u.studentID = a.studentID
//     AND a.log_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)

// LEFT JOIN student_tasks t 
//     ON u.studentID = t.studentID

// LEFT JOIN student_progress p 
//     ON u.studentID = p.studentID

// WHERE u.role = 'student'

// GROUP BY u.studentID, o.name, p.completed_hours, p.required_hours

// HAVING 
//     absents >= 2 
//     OR lates >= 5 
//     OR overdue_tasks >= 1 
//     OR (p.completed_hours < (p.required_hours * 0.5))
// ";

// $result = $conn->query($query);

// $data = [];

// while ($row = $result->fetch_assoc()) {

//     $risk = "LOW";

//     if ($row['absents'] >= 3 || $row['overdue_tasks'] >= 2) {
//         $risk = "HIGH";
//     } elseif ($row['absents'] >= 2 || $row['lates'] >= 5) {
//         $risk = "MEDIUM";
//     }

//     $data[] = [
//         "studentID" => $row['studentID'],
//         "name" => $row['name'],
//         "absents" => $row['absents'],
//         "lates" => $row['lates'],
//         "overdue_tasks" => $row['overdue_tasks'],
//         "progress" => $row['completed_hours'] . "/" . $row['required_hours'],
//         "risk" => $risk
//     ];
// }

// echo json_encode($data);
?>

<?php
require_once("../../Shared/kapstongConnection.php");
require_once("../../auth/admin_auth.php");

header('Content-Type: application/json');

$query = "
SELECT 
    u.studentID,
    o.name,

    -- attendance risk
    COALESCE(SUM(a.status = 'absent'), 0) AS absents,
    COALESCE(SUM(a.status = 'late'), 0) AS lates,

    -- task risk
    COALESCE(SUM(
        t.due_date < CURDATE()
        AND t.status != 'APPROVED'
    ), 0) AS overdue_tasks,

    -- progress
    COALESCE(p.completed_hours, 0) AS completed_hours,
    COALESCE(p.required_hours, 500) AS required_hours

FROM users u

LEFT JOIN ojtstudent o 
    ON u.studentID = o.studentID

LEFT JOIN attendance_logs a 
    ON u.studentID = a.studentID
    AND a.log_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)

LEFT JOIN student_tasks t 
    ON u.studentID = t.studentID

LEFT JOIN student_progress p 
    ON u.studentID = p.studentID

WHERE u.role = 'student' AND u.isVerified = 'VERIFIED'

GROUP BY 
    u.studentID,
    o.name,
    p.completed_hours,
    p.required_hours

HAVING 
    absents >= 2 
    OR lates >= 5 
    OR overdue_tasks >= 1 
    OR (completed_hours < (required_hours * 0.5))

ORDER BY 
    overdue_tasks DESC,
    absents DESC,
    lates DESC,
    completed_hours ASC

LIMIT 2
";

$result = $conn->query($query);

$data = [];

while ($row = $result->fetch_assoc()) {

    $completed = (float)$row['completed_hours'];
    $required = (float)$row['required_hours'];

    $progressPercent = 0;

    if ($required > 0) {
        $progressPercent = round(($completed / $required) * 100);
    }

    if ($progressPercent > 100) {
        $progressPercent = 100;
    }

    $progressStatus = "BEHIND";

    if ($progressPercent >= 85) {
        $progressStatus = "ON TRACK";
    }
    else if ($progressPercent >= 60) {
        $progressStatus = "DUE SOON";
    }

    $risk = "LOW";

    if (
        $row['absents'] >= 3 ||
        $row['overdue_tasks'] >= 2
    ) {
        $risk = "HIGH";
    }
    elseif (
        $row['absents'] >= 2 ||
        $row['lates'] >= 5
    ) {
        $risk = "MEDIUM";
    }

    $data[] = [
        "studentID" => $row['studentID'],
        "name" => $row['name'] ?? 'Unknown Student',

        "absents" => (int)$row['absents'],
        "lates" => (int)$row['lates'],
        "overdue_tasks" => (int)$row['overdue_tasks'],

        "completed_hours" => $completed,
        "required_hours" => $required,

        "progress_percent" => $progressPercent,
        "progress_status" => $progressStatus,

        "risk" => $risk
    ];
}

echo json_encode($data);
?>