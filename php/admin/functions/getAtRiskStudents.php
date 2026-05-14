<?php
require_once("../../kapstongConnection.php");
require_once("../../auth/admin_auth.php");

header('Content-Type: application/json');

$query = "
SELECT 
    u.studentID,
    o.name,

    -- attendance risk
    SUM(a.status = 'absent') AS absents,
    SUM(a.status = 'late') AS lates,

    -- task risk
    SUM(t.due_date < CURDATE() AND t.status != 'APPROVED') AS overdue_tasks,

    -- progress
    p.completed_hours,
    p.required_hours

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

WHERE u.role = 'student'

GROUP BY u.studentID, o.name, p.completed_hours, p.required_hours

HAVING 
    absents >= 2 
    OR lates >= 5 
    OR overdue_tasks >= 1 
    OR (p.completed_hours < (p.required_hours * 0.5))
";

$result = $conn->query($query);

$data = [];

while ($row = $result->fetch_assoc()) {

    $risk = "LOW";

    if ($row['absents'] >= 3 || $row['overdue_tasks'] >= 2) {
        $risk = "HIGH";
    } elseif ($row['absents'] >= 2 || $row['lates'] >= 5) {
        $risk = "MEDIUM";
    }

    $data[] = [
        "studentID" => $row['studentID'],
        "name" => $row['name'],
        "absents" => $row['absents'],
        "lates" => $row['lates'],
        "overdue_tasks" => $row['overdue_tasks'],
        "progress" => $row['completed_hours'] . "/" . $row['required_hours'],
        "risk" => $risk
    ];
}

echo json_encode($data);
?>