<?php
require_once("../../auth/supervisor_auth.php");
require_once("../../Shared/kapstongConnection.php");

function e($val) {
    return htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8');
}

$attendanceID = $_POST['attendanceID'] ?? '';

$sql = "
    SELECT 
        attendance_logs.*,
        ojtstudent.name
    FROM attendance_logs
    LEFT JOIN ojtstudent 
        ON ojtstudent.studentID = attendance_logs.studentID
    WHERE attendance_logs.attendanceID = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $attendanceID);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {

    $timeIn = $row['first_time_in'] ? date('h:i A', strtotime($row['first_time_in'])) : '—';
    $timeOut = $row['final_time_out'] ? date('h:i A', strtotime($row['final_time_out'])) : 'Not clocked out';
    $logDate = date('F d, Y', strtotime($row['log_date']));

    $reasonToShow = !empty($row['emergency_reason']) 
    ? $row['emergency_reason'] 
    : (stripos($row['remarks'] ?? '', 'EMERGENCY TIME OUT') !== false 
        ? preg_replace('/^EMERGENCY TIME OUT:\s*/i', '', $row['remarks']) 
        : null);

?>

    <div class="excuse-profile-strip">
        <div class="excuse-strip-icon">
            <i class='bx bx-error-alt'></i>
        </div>
        <div class="excuse-strip-info">
            <span class="excuse-strip-name"><?= e($row['name']) ?></span>
            <span class="excuse-strip-meta"><?= e($row['studentID']) ?> • <?= e($logDate) ?></span>
        </div>
        <span class="status-badge emergency">EMERGENCY TIMEOUT</span>
    </div>

    <div class="excuse-time-grid">
        <div class="excuse-time-card">
            <span class="excuse-time-label"><i class='bx bx-log-in-circle'></i> Time In</span>
            <span class="excuse-time-value"><?= e($timeIn) ?></span>
        </div>
        <div class="excuse-time-card excuse-time-card-out">
            <span class="excuse-time-label"><i class='bx bx-log-out-circle'></i> Time Out</span>
            <span class="excuse-time-value"><?= e($timeOut) ?></span>
        </div>
    </div>

    <div class="info-card">
        <div class="card-body">
            <div class="excuse-reason-block">
                <label class="excuse-reason-label">Reason Given</label>
                <p class="excuse-reason-text">
                    <?= $reasonToShow ? e($reasonToShow) : 'No reason provided.' ?>
                </p>
            </div>
        </div>
    </div>

<?php
} else {
    echo "<p style='padding:20px;text-align:center;color:#9CA3AF;'>Record not found.</p>";
}
?>