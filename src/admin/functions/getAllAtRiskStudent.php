

<?php
// require_once("../../kapstongConnection.php");
// require_once("../../auth/admin_auth.php");

// header('Content-Type: application/json');

// $query = "
// SELECT 
//     u.studentID,
//     o.name,

//     -- attendance risk
//     COALESCE(SUM(a.status = 'absent'), 0) AS absents,
//     COALESCE(SUM(a.status = 'late'), 0) AS lates,

//     -- task risk
//     COALESCE(SUM(
//         t.due_date < CURDATE()
//         AND t.status != 'APPROVED'
//     ), 0) AS overdue_tasks,

//     -- progress
//     COALESCE(p.completed_hours, 0) AS completed_hours,
//     COALESCE(p.required_hours, 500) AS required_hours

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

// WHERE u.role = 'student' AND u.isVerified = 'VERIFIED'

// GROUP BY 
//     u.studentID,
//     o.name,
//     p.completed_hours,
//     p.required_hours

// HAVING 
//     absents >= 2 
//     OR lates >= 5 
//     OR overdue_tasks >= 1 
//     OR (completed_hours < (required_hours * 0.5))

// ORDER BY 
//     overdue_tasks DESC,
//     absents DESC,
//     lates DESC,
//     completed_hours ASC
// ";

// $result = $conn->query($query);

// $data = [];

// while ($row = $result->fetch_assoc()) {

//     $completed = (float)$row['completed_hours'];
//     $required = (float)$row['required_hours'];

//     $progressPercent = 0;

//     if ($required > 0) {
//         $progressPercent = round(($completed / $required) * 100);
//     }

//     if ($progressPercent > 100) {
//         $progressPercent = 100;
//     }

//     $progressStatus = "BEHIND";

//     if ($progressPercent >= 85) {
//         $progressStatus = "ON TRACK";
//     }
//     else if ($progressPercent >= 60) {
//         $progressStatus = "DUE SOON";
//     }

//     $risk = "LOW";

//     if (
//         $row['absents'] >= 3 ||
//         $row['overdue_tasks'] >= 2
//     ) {
//         $risk = "HIGH";
//     }
//     elseif (
//         $row['absents'] >= 2 ||
//         $row['lates'] >= 5
//     ) {
//         $risk = "MEDIUM";
//     }

//     $data[] = [
//         "studentID" => $row['studentID'],
//         "name" => $row['name'] ?? 'Unknown Student',

//         "absents" => (int)$row['absents'],
//         "lates" => (int)$row['lates'],
//         "overdue_tasks" => (int)$row['overdue_tasks'],

//         "completed_hours" => $completed,
//         "required_hours" => $required,

//         "progress_percent" => $progressPercent,
//         "progress_status" => $progressStatus,

//         "risk" => $risk
//     ];
// }

// echo json_encode($data);



require_once("../../kapstongConnection.php");
require_once("../../auth/admin_auth.php");

header('Content-Type: application/json');

$query = "
SELECT
    u.studentID,
    o.name,
    o.course,
    o.yearLevel,
 
    /* ── Attendance (all-time) ───────────────────── */
    COALESCE(SUM(a.status = 'absent'),  0) AS absents,
    COALESCE(SUM(a.status = 'late'),    0) AS lates,
    COALESCE(SUM(a.status = 'present'), 0) AS presents,
    COALESCE(SUM(a.status = 'excused'), 0) AS excused,
 
    /* ── Recent absences (last 7 days) ──────────── */
    COALESCE(SUM(
        a.status = 'absent'
        AND a.log_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    ), 0) AS recent_absents,
 
    /* ── Consecutive absences (last 3 days) ─────── */
    COALESCE(SUM(
        a.status = 'absent'
        AND a.log_date >= DATE_SUB(CURDATE(), INTERVAL 3 DAY)
    ), 0) AS consecutive_absents,
 
    /* ── Task stats ──────────────────────────────── */
    COALESCE(SUM(
        t.due_date < CURDATE()
        AND t.status NOT IN ('APPROVED', 'SUBMITTED')
    ), 0) AS overdue_tasks,
 
    COALESCE(SUM(t.status = 'APPROVED'), 0)     AS completed_tasks,
    COALESCE(SUM(t.status = 'IN PROGRESS'), 0)  AS inprogress_tasks,
    COALESCE(COUNT(DISTINCT t.taskID), 0)        AS total_tasks,
 
    /* ── Progress ────────────────────────────────── */
    COALESCE(p.completed_hours, 0)  AS completed_hours,
    COALESCE(p.required_hours,  500) AS required_hours,
    p.completion_status,
 
    /* ── Last attendance date ────────────────────── */
    MAX(a.log_date) AS last_seen
 
