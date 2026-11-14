<?php
function loadPaceMap(mysqli $conn): array
{
    $pace = [];
    $res = $conn->query("SELECT studentID,
                                SUM(CASE WHEN log_date >= DATE_SUB(CURDATE(), INTERVAL 28 DAY)
                                         THEN total_hours ELSE 0 END) AS hrs28,
                                MIN(log_date) AS first_log
                         FROM attendance_logs
                         WHERE current_state <> 'VOIDED'
                         GROUP BY studentID");
    while ($r = $res->fetch_assoc()) {
        $pace[$r['studentID']] = $r;
    }
    return $pace;
}

function computeForecast(float $required, float $completed, ?string $start, ?string $deadline, ?array $pace): array
{
    $remaining = max(0, $required - $completed);
    $out = [
        'remaining' => $remaining, 'weekly' => 0, 'projected' => null,
        'gap_days' => null, 'required_pace' => null, 'forecast' => ''
    ];

    $start = $start ?: ($pace['first_log'] ?? null);

    if ($remaining <= 0) { $out['forecast'] = 'COMPLETED';   return $out; }
    if (!$start)         { $out['forecast'] = 'NOT STARTED'; return $out; }

    $today = new DateTime('today');
    $daysSinceStart = (new DateTime($start))->diff($today)->days;
    $weeks  = min(4, max(1, $daysSinceStart / 7));
    $weekly = $pace ? ((float)$pace['hrs28'] / $weeks) : 0;
    $out['weekly'] = $weekly;

    if ($weekly <= 0) { $out['forecast'] = 'STALLED'; return $out; }

    $projected = (clone $today)->modify('+' . (int)ceil(($remaining / $weekly) * 7) . ' days');
    $out['projected'] = $projected->format('Y-m-d');

    if ($deadline) {
        $dl       = new DateTime($deadline);
        $gap      = (int)$dl->diff($projected)->format('%r%a'); 
        $daysLeft = (int)$today->diff($dl)->format('%r%a');
        $out['gap_days']      = $gap;
        $out['required_pace'] = $daysLeft > 0 ? round($remaining / max(1, $daysLeft / 7), 1) : null;
        $out['forecast']      = $gap <= 0 ? 'ON TRACK' : ($gap <= 14 ? 'AT RISK' : 'BEHIND');
    } else {
        $out['forecast'] = 'ESTIMATED';
    }

    return $out;
}


function loadPaceFor(mysqli $conn, string $studentID): ?array
{
    $stmt = $conn->prepare("SELECT SUM(CASE WHEN log_date >= DATE_SUB(CURDATE(), INTERVAL 28 DAY)
                                            THEN total_hours ELSE 0 END) AS hrs28,
                                   MIN(log_date) AS first_log
                            FROM attendance_logs
                            WHERE studentID = ? AND status <> 'voided'");
    $stmt->bind_param("s", $studentID);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    return ($row && $row['first_log'] !== null) ? $row : null;
}

function getStudentForecast($conn, $studentID, $minDays = 14, $windowDays = 28, $graceDays = 7)
{
    $today = new DateTimeImmutable('today');

    $stmt = $conn->prepare("
        SELECT sp.required_hours,
               COALESCE(sp.completed_hours, 0) AS completed_hours,
               COALESCE(
                   o.end_date,
                   (SELECT end_date FROM ojt_settings WHERE status = 'ACTIVE' ORDER BY settingID DESC LIMIT 1)
               ) AS end_date
        FROM student_progress sp
        LEFT JOIN ojt_settings o ON o.settingID = sp.settingID
        WHERE sp.studentID = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $studentID);
    $stmt->execute();
    $p = $stmt->get_result()->fetch_assoc();
    if (!$p) return null;

    $required  = (float) $p['required_hours'];
    $completed = (float) $p['completed_hours'];
    $remaining = max(0, $required - $completed);
    $endDate   = $p['end_date'] ? new DateTimeImmutable($p['end_date']) : null;

    $base = [
        'completed' => $completed,
        'required'  => $required,
        'remaining' => $remaining,
        'end_date'  => $endDate ? $endDate->format('Y-m-d') : null,
        'projected_date' => null,
        'hours_per_week' => null,
        'hours_per_day'  => null,
        'needed_per_day' => null,
        'days_late'      => null,
    ];

    if ($remaining <= 0) return ['status' => 'COMPLETED'] + $base;

    $stmt = $conn->prepare("
        SELECT MIN(log_date) AS first_log
        FROM attendance_logs
        WHERE studentID = ? AND status <> 'voided'
    ");
    $stmt->bind_param("s", $studentID);
    $stmt->execute();
    $firstLog = $stmt->get_result()->fetch_assoc()['first_log'] ?? null;
    if (!$firstLog) return ['status' => 'NOT_ENOUGH_DATA'] + $base;

    $first       = new DateTimeImmutable($firstLog);
    $yesterday   = $today->modify('-1 day');
    $daysTracked = (int) $first->diff($yesterday)->format('%r%a') + 1;
    if ($daysTracked < $minDays) return ['status' => 'NOT_ENOUGH_DATA'] + $base;

    $windowStart = max($first, $today->modify("-{$windowDays} days"));

    $o = $conn->prepare("SELECT superID FROM student_supervisor WHERE studentID = ? AND status = 'ACTIVE' LIMIT 1");
    $o->bind_param("s", $studentID);
    $o->execute();
    $ownerID = (int) ($o->get_result()->fetch_column() ?: 0);

    $events = [];
    $ws = $windowStart->format('Y-m-d');
    $stmt = $conn->prepare("
        SELECT event_date, type FROM calendar_events
        WHERE event_date >= ? AND superID IN (?, 0) AND studentID IN (?, '')
        ORDER BY (studentID <> '') ASC, superID ASC
    ");
    $stmt->bind_param("sis", $ws, $ownerID, $studentID);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $events[$r['event_date']] = $r['type'];
    }

    $isWorkday = function (DateTimeImmutable $d) use ($events) {
        $key = $d->format('Y-m-d');
        if (isset($events[$key])) return $events[$key] !== 'NO_WORK';
        return (int) $d->format('N') <= 5;
    };

    $workdaysWindow = 0;
    for ($d = $windowStart; $d <= $yesterday; $d = $d->modify('+1 day')) {
        if ($isWorkday($d)) $workdaysWindow++;
    }
    if ($workdaysWindow === 0) return ['status' => 'NOT_ENOUGH_DATA'] + $base;

    $stmt = $conn->prepare("
        SELECT COALESCE(SUM(total_hours), 0) AS hrs
        FROM attendance_logs
        WHERE studentID = ? AND status <> 'voided'
          AND log_date BETWEEN ? AND ?
    ");
    $ye = $yesterday->format('Y-m-d');
    $stmt->bind_param("sss", $studentID, $ws, $ye);
    $stmt->execute();
    $hoursWindow = (float) $stmt->get_result()->fetch_assoc()['hrs'];

    $pace      = $hoursWindow / $workdaysWindow;
    $windowLen = (int) $windowStart->diff($yesterday)->format('%a') + 1;
    $base['hours_per_day']  = round($pace, 1);
    $base['hours_per_week'] = round($hoursWindow / ($windowLen / 7), 1);

    if ($endDate) {
        $daysLeft = 0;
        for ($d = $today->modify('+1 day'); $d <= $endDate; $d = $d->modify('+1 day')) {
            if ($isWorkday($d)) $daysLeft++;
        }
        $base['needed_per_day'] = $daysLeft > 0 ? round($remaining / $daysLeft, 1) : null;
    }

    if ($pace <= 0) return ['status' => 'STALLED'] + $base;

    $acc   = 0.0;
    $d     = $today->modify('+1 day');
    $guard = 0;
    while ($guard < 730) {
        if ($isWorkday($d)) $acc += $pace;
        if ($acc >= $remaining) break;
        $d = $d->modify('+1 day');
        $guard++;
    }
    if ($guard >= 730) return ['status' => 'TOO_SLOW'] + $base;

    $base['projected_date'] = $d->format('Y-m-d');

    if (!$endDate) return ['status' => 'NO_DEADLINE'] + $base;

    $daysLate = (int) $endDate->diff($d)->format('%r%a');
    $base['days_late'] = $daysLate;

    return ['status' => $daysLate > $graceDays ? 'AT_RISK' : 'ON_TRACK'] + $base;
}