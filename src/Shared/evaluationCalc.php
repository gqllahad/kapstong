<?php
function calcAttendanceScore($conn, $studentID) {
    $stmt = $conn->prepare("
        SELECT COUNT(*) t, SUM(status='present') p, SUM(status='late') l, SUM(status='excused') e
        FROM attendance_logs
        WHERE studentID = ? AND status NOT IN ('voided','incomplete')
    ");
    $stmt->bind_param("s", $studentID);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();

    $counted = ($r['t'] ?? 0) - ($r['e'] ?? 0);
    if ($counted <= 0) return 0;

    return min((($r['p'] + $r['l'] * 0.75) / $counted) * 100, 100);
}

function calcProgressScore($conn, $studentID) {
    $stmt = $conn->prepare("
        SELECT ojt_start_date, ojt_end_date
        FROM student_progress WHERE studentID = ? LIMIT 1
    ");
    $stmt->bind_param("s", $studentID);
    $stmt->execute();
    $p = $stmt->get_result()->fetch_assoc();

    if (empty($p['ojt_start_date']) || empty($p['ojt_end_date'])) return 0;

    $start = new DateTime($p['ojt_start_date']);
    $end   = min(new DateTime($p['ojt_end_date']), new DateTime('today'));
    $s = $start->format('Y-m-d');
    $e = $end->format('Y-m-d');

    $stmt = $conn->prepare("
        SELECT event_date, type FROM calendar_events
        WHERE event_date BETWEEN ? AND ?
        AND (
            studentID = ?
            OR (studentID = '' AND superID IN (
                0,
                IFNULL((SELECT superID FROM student_supervisor
                        WHERE studentID = ? AND status = 'ACTIVE' LIMIT 1), 0)
            ))
        )
        ORDER BY (studentID = '') DESC
    ");
    $stmt->bind_param("ssss", $s, $e, $studentID, $studentID);
    $stmt->execute();
    $res = $stmt->get_result();

    $events = [];
    while ($ev = $res->fetch_assoc()) $events[$ev['event_date']] = $ev['type'];

    $expected = [];
    for ($d = clone $start; $d <= $end; $d->modify('+1 day')) {
        $key = $d->format('Y-m-d');
        $isWork = ($d->format('N') != 7);
        if (isset($events[$key])) $isWork = ($events[$key] === 'WORKDAY');
        if ($isWork) $expected[$key] = true;
    }

    $stmt = $conn->prepare("
        SELECT log_date, status FROM attendance_logs
        WHERE studentID = ? AND log_date BETWEEN ? AND ?
    ");
    $stmt->bind_param("sss", $studentID, $s, $e);
    $stmt->execute();
    $res = $stmt->get_result();

    $rendered = [];
    $excused  = [];
    while ($l = $res->fetch_assoc()) {
        if (!isset($expected[$l['log_date']])) continue;
        if (in_array($l['status'], ['present', 'late'])) $rendered[$l['log_date']] = true;
        elseif ($l['status'] === 'excused') $excused[$l['log_date']] = true;
    }

    $today = date('Y-m-d');
    if (isset($expected[$today]) && !isset($rendered[$today])) unset($expected[$today]);

    $total = count($expected);
    if ($total <= 0) return 0;

    return min(count($rendered) / $total * 100, 100);
}


function calcFinalScores($conn, $studentID) {
    $attendance = calcAttendanceScore($conn, $studentID);
    $progress   = calcProgressScore($conn, $studentID);

    $stmt = $conn->prepare("SELECT status FROM student_tasks WHERE studentID = ?");
    $stmt->bind_param("s", $studentID);
    $stmt->execute();
    $res = $stmt->get_result();
    $map = ['APPROVED'=>100,'SUBMITTED'=>75,'IN PROGRESS'=>50,'NOT STARTED'=>20,'REJECTED'=>0];
    $n = 0; $sum = 0;
    while ($t = $res->fetch_assoc()) { $n++; $sum += $map[$t['status']] ?? 0; }
    $tasks = $n ? $sum / $n : 0;

    $w = $conn->query("SELECT attendance_weight, progress_weight, task_weight
                       FROM evaluation_settings WHERE superID IS NULL LIMIT 1")->fetch_assoc() ?? [];
    $aw = floatval($w['attendance_weight'] ?? 0.33);
    $pw = floatval($w['progress_weight'] ?? 0.33);
    $tw = floatval($w['task_weight'] ?? 0.33);
    $tot = $aw + $pw + $tw;
    if ($tot > 0) { $aw /= $tot; $pw /= $tot; $tw /= $tot; }

    return [
        'attendance' => round($attendance, 2),
        'progress'   => round($progress, 2),
        'tasks'      => round($tasks, 2),
        'final'      => round($attendance*$aw + $progress*$pw + $tasks*$tw, 2),
    ];
}