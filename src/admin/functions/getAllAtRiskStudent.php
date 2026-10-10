

<?php

require_once("../../Shared/kapstongConnection.php");
require_once("../../auth/admin_auth.php");

  ini_set('display_errors', 0);
   ini_set('log_errors', 1);

header('Content-Type: application/json');

date_default_timezone_set('Asia/Manila');

$query = "
SELECT
    u.studentID, o.name, o.course, o.yearLevel,
    COALESCE(a.absents, 0)        AS absents,
    COALESCE(a.lates, 0)          AS lates,
    COALESCE(a.presents, 0)       AS presents,
    COALESCE(a.excused, 0)        AS excused,
    COALESCE(a.recent_absents, 0) AS recent_absents,
    a.last_seen,
    COALESCE(t.overdue_tasks, 0)  AS overdue_tasks,
    COALESCE(t.completed_tasks, 0) AS completed_tasks,
    COALESCE(t.inprogress_tasks, 0) AS inprogress_tasks,
    COALESCE(t.total_tasks, 0)    AS total_tasks,
    COALESCE(p.completed_hours, 0)  AS completed_hours,
    COALESCE(p.required_hours, 500) AS required_hours,
    p.completion_status,
    p.ojt_start_date,
    s.end_date AS deadline
FROM users u
LEFT JOIN ojtstudent o ON o.studentID = u.studentID
LEFT JOIN (
    SELECT studentID,
           SUM(status = 'absent')  AS absents,
           SUM(status = 'late')    AS lates,
           SUM(status = 'present') AS presents,
           SUM(status = 'excused') AS excused,
           SUM(status = 'absent' AND log_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)) AS recent_absents,
           MAX(log_date) AS last_seen
    FROM attendance_logs GROUP BY studentID
) a ON a.studentID = u.studentID
LEFT JOIN (
    SELECT studentID,
           SUM(due_date < CURDATE() AND status NOT IN ('APPROVED','SUBMITTED')) AS overdue_tasks,
           SUM(status = 'APPROVED')    AS completed_tasks,
           SUM(status = 'IN PROGRESS') AS inprogress_tasks,
           COUNT(*) AS total_tasks
    FROM student_tasks GROUP BY studentID
) t ON t.studentID = u.studentID
LEFT JOIN student_progress p ON p.studentID = u.studentID
LEFT JOIN ojt_settings s ON s.settingID = p.settingID
WHERE u.role = 'student' AND u.isVerified = 'VERIFIED'";

$result = $conn->query($query);

   if (!$result) {
       http_response_code(500);
       echo json_encode(['error' => $conn->error]);
       exit;
   }

$pace = [];
$pr = $conn->query("SELECT studentID,
                           SUM(CASE WHEN log_date >= DATE_SUB(CURDATE(), INTERVAL 28 DAY)
                                    THEN total_hours ELSE 0 END) AS hrs28,
                           MIN(log_date) AS first_log
                    FROM attendance_logs
                    WHERE status <> 'voided'
                    GROUP BY studentID");
while ($r = $pr->fetch_assoc()) {
    $pace[$r['studentID']] = $r;
}

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


    $remaining = max(0, $required - $completed);  
    $p        = $pace[$row['studentID']] ?? null;
    $startStr = $row['ojt_start_date'] ?: ($p['first_log'] ?? null);

    $weekly = 0; $projected = null; $gapDays = null; $requiredPace = null;

    if ($remaining <= 0) {
        $forecast = 'COMPLETED';
    } elseif (!$startStr) {
        $forecast = 'NOT STARTED';
    } else {
       
        $daysSinceStart = (new DateTime($startStr))->diff(new DateTime('today'))->days;
        $weeks  = min(4, max(1, $daysSinceStart / 7));
        $weekly = $p ? ((float)$p['hrs28'] / $weeks) : 0;

        if ($weekly <= 0) {
            $forecast = 'Not progressing';
        } else {
            $today     = new DateTime('today');
            $projected = (clone $today)->modify('+' . (int)ceil(($remaining / $weekly) * 7) . ' days');

            if ($row['deadline']) {
                $deadline = new DateTime($row['deadline']);
                $gapDays  = (int)$deadline->diff($projected)->format('%r%a');  
                $daysLeft = (int)$today->diff($deadline)->format('%r%a');
                $requiredPace = $daysLeft > 0 ? round($remaining / max(1, $daysLeft / 7), 1) : null;
                $forecast = $gapDays <= 0 ? 'ON TRACK' : ($gapDays <= 14 ? 'AT RISK' : 'BEHIND');
            } else {
                $forecast = 'ESTIMATED';
            }
        }
    }

    $data[] = [
        "studentID"          => $row['studentID'],
        "name"               => $row['name'] ?? 'Unknown Student',
        "course"             => $row['course'] ?? '—',
        "yearLevel"          => $row['yearLevel'] ?? '—',

        "absents"            => (int)  $row['absents'],
        "lates"              => (int)  $row['lates'],
        "presents"           => (int)  $row['presents'],
        "recent_absents"     => (int)  $row['recent_absents'],
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

        "remaining_hours"  => round($remaining, 1),
        "weekly_pace"      => round($weekly, 1),
        "projected_finish" => $projected ? $projected->format('Y-m-d') : null,
        "deadline"         => $row['deadline'],
        "gap_days"         => $gapDays,
        "required_pace"    => $requiredPace,
        "forecast"         => $forecast,
        "flagged"          => ($row['absents'] >= 2 || $row['lates'] >= 5 || $row['overdue_tasks'] >= 1 || $progressPercent < 50),
    ];
}

echo json_encode($data);
?>