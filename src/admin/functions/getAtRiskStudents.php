<?php
require_once("../../Shared/kapstongConnection.php");
require_once("../../auth/admin_auth.php");
require_once("../../Shared/ojtForecast.php");

ini_set('display_errors', 0);
ini_set('log_errors', 1);
header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila');

try {

$query = "
SELECT
    u.studentID, o.name, o.course,
    COALESCE(a.absents, 0)       AS absents,
    COALESCE(a.lates, 0)         AS lates,
    COALESCE(t.overdue_tasks, 0) AS overdue_tasks,
    COALESCE(p.completed_hours, 0)  AS completed_hours,
    COALESCE(p.required_hours, 500) AS required_hours,
    p.ojt_start_date,
    s.end_date AS deadline
FROM users u
LEFT JOIN ojtstudent o ON o.studentID = u.studentID
LEFT JOIN (
    SELECT studentID,
           SUM(status = 'absent') AS absents,
           SUM(status = 'late')   AS lates
    FROM attendance_logs
    WHERE log_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY studentID
) a ON a.studentID = u.studentID
LEFT JOIN (
    SELECT studentID,
           SUM(due_date < CURDATE() AND status NOT IN ('APPROVED','SUBMITTED')) AS overdue_tasks
    FROM student_tasks
    GROUP BY studentID
) t ON t.studentID = u.studentID
LEFT JOIN student_progress p ON p.studentID = u.studentID
LEFT JOIN ojt_settings s ON s.settingID = p.settingID
WHERE u.role = 'student' AND u.isVerified = 'VERIFIED'
  AND (
        COALESCE(a.absents, 0) >= 2
     OR COALESCE(a.lates, 0) >= 5
     OR COALESCE(t.overdue_tasks, 0) >= 1
     OR COALESCE(p.completed_hours, 0) < COALESCE(p.required_hours, 500) * 0.5
  )
ORDER BY overdue_tasks DESC, absents DESC, lates DESC, completed_hours ASC
LIMIT 2";

$result = $conn->query($query);
$pace   = loadPaceMap($conn);
$data   = [];

while ($row = $result->fetch_assoc()) {

    $completed = (float)$row['completed_hours'];
    $required  = (float)$row['required_hours'];
    $progressPercent = $required > 0 ? min(100, round(($completed / $required) * 100)) : 0;

    $progressStatus = $progressPercent >= 85 ? "ON TRACK" : ($progressPercent >= 60 ? "DUE SOON" : "BEHIND");

    $risk = "LOW";
    if ($row['absents'] >= 3 || $row['overdue_tasks'] >= 2)      $risk = "HIGH";
    elseif ($row['absents'] >= 2 || $row['lates'] >= 5)          $risk = "MEDIUM";

    $f = computeForecast($required, $completed, $row['ojt_start_date'], $row['deadline'],
                         $pace[$row['studentID']] ?? null);

    $data[] = [
        "studentID" => $row['studentID'],
        "name"      => $row['name'] ?? 'Unknown Student',
        "course"    => $row['course'] ?? '',

        "absents"       => (int)$row['absents'],
        "lates"         => (int)$row['lates'],
        "overdue_tasks" => (int)$row['overdue_tasks'],

        "completed_hours"  => $completed,
        "required_hours"   => $required,
        "progress_percent" => $progressPercent,
        "progress_status"  => $progressStatus,
        "risk"             => $risk,

        "remaining_hours"  => round($f['remaining'], 1),
        "weekly_pace"      => round($f['weekly'], 1),
        "projected_finish" => $f['projected'],
        "gap_days"         => $f['gap_days'],
        "forecast"         => $f['forecast'],
    ];
}

echo json_encode($data);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage(), 'line' => $e->getLine()]);
}