FROM users u
 
LEFT JOIN ojtstudent o
    ON u.studentID = o.studentID
 
LEFT JOIN attendance_logs a
    ON u.studentID = a.studentID
 
LEFT JOIN student_tasks t
    ON u.studentID = t.studentID
 
LEFT JOIN student_progress p
    ON u.studentID = p.studentID
 
WHERE u.role = 'student'
  AND u.isVerified = 'VERIFIED'
 
GROUP BY
    u.studentID,
    o.name,
    o.course,
    o.yearLevel,
    p.completed_hours,
    p.required_hours,
    p.completion_status
 
HAVING
    absents        >= 2
    OR lates       >= 5
    OR overdue_tasks >= 1
    OR (completed_hours < (required_hours * 0.50))
 
ORDER BY
    overdue_tasks  DESC,
    absents        DESC,
    lates          DESC,
    completed_hours ASC
";

$result = $conn->query($query);

$data = [];

while ($row = $result->fetch_assoc()) {

    $completed = (float) $row['completed_hours'];
    $required  = (float) $row['required_hours'];

    $progressPercent = ($required > 0)
        ? min(100, round(($completed / $required) * 100, 1))
        : 0;

    $riskScore = 0;
    $riskScore += min((int)$row['absents'],          10) * 5;
    $riskScore += min((int)$row['lates'],            10) * 2;
    $riskScore += min((int)$row['overdue_tasks'],     5) * 6;
    if ($progressPercent < 25) $riskScore += 30;
    elseif ($progressPercent < 50) $riskScore += 15;
    $riskScore = min(100, $riskScore);

    if ($riskScore >= 60) {
        $riskLevel = "CRITICAL";
    } elseif ($riskScore >= 35) {
        $riskLevel = "HIGH";
    } elseif ($riskScore >= 15) {
        $riskLevel = "MEDIUM";
    } else {
        $riskLevel = "LOW";
    }

    if ($progressPercent >= 85) {
        $progressStatus = "ON TRACK";
    } elseif ($progressPercent >= 60) {
        $progressStatus = "DUE SOON";
    } else {
        $progressStatus = "BEHIND";
    }

    $daysSinceLastSeen = null;
    if ($row['last_seen']) {
        $lastDate  = new DateTime($row['last_seen']);
        $today     = new DateTime('today');
        $daysSinceLastSeen = (int) $today->diff($lastDate)->days;
    }

    $totalLogs       = (int)$row['presents'] + (int)$row['absents']
        + (int)$row['lates']    + (int)$row['excused'];
    $attendanceRate  = ($totalLogs > 0)
        ? round((((int)$row['presents'] + (int)$row['lates']) / $totalLogs) * 100)
        : 0;

    $data[] = [
        "studentID"          => $row['studentID'],
        "name"               => $row['name'] ?? 'Unknown Student',
        "course"             => $row['course'] ?? '—',
        "yearLevel"          => $row['yearLevel'] ?? '—',

        "absents"            => (int)  $row['absents'],
        "lates"              => (int)  $row['lates'],
        "presents"           => (int)  $row['presents'],
        "recent_absents"     => (int)  $row['recent_absents'],
        "consecutive_absents" => (int)  $row['consecutive_absents'],
        "attendance_rate"    => $attendanceRate,

        "overdue_tasks"      => (int)  $row['overdue_tasks'],
        "completed_tasks"    => (int)  $row['completed_tasks'],
        "inprogress_tasks"   => (int)  $row['inprogress_tasks'],
        "total_tasks"        => (int)  $row['total_tasks'],

        "completed_hours"    => $completed,
        "required_hours"     => $required,
        "progress_percent"   => $progressPercent,
        "progress_status"    => $progressStatus,
        "completion_status"  => $row['completion_status'] ?? 'ONGOING',

        "risk_score"         => $riskScore,
        "risk"               => $riskLevel,

        "days_since_last_seen" => $daysSinceLastSeen,
        "last_seen"            => $row['last_seen'],
    ];
}

echo json_encode($data);
?>