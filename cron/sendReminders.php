<?php
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}

require_once __DIR__ . '/src/Shared/config.php';  
require_once __DIR__ . '/src/Shared/functions.php';

function alreadySent($conn, $studentID, $type, $refID, $days)
{
    $sql = "SELECT 1 FROM warning_logs 
            WHERE studentID = ? AND warning_type = ? 
              AND (ref_id <=> ?) 
              AND sent_at > NOW() - INTERVAL ? DAY LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $studentID, $type, $refID, $days);
    $stmt->execute();
    return (bool) $stmt->get_result()->fetch_assoc();
}

function logSent($conn, $studentID, $type, $refID)
{
    $stmt = $conn->prepare("INSERT INTO warning_logs (studentID, warning_type, ref_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $studentID, $type, $refID);
    $stmt->execute();
}

function sendReminderEmail($to, $name, $subject, $body)
{
    // 
}

$res = $conn->query("
    SELECT t.taskID, t.title, t.due_date, s.studentID, s.name, s.email
    FROM student_tasks t
    INNER JOIN ojtstudent s ON s.studentID = t.studentID
    INNER JOIN student_supervisor ss ON ss.studentID = s.studentID AND ss.status = 'ACTIVE'
    WHERE t.status IN ('NOT STARTED', 'IN PROGRESS')
      AND t.due_date >= CURDATE()
      AND t.due_date < DATE_ADD(CURDATE(), INTERVAL 4 DAY)
");
while ($r = $res->fetch_assoc()) {
    if (alreadySent($conn, $r['studentID'], 'TASK_DUE_SOON', (int)$r['taskID'], 30)) continue;
    $ok = sendReminderEmail($r['email'], $r['name'],
        "Task due soon: " . $r['title'],
        "Hi {$r['name']}, your task '{$r['title']}' is due on " . date('M d, Y', strtotime($r['due_date'])) . ".");
    if ($ok) logSent($conn, $r['studentID'], 'TASK_DUE_SOON', (int)$r['taskID']);
}

$res = $conn->query("
    SELECT t.taskID, t.title, s.studentID, s.name, s.email
    FROM student_tasks t
    INNER JOIN ojtstudent s ON s.studentID = t.studentID
    INNER JOIN student_supervisor ss ON ss.studentID = s.studentID AND ss.status = 'ACTIVE'
    WHERE t.status IN ('NOT STARTED', 'IN PROGRESS')
      AND t.due_date < CURDATE()
      AND t.due_date >= DATE_SUB(CURDATE(), INTERVAL 2 DAY)
");
while ($r = $res->fetch_assoc()) {
    if (alreadySent($conn, $r['studentID'], 'OVERDUE_TASK', (int)$r['taskID'], 30)) continue;
    $ok = sendReminderEmail($r['email'], $r['name'],
        "Task overdue: " . $r['title'],
        "Hi {$r['name']}, your task '{$r['title']}' is now overdue. Please submit it as soon as you can.");
    if ($ok) logSent($conn, $r['studentID'], 'OVERDUE_TASK', (int)$r['taskID']);
}

$res = $conn->query("
    SELECT s.studentID, s.name, s.email,
           COALESCE(MAX(a.log_date), DATE(MAX(ss.date_assigned))) AS ref_date
    FROM ojtstudent s
    INNER JOIN student_supervisor ss ON ss.studentID = s.studentID AND ss.status = 'ACTIVE'
    LEFT JOIN attendance_logs a ON a.studentID = s.studentID
    GROUP BY s.studentID, s.name, s.email
    HAVING ref_date < DATE_SUB(CURDATE(), INTERVAL 5 DAY)
       AND ref_date >= DATE_SUB(CURDATE(), INTERVAL 26 DAY)
");
while ($r = $res->fetch_assoc()) {
    if (alreadySent($conn, $r['studentID'], 'INACTIVE_ATTENDANCE', null, 3)) continue;
    $ok = sendReminderEmail($r['email'], $r['name'],
        "We haven't seen you in a while",
        "Hi {$r['name']}, you have no attendance recorded in the last 5 days. Please coordinate with your supervisor.");
    if ($ok) logSent($conn, $r['studentID'], 'INACTIVE_ATTENDANCE', null);
}