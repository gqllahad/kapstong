<?php
function loadPaceMap(mysqli $conn): array
{
    $pace = [];
    $res = $conn->query("SELECT studentID,
                                SUM(CASE WHEN log_date >= DATE_SUB(CURDATE(), INTERVAL 28 DAY)
                                         THEN total_hours ELSE 0 END) AS hrs28,
                                MIN(log_date) AS first_log
                         FROM attendance_logs
                         WHERE status <> 'voided'
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