<?php


function logout()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();

    header("Location: ../php/loginPhase.php#log-container");
    exit;
}

// ipaddress
function getUserIP()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}


// checkstudent
function isStudentIDTaken($conn, $studentID)
{
    $checkStudent = $conn->prepare("SELECT COUNT(*) FROM users WHERE studentID = ?");
    $checkStudent->bind_param("s", $studentID);
    $checkStudent->execute();

    $count = 0;
    $checkStudent->bind_result($count);
    $checkStudent->fetch();
    $checkStudent->close();

    return $count > 0;
}

function isEmailTaken($conn, $studentEmail)
{
    $checkEmail = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $studentEmail);
    $checkEmail->execute();

    $count = 0;
    $checkEmail->bind_result($count);
    $checkEmail->fetch();
    $checkEmail->close();

    return $count > 0;
}

function getStudentDocuments($conn, $studentID)
{
    $stmt = $conn->prepare("SELECT idUpload, regFormUpload, status, profilePicture FROM student_documents WHERE studentID = ?");
    $stmt->bind_param("s", $studentID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

function getStudentInfo($conn, $studentID)
{
    $stmt = $conn->prepare("
        SELECT 
            s.studentID,
            s.email,
            s.name,
            s.semester,
            s.schoolYear,
            s.mobileNumber,
            s.course,
            p.prg_name AS course_name,
            p.prg_department AS course_dpt,
            s.gender,
            s.yearLevel,
            s.birthDate,
            s.address
        FROM ojtstudent s
        LEFT JOIN program p ON s.course = p.prg_acro
        WHERE s.studentID = ?
    ");
    $stmt->bind_param("s", $studentID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

function renderApprovalTable($conn, $type, $verifiedFilter, $search)
{
    $where = "WHERE users.role = '$type'";

    if (!empty($search)) {
        $where .= " AND (
        users.userID LIKE '%$search%' OR
        users.name LIKE '%$search%' OR
        users.email LIKE '%$search%'
    )";
    }

    $vf = strtoupper($verifiedFilter);
    if ($vf === 'PENDING') {
        $where .= " AND users.isVerified = 'PENDING'";
    } elseif ($vf === 'NOT VERIFIED') {
        $where .= " AND users.isVerified = 'NOT VERIFIED'";
    }

    $sql = "SELECT 
        users.studentID,
        users.name,
        users.email,
        users.isVerified,
        students.course,
        students.yearLevel
    FROM users users
    LEFT JOIN ojtstudent AS students
        ON users.studentID = students.studentID
    $where
    ORDER BY users.dateCreated DESC";

    $result = $conn->query($sql);

    $output = '';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $status = $row['isVerified'];

            $output .= '
            <tr>
                <td>' . $row['studentID'] . '</td>
                <td>' . $row['name'] . '</td>
                <td>' . $row['email'] . '</td>
                <td>' . ($row['course'] ?? '-') . '</td>
                <td>' . ($row['yearLevel'] ?? '-') . '</td>
                <td>
                    <span class="status ' . strtolower(str_replace(' ', '-', $status)) . '">' . $status . '</span>
                </td>
                <td>';

            if ($status !== 'VERIFIED') {
                $output .= '
                             <button class="view-btn" onclick="viewUser(\'' . $row['studentID'] . '\', \'main\')">View</button>
                            <button class="approve-btn" onclick="approveUser(\'' . $row['studentID'] . '\')">Approve</button>
                            <button class="reject-btn" onclick="rejectUser(\'' . $row['studentID'] . '\')">Reject</button>';
            } else {
                $output .= '<span style="color:lime;">✔</span>';
            }
        }
    } else {
        $output .= '<tr><td colspan="7" style="text-align:center;padding:15px;font-weight:500;">No records found</td></tr>';
    }

    $output .= '</tbody></table>';

    return $output;
}

function renderStudentTable($conn, $type, $verifiedFilter, $search)
{
    $where = "WHERE users.role = '$type'";

    if (!empty($search)) {
        $where .= " AND (
        users.userID LIKE '%$search%' OR
        users.name LIKE '%$search%' OR
        users.email LIKE '%$search%'
    )";
    }

    $vf = strtoupper($verifiedFilter);
    if ($vf === 'VERIFIED') {
        $where .= " AND users.isVerified = 'VERIFIED'";
    } elseif ($vf === 'NOT VERIFIED' || $vf === 'NOTVERIFIED') {
        $where .= " AND users.isVerified = 'NOT VERIFIED'";
    }

    $sql = "SELECT 
        users.studentID,
        users.name,
        users.email,
        users.isVerified,
        students.course,
        students.yearLevel
    FROM users users
    LEFT JOIN ojtstudent AS students
        ON users.studentID = students.studentID
    $where
    ORDER BY users.dateCreated DESC";

    $result = $conn->query($sql);

    $output = '';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $status = $row['isVerified'];

            $output .= '
            <tr>
                <td>' . $row['studentID'] . '</td>
                <td>' . $row['name'] . '</td>
                <td>' . $row['email'] . '</td>
                <td>' . ($row['course'] ?? '-') . '</td>
                <td>' . ($row['yearLevel'] ?? '-') . '</td>
                <td>
                    <span class="status ' . strtolower(str_replace(' ', '-', $status)) . '">' . $status . '</span>
                </td>
                <td>';

            if ($status === 'VERIFIED') {
                $output .= '
                             <button class="view-btn" onclick="viewUser(' . $row['studentID'] . ', \'allStudent\')">View</button>';
            } else {
                $output .= '<button class="view-btn" onclick="viewUser(' . $row['studentID'] . ', \'UnverifiedStudent\')">View</button>';
            }
        }
    } else {
        $output .= '<tr><td colspan="7" style="text-align:center;padding:15px;font-weight:500;">No records found</td></tr>';
    }

    $output .= '</tbody></table>';

    return $output;
}

// supervisor table
function renderSupervisorTable($conn, $search = '')
{
    $where = "WHERE 1=1";

    if (!empty($search)) {
        $where .= " AND (
            supervisor.superID LIKE '%$search%' OR
            supervisor.name LIKE '%$search%' OR
            supervisor.email LIKE '%$search%' OR
            supervisor.department LIKE '%$search%'
        )";
    }

    $sql = "SELECT 
                supervisor.superID,
                supervisor.name,
                supervisor.email,
                supervisor.number,
                supervisor.department,
                supervisor.date_created
            FROM supervisor
            $where
            ORDER BY supervisor.date_created DESC";

    $result = $conn->query($sql);

    $output = '';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $output .= '
            <tr>
                <td>' . $row['superID'] . '</td>
                <td>' . $row['name'] . '</td>
                <td>' . $row['email'] . '</td>
                <td>' . $row['number'] . '</td>
                <td>' . $row['department'] . '</td>
                <td>' . date("M d, Y h:i A", strtotime($row['date_created'])) . '</td>
                <td>
                    <button class="view-btn" onclick="viewSupervisor(' . $row['superID'] . ')">View</button>
                </td>
            </tr>';
        }
    } else {
        $output .= '
        <tr>
            <td colspan="8" style="text-align:center;padding:15px;font-weight:500;">
                No supervisors found
            </td>
        </tr>';
    }

    $output .= '</tbody></table>';

    return $output;
}

function renderSupervisorAssignedStudents($conn, $superID)
{
    $stmt = $conn->prepare("
        SELECT 
            ojtstudent.studentID,
            ojtstudent.name,
            ojtstudent.course,
            ojtstudent.yearLevel
        FROM student_supervisor
        INNER JOIN ojtstudent 
            ON student_supervisor.studentID = ojtstudent.studentID
        WHERE student_supervisor.superID = ?
        AND student_supervisor.status = 'ACTIVE'
        ORDER BY ojtstudent.name ASC
    ");

    $stmt->bind_param("i", $superID);
    $stmt->execute();
    $result = $stmt->get_result();

    $output = '';

    if ($result->num_rows > 0) {

        $output .= "<div style='padding:10px;'>";

        while ($row = $result->fetch_assoc()) {
            $output .= '
                    <div class="assigned-student-row">
                        
                        <div class="student-info">
                            <strong>' . $row['name'] . '</strong><br>
                            <small>
                                ' . $row['studentID'] . ' • 
                                ' . $row['course'] . ' • 
                                ' . $row['yearLevel'] . '
                            </small>
                        </div>

                        <div class="student-action">
                            <button class="view-btn" onclick="viewUser(\'' . $row['studentID'] . '\', \'supervisorView\')">
                                View
                            </button>
                            <button class="view-btn" onclick="reAssignUser(\'' . $row['studentID'] . '\', \'supervisorView\')">
                                Reassign
                            </button>
                        </div>

                    </div>';
        }



        $output .= "</div>";
    } else {
        $output = "<p style='padding:10px;color:#6b7280;'>No assigned students yet.</p>";
    }

    return $output;
}

// pending approval reports(student)
function renderApprovalReportList($conn, $superID, $search = '')
{
    $where = "
        WHERE 
            student_tasks.superID = '$superID'
            AND student_tasks.status = 'SUBMITTED'
    ";

    if (!empty($search)) {
        $where .= " AND (
            student_tasks.studentID LIKE '%$search%' OR
            ojtstudent.name LIKE '%$search%' OR
            student_tasks.title LIKE '%$search%'
        )";
    }

    $sql = "
        SELECT 
            student_tasks.taskID,
            student_tasks.studentID,
            ojtstudent.name,
            student_tasks.title,
            student_tasks.status,
            student_tasks.date_created
        FROM student_tasks
        INNER JOIN ojtstudent 
            ON student_tasks.studentID = ojtstudent.studentID
        $where
        ORDER BY student_tasks.date_created DESC
    ";

    $result = $conn->query($sql);

    $output = '';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $output .= '
            <tr>
                <td>
            <div class="student-id-cell">
                #' . $row['studentID'] . '
            </div>
        </td>

        <td>
            <div class="student-name-cell">
                <div class="student-avatar">
                    ' . strtoupper(substr($row['name'], 0, 1)) . '
                </div>

                <span>' . $row['name'] . '</span>
            </div>
        </td>

                <td>
            <div class="task-title-cell">
                <span class="task-title">
                    ' . $row['title'] . '
                </span>

                <small class="task-subtitle">
                    Awaiting supervisor review
                </small>
            </div>
        </td>
                <td>
                    <span class="status-pill submitted">
                        ' . $row['status'] . '
                    </span>
                </td>
                <td>
            <div class="date-cell">
                ' . date("M d, Y", strtotime($row['date_created'])) . '
                <small>
                    ' . date("h:i A", strtotime($row['date_created'])) . '
                </small>
            </div>
        </td>
                <td>
                    <button 
                        class="view-btn" 
                        onclick="previewTask(' . $row['taskID'] . ')">
                        Preview
                    </button>
                </td>
            </tr>';
        }
    } else {
        $output .= '
        <tr>
            <td colspan="6" style="text-align:center;padding:15px;font-weight:500;">
                No pending approval reports found
            </td>
        </tr>';
    }

    $output .= '</tbody></table>';

    return $output;
}

// task management (supervisor)
function renderTaskManagementList($conn, $superID, $search = '', $status = '', $deadline = '')
{
    $where = "
        WHERE student_tasks.superID = '$superID'
    ";

    if (!empty($search)) {
        $where .= " AND (
            student_tasks.studentID LIKE '%$search%' OR
            ojtstudent.name LIKE '%$search%' OR
            student_tasks.title LIKE '%$search%'
        )";
    }

    if (!empty($deadline)) {
        $where .= " AND student_tasks.due_date = '$deadline'";
    }

    if (!empty($status)) {
        $where .= " AND student_tasks.status = '$status'";
    }

    $sql = "
        SELECT 
            student_tasks.taskID,
            student_tasks.studentID,
            ojtstudent.name,
            student_tasks.title,
            student_tasks.description,
            student_tasks.due_date,
            student_tasks.status,
            student_tasks.date_created
        FROM student_tasks
        INNER JOIN ojtstudent 
            ON student_tasks.studentID = ojtstudent.studentID
        $where
        ORDER BY student_tasks.date_created DESC
    ";

    $result = $conn->query($sql);

    $output = '';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $status = $row['status'];

            switch ($status) {
                case 'NOT STARTED':
                    $color = '#6B7280';
                    break;
                case 'IN PROGRESS':
                    $color = '#2563EB';
                    break;
                case 'SUBMITTED':
                    $color = '#F59E0B';
                    break;
                case 'APPROVED':
                    $color = '#059669';
                    break;
                case 'REJECTED':
                    $color = '#DC2626';
                    break;
                default:
                    $color = '#9CA3AF';
            }

            $actionButtons = '';

            if ($status === 'APPROVED' || $status === 'REJECTED') {

                $actionButtons = '
                <button class="view-btn" onclick="viewTask(' . $row['taskID'] . ')">
                    View
                </button>
            ';
            } else {

                $actionButtons = '
                <button class="edit-btn" onclick="editTask(' . $row['taskID'] . ')">
                    Edit
                </button>

                <button class="delete-btn" onclick="deleteTask(' . $row['taskID'] . ')">
                    Delete
                </button>
            ';
            }

            $output .= '
            <tr>
                <td>
                    <div class="task-title-cell">
                        <span class="task-title">
                            ' . $row['title'] . '
                        </span>

                        <small class="task-subtitle">
                             ' . $row['description'] . '
                        </small>
                    </div>
                </td>
                <td>
                    <div class="student-name-cell">
                        <div class="student-avatar">
                            ' . strtoupper(substr($row['name'], 0, 1)) . '
                        </div>

                        <span>' . $row['name'] . '</span>
                    </div>
                </td>
                <td>' . date("M d, Y", strtotime($row['due_date'])) . '</td>
                <td>
                    <span class="status-pill" style="
                        background: ' . $color . ';
                        border: 1px solid ' . $color . ';
                    ">
                        ' . $status . '
                    </span>
                </td>
                <td style="display:flex;flex-direction:row;gap:10px;">
                    
               ' . $actionButtons . '
                </td>
            </tr>';
        }
    } else {
        $output .= '
        <tr>
            <td colspan="5" style="text-align:center;padding:15px;font-weight:500;">
                No tasks found
            </td>
        </tr>';
    }

    return $output;
}

// student process(supervisor)
function renderStudentProgressList($conn, $superID, $search = '')
{
    $where = "
        WHERE 
            student_supervisor.superID = '$superID'
            AND student_supervisor.status = 'ACTIVE'
    ";

    if (!empty($search)) {
        $where .= " AND (
            student_supervisor.studentID LIKE '%$search%' OR
            ojtstudent.name LIKE '%$search%'
        )";
    }

    $sql = "
        SELECT 
            student_supervisor.assignmentID,
            student_supervisor.studentID,
            ojtstudent.name,
            student_progress.completed_hours,
            student_progress.required_hours,
            student_progress.remaining_hours,
            student_progress.completion_status,
            student_progress.last_updated
        FROM student_supervisor

        INNER JOIN ojtstudent 
            ON student_supervisor.studentID = ojtstudent.studentID

        LEFT JOIN student_progress 
            ON student_supervisor.studentID = student_progress.studentID

        $where

        ORDER BY ojtstudent.name ASC
    ";

    $result = $conn->query($sql);

    $output = '';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $completedHours = $row['completed_hours'] ?? 0;
            $requiredHours = $row['required_hours'] ?? 500;
            $remainingHours = $row['remaining_hours'] ?? ($requiredHours - $completedHours);
            $status = $row['completion_status'] ?? 'ONGOING';

            switch ($status) {
                case 'ONGOING':
                    $statusColor = '#2563EB';
                    break;
                case 'COMPLETED':
                    $statusColor = '#059669';
                    break;
                default:
                    $statusColor = '#9CA3AF';
            }

            $output .= '
            <tr>
                <td>
                    <div class="student-name-cell">
                        <div class="student-avatar">
                            ' . strtoupper(substr($row['name'], 0, 1)) . '
                        </div>

                        <span>' . $row['name'] . '</span>
                    </div>
                </td>
                <td>' . $completedHours . '</td>
                <td>' . $requiredHours . '</td> 
                <td>' . $remainingHours . '</td>
                <td> 
                <span class="status-pill" style="
                        background: ' . $statusColor . ';
                        border: 1px solid ' . $statusColor . ';
                    ">
                        ' . $status . '
                    </span></td>
                <td>
                    <button 
                        class="view-btn" 
                        onclick="viewStudentProgress(\'' . $row['studentID'] . '\')">
                        View
                    </button>
                </td>
            </tr>';
        }
    } else {
        $output .= '
        <tr>
            <td colspan="6" style="text-align:center;padding:15px;font-weight:500;">
                No assigned students found
            </td>
        </tr>';
    }

    return $output;
}

// evaluations (sueprvisor)

function convertRatingToScore($rating)
{
    switch (strtoupper($rating)) {

        case 'A+': return 100;
        case 'A':  return 95;
        case 'A-': return 90;

        case 'B+': return 85;
        case 'B':  return 80;
        case 'B-': return 75;

        case 'C+': return 70;
        case 'C':  return 65;
        case 'C-': return 60;

        case 'D':  return 50;
        case 'F':  return 0;

        default:   return 0;
    }
}

function renderEvaluationList($conn, $superID, $search = '')
{
    $where = "
        WHERE ss.superID = '$superID'
        AND ss.status = 'ACTIVE'
    ";

    if (!empty($search)) {
        $where .= " AND (
            u.studentID LIKE '%$search%' OR
            u.name LIKE '%$search%'
        )";
    }

    $sql = "
        SELECT 
            u.studentID,
            u.name
        FROM student_supervisor ss
        INNER JOIN users u 
            ON ss.studentID = u.studentID
        $where
        ORDER BY u.name ASC
    ";

    $result = $conn->query($sql);

    if (!$result) {
        return "<tr><td colspan='8'>SQL Error: " . $conn->error . "</td></tr>";
    }

    $output = '';

    while ($row = $result->fetch_assoc()) {

        $studentID = $row['studentID'];

        $progressData = $conn->query("
            SELECT required_hours, completed_hours
            FROM student_progress
            WHERE studentID = '$studentID'
            LIMIT 1
        ")->fetch_assoc();

        $requiredHours = floatval($progressData['required_hours'] ?? 0);
        $completedHours = floatval($progressData['completed_hours'] ?? 0);

        if ($requiredHours <= 0) {
            $attendanceScore = 0;
        } else {
            $attendanceScore = min(
                ($completedHours / $requiredHours) * 100,
                100
            );
        }

        $stageResult = $conn->query("
            SELECT status
            FROM internship_stages
            WHERE studentID = '$studentID'
        ");

        $totalStages = 0;
        $completedStages = 0;

        while ($s = $stageResult->fetch_assoc()) {

            $totalStages++;

            if ($s['status'] === 'COMPLETED') {
                $completedStages++;
            }
        }

        $progressScore = ($totalStages > 0)
            ? ($completedStages / $totalStages) * 100
            : 0;

        $taskResult = $conn->query("
            SELECT status, rating
            FROM student_tasks
            WHERE studentID = '$studentID'
        ");

        $taskTotal = 0;
        $taskSum = 0;

        while ($t = $taskResult->fetch_assoc()) {

            $taskTotal++;

            switch ($t['status']) {

                case 'APPROVED':
                    $statusScore = 90;
                    break;

                case 'SUBMITTED':
                    $statusScore = 75;
                    break;

                case 'IN PROGRESS':
                    $statusScore = 50;
                    break;

                case 'NOT STARTED':
                    $statusScore = 20;
                    break;

                case 'REJECTED':
                    $statusScore = 0;
                    break;

                default:
                    $statusScore = 0;
            }

            $ratingScore = convertRatingToScore($t['rating'] ?? '');

            $taskSum += ($statusScore * 0.6) + ($ratingScore * 0.4);
        }

        $taskScore = ($taskTotal > 0)
            ? ($taskSum / $taskTotal)
            : 0;

        $weightData = $conn->query("
            SELECT attendance_weight, progress_weight, task_weight
            FROM evaluation_settings
            WHERE superID IS NULL
            LIMIT 1
        ");

        $weightRow = $weightData->fetch_assoc() ?? [];

        $attendanceW = floatval($weightRow['attendance_weight'] ?? 0.33);
        $progressW    = floatval($weightRow['progress_weight'] ?? 0.33);
        $taskW        = floatval($weightRow['task_weight'] ?? 0.33);

        $totalW = $attendanceW + $progressW + $taskW;

        if ($totalW > 0) {
            $attendanceW /= $totalW;
            $progressW    /= $totalW;
            $taskW        /= $totalW;
        }

        $finalGrade =
            ($attendanceScore * $attendanceW) +
            ($progressScore * $progressW) +
            ($taskScore * $taskW);

        $remarkColor = '#DC2626';

        if ($finalGrade >= 90) {
            $remarks = "EXCELLENT";
            $remarkColor = "#059669";
        } else if ($finalGrade >= 80) {
            $remarks = "VERY GOOD";
            $remarkColor = "#2563EB";
        } else if ($finalGrade >= 70) {
            $remarks = "SATISFACTORY";
            $remarkColor = "#F59E0B";
        } else if ($finalGrade >= 60) {
            $remarks = "NEEDS IMPROVEMENT";
            $remarkColor = "#F97316";
        } else {
            $remarks = "FAILED";
            $remarkColor = '#DC2626';
        }


        $gradeColor = '#DC2626';

        if ($finalGrade >= 90) $gradeColor = '#059669';
        else if ($finalGrade >= 80) $gradeColor = '#2563EB';
        else if ($finalGrade >= 70) $gradeColor = '#F59E0B';

        $output .= '
        <tr>
            <td>
                    <div class="student-name-cell">
                        <div class="student-avatar">
                            ' . strtoupper(substr($row['name'], 0, 1)) . '
                        </div>

                        <span>' . $row['name'] . '</span>
                    </div>
            </td>

            <td>' . round($attendanceScore, 2) . '%</td>
            <td>' . round($progressScore, 2) . '%</td>
            <td>' . round($taskScore, 2) . '%</td>

            <td style="color:' . $gradeColor . '; font-weight:600;">
                ' . round($finalGrade, 2) . '%
            </td>

            <td style="color: ' . $remarkColor . '; font-weight:600;">' . $remarks . '</td>

            <td>' . date("M d, Y") . '</td>

            <td>
                <button class="view-btn"
                    onclick="viewEvaluationBreakdown(\'' . $studentID . '\')">
                    View
                </button>
            </td>
        </tr>';
    }

    if ($output === '') {
        $output = '
        <tr>
            <td colspan="8" style="text-align:center;padding:15px;">
                No students found
            </td>
        </tr>';
    }

    return $output;
}

// assign student-supervisor
function renderAssignStudentList($conn, $search = '')
{
    $where = "WHERE users.role = 'student'
              AND users.isVerified = 'VERIFIED'
              AND ss.studentID IS NULL";

    if (!empty($search)) {
        $where .= " AND (
            users.studentID LIKE '%$search%' OR
            users.name LIKE '%$search%' OR
            users.email LIKE '%$search%'
        )";
    }

    $sql = "SELECT 
        users.userID,
        users.studentID,
        users.name,
        users.email,
        students.course,
        students.yearLevel
    FROM users
    LEFT JOIN ojtstudent AS students
        ON users.studentID = students.studentID

        LEFT JOIN student_supervisor ss
        ON users.studentID = ss.studentID
        AND ss.status = 'ACTIVE'
    $where
    ORDER BY users.dateCreated DESC";

    $result = $conn->query($sql);

    $output = '';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $output .= '
            <div class="list-item student-item"
                data-id="' . $row['studentID'] . '"
                data-user="' . $row['userID'] . '">

                <strong>' . $row['name'] . '</strong>
                <span>' . $row['studentID'] . ' • ' . ($row['course'] ?? '-') . ' • ' . ($row['yearLevel'] ?? '-') . '</span>

            </div>';
        }
    } else {
        $output .= '<div class="list-item">No students found</div>';
    }

    return $output;
}

// assigning task to student (supervisor)
function renderTaskAssignStudentList($conn, $superID, $search = '')
{
    $where = "
        WHERE users.role = 'student'
        AND users.isVerified = 'VERIFIED'
        AND ss.superID = '$superID'
        AND ss.status = 'ACTIVE'
    ";

    if (!empty($search)) {
        $where .= " AND (
            users.studentID LIKE '%$search%' OR
            users.name LIKE '%$search%' OR 
            ojtstudent.course LIKE '%$search%' OR
            ojtstudent.yearLevel LIKE '%$search%'
        )";
    }

    $sql = "
        SELECT 
            users.studentID,
            users.name,
            users.email,
            ojtstudent.course,
            ojtstudent.yearLevel
        FROM users

        INNER JOIN student_supervisor ss
            ON users.studentID = ss.studentID

        LEFT JOIN ojtstudent
            ON users.studentID = ojtstudent.studentID

        $where

        ORDER BY users.name ASC
    ";

    $result = $conn->query($sql);

    $output = '';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $course = $row['course'] ?? 'No Course';
            $yearLevel = $row['yearLevel'] ?? '-';

            $output .= '
            <div class="task-student-item"
                data-id="' . $row['studentID'] . '">

                <div class="student-name">
                    ' . $row['name'] . '
                </div>

                <div class="student-details">
                    ' . $row['studentID'] . ' • ' . $course . ' • ' . $yearLevel . '
                </div>

            </div>';
        }
    } else {
        $output .= '
        <div class="empty-state">
            No students found
        </div>';
    }

    return $output;
}

function renderStudentMainAttendance($conn, $superID, $search = '', $status = '' ,$dateFromAttendance = '', $dateToAttendance = '')
{
     $sql = "
        SELECT 
            attendance_logs.attendanceID,
            attendance_logs.log_date,
            attendance_logs.first_time_in,
            attendance_logs.final_time_out,
            attendance_logs.status,
            attendance_logs.total_hours,
            attendance_logs.remarks,
            attendance_logs.rfid_uid,
            attendance_logs.studentID,
            attendance_logs.created_at,
            ojtstudent.name
        FROM attendance_logs

        LEFT JOIN student_supervisor 
            ON student_supervisor.studentID = attendance_logs.studentID

        LEFT JOIN ojtstudent 
            ON ojtstudent.studentID = attendance_logs.studentID

        WHERE student_supervisor.superID = ?
        AND student_supervisor.status = 'ACTIVE'
    ";

    $params = [];
    $types = "i";
    $params[] = $superID;

    if (!empty($search)) {
        $sql .= " AND (
            attendance_logs.studentID LIKE ? OR
            ojtstudent.name LIKE ? OR
            attendance_logs.rfid_uid LIKE ?
        )";

        $like = "%$search%";
        $types .= "sss";
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    if (!empty($status)) {
        $sql .= " AND attendance_logs.status = ?";
        $types .= "s";
        $params[] = $status;
    }

    if (!empty($dateFromAttendance)) {
        $sql .= " AND attendance_logs.log_date >= ?";
        $types .= "s";
        $params[] = $dateFromAttendance;
    }

    if (!empty($dateToAttendance)) {
        $sql .= " AND attendance_logs.log_date >= ?";
        $types .= "s";
        $params[] = $dateToAttendance;
    }

    $sql .= " ORDER BY attendance_logs.log_date DESC, attendance_logs.first_time_in DESC";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    $result = $stmt->get_result();

    $output = '';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $status = strtolower($row['status']);

            switch($status) {
                case 'present':
                    $color = '#059669';
                    break;
                case 'late':
                    $color = '#F59E0B';
                    break;
                case 'absent':
                    $color =  '#EF4444';
                    break;
                case 'excused':
                    $color ='#3B82F6';
                    break;
                default: 
                    $color ='#9CA3AF';
                    break;
            };

            $output .= "
            <tr>
                <td><div class='student-id-cell'>
                    #" . $row['rfid_uid'] . "
                    </div>
                </td>
                <td>" . date('F d, Y', strtotime($row['log_date'])) . "</td>
                <td>" . date('h:i A', strtotime($row['first_time_in'])) . "</td>
                <td>" . date('h:i A', strtotime($row['final_time_out'])) . "</td>
                <td>
                    <span class='status-pill' style='background: " . $color . ";border: 1px solid " . $color . ";'>
                        " . $row['status'] . "
                    </span>
                </td>
                <td>{$row['total_hours']}</td>
                <td>{$row['remarks']}</td>
                <td>
                    <button class='view-btn'
                        onclick=\"viewAttendance('{$row['attendanceID']}')\">
                        View
                    </button>
                </td>
            </tr>";
        }
    } else {
        $output .= "
        <tr>
            <td colspan='8' style='text-align:center;padding:15px'>
                No attendance records found
            </td>
        </tr>";
    }

    return $output;
}

function renderAssignSupervisorList($conn, $search = '')
{
    $where = "WHERE 1=1";

    if (!empty($search)) {
        $where .= " AND (
            supervisor.superID LIKE '%$search%' OR
            supervisor.name LIKE '%$search%' OR
            supervisor.email LIKE '%$search%' OR
            supervisor.department LIKE '%$search%'
        )";
    }

    $sql = "SELECT 
                supervisor.superID,
                supervisor.name,
                supervisor.email,
                supervisor.number,
                supervisor.department
            FROM supervisor
            $where
            ORDER BY supervisor.date_created DESC";

    $result = $conn->query($sql);

    $output = '';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $output .= '
            <div class="list-item supervisor-item"
                data-id="' . $row['superID'] . '">

                <strong>' . $row['name'] . '</strong>
                <span>' . $row['superID'] . ' • ' . $row['department'] . '</span>

            </div>';
        }
    } else {
        $output .= '<div class="list-item">No supervisors found</div>';
    }

    return $output;
}

// supervisor activity log view
function renderSupervisorActivityLogTable($conn, $superID, $search = '', $module = '', $dateFrom = '', $dateTo = '')
{
    $sql = "
        SELECT 
            activity_log.logID,
            activity_log.role,
            activity_log.action,
            activity_log.module,
            activity_log.description,
            activity_log.target_type,
            activity_log.target_id,
            activity_log.ip_address,
            activity_log.created_at,
            users.name,
            users.email
        FROM activity_log
        INNER JOIN student_supervisor ss
            ON ss.studentID = activity_log.target_id
        LEFT JOIN users
            ON activity_log.userID = users.userID
        WHERE ss.superID = ?
    ";

    $params = [];
    $types = "i";
    $params[] = $superID;

    if (!empty($search)) {
        $sql .= " AND (
            users.name LIKE ? OR
            activity_log.action LIKE ? OR
            activity_log.module LIKE ? OR
            activity_log.target_id LIKE ?
        )";

        $like = "%$search%";
        $types .= "ssss";

        array_push($params, $like, $like, $like, $like);
    }

    if (!empty($module)) {
        $sql .= " AND activity_log.module = ?";
        $types .= "s";
        $params[] = $module;
    }

    if (!empty($dateFrom)) {
        $sql .= " AND DATE(activity_log.created_at) >= ?";
        $types .= "s";
        $params[] = $dateFrom;
    }

    if (!empty($dateTo)) {
        $sql .= " AND DATE(activity_log.created_at) <= ?";
        $types .= "s";
        $params[] = $dateTo;
    }

    $sql .= " ORDER BY activity_log.created_at DESC";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param($types, ...$params);

    $stmt->execute();
    $result = $stmt->get_result();

    $output = '';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $role = strtoupper($row['role']);

            $roleColor = match($role) {
                'STUDENT' => '#3B82F6',
                'SUPERVISOR' => '#F59E0B',
                'ADMIN' => '#374151',
                default => '#9CA3AF'
            };

            $output .= "
            <tr>
                <td>{$row['name']}</td>
                <td style='color:$roleColor;font-weight:600'>$role</td>
                <td>{$row['action']}</td>
                <td>{$row['module']}</td>
                <td>{$row['target_type']}</td>
                <td>{$row['target_id']}</td>
                <td>{$row['ip_address']}</td>
                <td>" . date("M d, Y h:i A", strtotime($row['created_at'])) . "</td>
            </tr>";
        }
    } else {
        $output .= "
        <tr>
            <td colspan='8' style='text-align:center;'>No supervisor activity found</td>
        </tr>";
    }

    return $output;
}



// admin activity_log view
function renderActivityLogTable($conn, $search = '', $module = '', $dateFrom = '', $dateTo = '')
{
    $sql = "
        SELECT 
            activity_log.logID,
            activity_log.role,
            activity_log.action,
            activity_log.module,
            activity_log.description,
            activity_log.target_type,
            activity_log.target_id,
            activity_log.ip_address,
            activity_log.created_at,
            users.name,
            users.email
        FROM activity_log
        LEFT JOIN users 
            ON activity_log.userID = users.userID
        WHERE 1=1
    ";

    $params = [];
    $types = "";

    if (!empty($search)) {
        $sql .= " AND (
            users.name LIKE ? OR
            users.email LIKE ? OR
            activity_log.action LIKE ? OR
            activity_log.module LIKE ? OR
            activity_log.target_id LIKE ?
        )";

        $like = "%$search%";
        $types .= "sssss";
        array_push($params, $like, $like, $like, $like, $like);
    }

    if (!empty($module)) {
        $sql .= " AND activity_log.module = ?";
        $types .= "s";
        $params[] = $module;
    }

    if (!empty($dateFrom)) {
        $sql .= " AND DATE(activity_log.created_at) >= ?";
        $types .= "s";
        $params[] = $dateFrom;
    }

    if (!empty($dateTo)) {
        $sql .= " AND DATE(activity_log.created_at) <= ?";
        $types .= "s";
        $params[] = $dateTo;
    }

    $sql .= " ORDER BY activity_log.created_at DESC";

    $stmt = $conn->prepare($sql);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $output = '';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $role = strtoupper($row['role']);

            $roleColor = match ($role) {
                'STUDENT' => '#3B82F6',
                'SUPERVISOR' => '#F59E0B',
                'ADMIN' => '#374151',
                default => '#9CA3AF'
            };

            $output .= "
            <tr>
                <td>{$row['name']}</td>
                <td style='color:$roleColor;font-weight:600'>$role</td>
                <td>{$row['action']}</td>
                <td>{$row['module']}</td>
                <td>{$row['target_type']}</td>
                <td>{$row['target_id']}</td>
                <td>{$row['ip_address']}</td>
                <td>" . date("M d, Y h:i A", strtotime($row['created_at'])) . "</td>
            </tr>";
        }
    } else {
        $output .= "
        <tr>
            <td colspan='8' style='text-align:center; padding:15px;'>
                No activity logs found
            </td>
        </tr>";
    }

    return $output;
}
// function renderActivityLogTable($conn, $search = '', $module = '')
// {
//     $sql = "
//         SELECT 
//             activity_log.logID,
//             activity_log.role,
//             activity_log.action,
//             activity_log.module,
//             activity_log.description,
//             activity_log.target_type,
//             activity_log.target_id,
//             activity_log.ip_address,
//             activity_log.created_at,
//             users.name,
//             users.email
//         FROM activity_log
//         LEFT JOIN users 
//             ON activity_log.userID = users.userID
//         WHERE 1=1
//     ";

//     $params = [];
//     $types = "";

//     if (!empty($search)) {
//         $sql .= " AND (
//             users.name LIKE ? OR
//             users.email LIKE ? OR
//             activity_log.action LIKE ? OR
//             activity_log.module LIKE ? OR
//             activity_log.target_id LIKE ?
//         )";

//         $like = "%$search%";
//         $types .= "sssss";
//         array_push($params, $like, $like, $like, $like, $like);
//     }

//     if (!empty($module)) {
//         $sql .= " AND activity_log.module = ?";
//         $types .= "s";
//         $params[] = $module;
//     }

//     $sql .= " ORDER BY activity_log.created_at DESC";

//     $stmt = $conn->prepare($sql);

//     if (!empty($params)) {
//         $stmt->bind_param($types, ...$params);
//     }

//     $stmt->execute();
//     $result = $stmt->get_result();

//     $output = '';

//     if ($result->num_rows > 0) {
//         while ($row = $result->fetch_assoc()) {

//             $role = strtoupper($row['role']);

//             $roleColor = match ($role) {
//                 'STUDENT' => '#3B82F6',
//                 'SUPERVISOR' => '#F59E0B',
//                 'ADMIN' => '#374151',
//                 default => '#9CA3AF'
//             };

//             $output .= "
//             <tr>
//                 <td>{$row['name']}</td>
//                 <td style='color:$roleColor;font-weight:600'>$role</td>
//                 <td>{$row['action']}</td>
//                 <td>{$row['module']}</td>
//                 <td>{$row['target_type']}</td>
//                 <td>{$row['target_id']}</td>
//                 <td>{$row['ip_address']}</td>
//                 <td>" . date("M d, Y h:i A", strtotime($row['created_at'])) . "</td>
//             </tr>";
//         }
//     } else {
//         $output .= "
//         <tr>
//             <td colspan='8' style='text-align:center; padding:15px;'>
//                 No activity logs found
//             </td>
//         </tr>";
//     }

//     return $output;
// }

// ojt settings
function renderActiveOJTCard($conn)
{
    $sql = "SELECT academic_year, required_hours, start_date, end_date, status
            FROM ojt_settings
            WHERE status = 'ACTIVE'
            ORDER BY settingID DESC
            LIMIT 1";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {

        $row = $result->fetch_assoc();

        return '
            <div class="ojt-card-box">
                <h4>Current Active OJT Setup</h4>

                <p><strong>Academic Year:</strong> ' . htmlspecialchars($row['academic_year']) . '</p>

                <p><strong>Required Hours:</strong> ' . htmlspecialchars($row['required_hours']) . ' Hours</p>

                <p><strong>Status:</strong> 
                    <span class="active-status">ACTIVE</span>
                </p>
            </div>
        ';
    }

    return '
        <div class="ojt-card-box empty">
            <p>No active OJT setup found.</p>
        </div>
    ';
}

// attendance settings
function getAttendanceSetting($conn, $key, $default = null) {

    $stmt = $conn->prepare("
        SELECT setting_value
        FROM attendance_settings
        WHERE setting_key = ?
    ");

    $stmt->bind_param("s", $key);
    $stmt->execute();

    $result = $stmt->get_result()->fetch_assoc();

    return $result['setting_value'] ?? $default;
}

// department management table
function renderDepartmentManagementTable($conn, $department = '')
{
    $where = "";

    if (!empty($department)) {
        $where = "WHERE prg_department = '" . $conn->real_escape_string($department) . "'";
    }

    $sql = "SELECT program_id, prg_name, prg_acro, prg_department, prg_department_code, status
            FROM program 
            $where
            ORDER BY prg_department ASC";

    $result = $conn->query($sql);

    $output = '';

    if ($result->num_rows > 0) {

        while ($row = $result->fetch_assoc()) {

            $status = strtoupper($row['status']);

            if ($status == "ACTIVE") {
                $statusColor = "#22C55E";
            } else {
                $statusColor = "#EF4444";
            }

            $output .= '
            <tr>
                <td>' . htmlspecialchars($row['prg_name']) . '</td>

                <td>' . htmlspecialchars($row['prg_acro']) . '</td>

                <td>
                    ' . htmlspecialchars($row['prg_department']) . '
                    <small style="color:gray;">(' . htmlspecialchars($row['prg_department_code']) . ')</small>
                </td>

                <td style="color: ' . $statusColor . '; font-weight:600;">
                    ' . htmlspecialchars($row['status']) . '
                </td>

                <td>
                    <button 
                        class="edit-program-btn"
                        data-id="' . $row['program_id'] . '"
                        data-name="' . htmlspecialchars($row['prg_name']) . '"
                        data-acro="' . htmlspecialchars($row['prg_acro']) . '"
                        data-department="' . htmlspecialchars($row['prg_department']) . '"
                        data-departmentcode="' . htmlspecialchars($row['prg_department_code']) . '"
                        data-status="' . htmlspecialchars($row['status']) . '">
                        Edit
                    </button>
                    <button class="delete-program-btn" data-id="' . $row['program_id'] . '">Delete</button>
                </td>
            </tr>';
        }
    } else {

        $output .= '
        <tr>
            <td colspan="5" style="text-align:center; padding:15px;">
                No programs found
            </td>
        </tr>';
    }

    return $output;
}

function renderDepartmentOptions($conn)
{
    $sql = "SELECT DISTINCT prg_department, prg_department_code FROM program";
    $result = $conn->query($sql);

    $output = '<option value="">All Departments</option>';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $output .= '
                <option value="' . htmlspecialchars($row['prg_department']) . '">
                    ' . htmlspecialchars($row['prg_department']) . ' (' . htmlspecialchars($row['prg_department_code']) . ')
                </option>
            ';
        }
    }

    return $output;
}

// function renderProgramOptions($conn){

//     $sql = "
//         SELECT 
//             program_id,
//             prg_name,
//             prg_acro
//         FROM program
//         WHERE status = 'ACTIVE'
//         ORDER BY prg_name ASC
//     ";

//     $result = $conn->query($sql);

//     $output = '<option value="">All Courses</option>';

//     if($result && $result->num_rows > 0){

//         while($row = $result->fetch_assoc()){

//             $output .= '
//                 <option value="' . htmlspecialchars($row['prg_acro']) . '">
//                     ' . htmlspecialchars($row['prg_name']) . ' 
//                     (' . htmlspecialchars($row['prg_acro']) . ')
//                 </option>
//             ';
//         }
//     }

//     return $output;
// }

// function renderYearLevelOptions($conn){

//     $sql = "
//         SELECT DISTINCT yearLevel
//         FROM ojtstudent
//         WHERE yearLevel IS NOT NULL
//         AND yearLevel != ''
//         ORDER BY yearLevel ASC
//     ";

//     $result = $conn->query($sql);

//     $output = '<option value="">All Year Levels</option>';

//     if($result && $result->num_rows > 0){

//         while($row = $result->fetch_assoc()){

//             $output .= '
//                 <option value="' . htmlspecialchars($row['yearLevel']) . '">
//                     ' . htmlspecialchars($row['yearLevel']) . '
//                 </option>
//             ';
//         }
//     }

//     return $output;
// }

function renderSelectOptions($conn, $type){

    $output = '';

    switch($type){

        case 'program':

            $sql = "
                SELECT prg_name, prg_acro
                FROM program
                WHERE status = 'ACTIVE'
                ORDER BY prg_name ASC
            ";

            $result = $conn->query($sql);

            $output .= '<option value="">All Courses</option>';

            while($row = $result->fetch_assoc()){

                $output .= '
                    <option value="'.$row['prg_acro'].'">
                        '.$row['prg_name'].' ('.$row['prg_acro'].')
                    </option>
                ';
            }

        break;

        case 'year':

            $sql = "
                SELECT DISTINCT yearLevel
                FROM ojtstudent
                ORDER BY yearLevel ASC
            ";

            $result = $conn->query($sql);

            $output .= '<option value="">All Year Levels</option>';

            while($row = $result->fetch_assoc()){

                $output .= '
                    <option value="'.$row['yearLevel'].'">
                        '.$row['yearLevel'].'
                    </option>
                ';
            }

        break;

        case 'department':
             $sql = "SELECT program_id, prg_name, prg_acro, prg_department, prg_department_code 
            FROM program 
            WHERE status = 'ACTIVE'
            ORDER BY prg_name ASC";

            $result = $conn->query($sql);

            $output = '<option value="">All Programs</option>';

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {

                    $label = $row['prg_name'] . ' (' . $row['prg_acro'] . ') - ' . $row['prg_department_code'];

                  $output .= '
                    <option value="'.$row['program_id'].'">
                        '.$row['prg_acro'].' - '.$row['prg_department'].'
                    </option>';
                }
            }

        break;
    }

    return $output;
}

// evaluation settings
function renderActiveEvaluationCard($conn)
{
    $sql = "SELECT attendance_weight, progress_weight, task_weight, date_updated
            FROM evaluation_settings
            ORDER BY settingID DESC
            LIMIT 1";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {

        $row = $result->fetch_assoc();

        $attendance = $row['attendance_weight'] * 100;
        $progress = $row['progress_weight'] * 100;
        $task = $row['task_weight'] * 100;

        return '
        <div class="eval-card">
            <h4>Current Evaluation Settings</h4>

            <div class="eval-grid">
                <div class="eval-item">
                    <span>Attendance</span>
                    <strong>' . $attendance . '%</strong>
                </div>

                <div class="eval-item">
                    <span>Performance</span>
                    <strong>' . $progress . '%</strong>
                </div>

                <div class="eval-item">
                    <span>Task Completion</span>
                    <strong>' . $task . '%</strong>
                </div>
            </div>

            <small>Last updated: ' . date("M d, Y h:i A", strtotime($row['date_updated'])) . '</small>
        </div>';
    }

    return '
    <div class="eval-card empty">
        <p>No evaluation settings found.</p>
    </div>';
}


// admin daashboard cards
function countStudents($conn)
{
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total 
        FROM users 
        WHERE role = 'student' AND isVerified = 'VERIFIED'
    ");
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'];
}

function countUnverifiedStudents($conn)
{
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total 
        FROM users 
        WHERE role = 'student' AND isVerified = 'NOT VERIFIED'
    ");
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'];
}

function countPendingStudents($conn)
{
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total 
        FROM users 
        WHERE role = 'student' AND isVerified = 'PENDING'
    ");
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'];
}

function countSupervisors($conn)
{
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total 
        FROM users 
        WHERE role = 'supervisor'
    ");
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'];
}

// trend takings
function getTrend($conn, $role, $status)
{
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total 
        FROM users 
        WHERE role = ? 
        AND isVerified = ?
        AND dateCreated >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ");

    $stmt->bind_param("ss", $role, $status);
    $stmt->execute();

    $recent = $stmt->get_result()->fetch_assoc()['total'];

    $stmt2 = $conn->prepare("
        SELECT COUNT(*) AS total 
        FROM users 
        WHERE role = ? 
        AND isVerified = ?
    ");

    $stmt2->bind_param("ss", $role, $status);
    $stmt2->execute();

    $total = $stmt2->get_result()->fetch_assoc()['total'];

    if ($total == 0) return "0%";

    $percent = ($recent / $total) * 100;

    return round($percent, 1) . "%";
}

// badges
function getBadge($count)
{
    if ($count >= 10) return "Hot";
    if ($count >= 3) return "New";
    return "Stable";
}

// supervisor cards

// getting superID
function getSupervisorIDByUserID($conn, $userID)
{
    $stmt = $conn->prepare("
        SELECT superID 
        FROM supervisor 
        WHERE email = (
            SELECT email FROM users WHERE userID = ?
        )
        LIMIT 1
    ");

    $stmt->bind_param("i", $userID);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc()['superID'] ?? null;
}

function countTotalAssignedStudents($conn, $superID)
{
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total FROM student_supervisor WHERE superID = ?;
    ");
    $stmt->bind_param("i", $superID);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'];
}

function countTotalActiveStudents($conn, $superID)
{
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total FROM student_supervisor WHERE superID = ? AND status = 'ACTIVE';
    ");
    $stmt->bind_param("i", $superID);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'];
}

function countTotalCompletedStudents($conn, $superID)
{
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total FROM student_supervisor WHERE superID = ? AND status = 'COMPLETED';
    ");
    $stmt->bind_param("i", $superID);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'];
}

function countPendingTasks($conn, $superID)
{
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM student_tasks
        WHERE superID = ?
        AND status = 'SUBMITTED'
    ");

    $stmt->bind_param("i", $superID);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc()['total'];
}

// super trend
function getSupervisorTrend($conn, $table, $column, $value, $dateColumn)
{
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM $table
        WHERE $column = ?
        AND $dateColumn >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ");

    $stmt->bind_param("s", $value);
    $stmt->execute();
    $recent = $stmt->get_result()->fetch_assoc()['total'];

    $stmt2 = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM $table
        WHERE $column = ?
    ");

    $stmt2->bind_param("s", $value);
    $stmt2->execute();
    $total = $stmt2->get_result()->fetch_assoc()['total'];

    if ($total == 0) return "0%";

    return round(($recent / $total) * 100, 1) . "%";
}

function timeAgo($datetime)
{
    $time = time() - strtotime($datetime);

    if ($time < 60) return "Just now";
    if ($time < 3600) return floor($time / 60) . " mins ago";
    if ($time < 86400) return floor($time / 3600) . " hrs ago";
    return floor($time / 86400) . " days ago";
}

// supervisor alerts
function getSupervisorAlerts($conn, $superID)
{
    $alerts = [];

    $sql1 = "
        SELECT s.studentID, s.name,
       MAX(a.log_date) as last_attendance
        FROM ojtstudent s
        INNER JOIN student_supervisor ss 
            ON ss.studentID = s.studentID
        LEFT JOIN attendance_logs a 
            ON s.studentID = a.studentID
        WHERE ss.superID = ?
        AND ss.status = 'ACTIVE'
        GROUP BY s.studentID
        HAVING last_attendance IS NULL 
        OR last_attendance < DATE_SUB(CURDATE(), INTERVAL 5 DAY)
    ";

    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("i", $superID);
    $stmt1->execute();
    $result1 = $stmt1->get_result();

    while ($row = $result1->fetch_assoc()) {
        $alerts[] = [
            "type" => "warning",
            "priority" => 1,
            "message" => $row['name'] . " has not recorded attendance for 5 days",
            "action" => "viewStudentProgress",
            "id" => $row['studentID']
        ];
    }

    $sql2 = "
    SELECT s.name, t.title, t.taskID
    FROM student_tasks t
    INNER JOIN ojtstudent s 
        ON t.studentID = s.studentID
    INNER JOIN student_supervisor ss 
        ON ss.studentID = s.studentID
    WHERE ss.superID = ?
      AND ss.status = 'ACTIVE'
      AND t.status IN ('NOT STARTED', 'IN PROGRESS')
      AND t.due_date < CURDATE()
";

    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $superID);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    while ($row = $result2->fetch_assoc()) {
        $alerts[] = [
            "type" => "danger",
            "priority" => 2,
            "message" => $row['name'] . " - Task '" . $row['title'] . "'  is overdue",
            "action" => "viewTask",
            "id" => $row['taskID']
        ];
    }


    $sql3 = "
        SELECT s.studentID, s.name
        FROM ojtstudent s
        INNER JOIN student_supervisor ss 
            ON ss.studentID = s.studentID
        WHERE ss.superID = ?
          AND ss.status = 'ACTIVE'
          AND NOT EXISTS (
              SELECT 1 FROM attendance_logs a
              WHERE a.studentID = s.studentID
                AND a.log_date >= DATE_SUB(CURDATE(), INTERVAL 5 DAY)
          )
          AND NOT EXISTS (
              SELECT 1 FROM student_tasks t
              WHERE t.studentID = s.studentID
                AND t.status IN ('IN PROGRESS', 'SUBMITTED', 'APPROVED')
          )
    ";

    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param("i", $superID);
    $stmt3->execute();
    $result3 = $stmt3->get_result();

    while ($row = $result3->fetch_assoc()) {
        $alerts[] = [
            "type" => "critical",
            "priority" => 3,
            "message" => $row['name'] . " is inactive (no attendance + no task activity)",
            "action" => "viewStudentProgress",
            "id" => $row['studentID']
        ];
    }

    usort($alerts, function ($a, $b) {
        return $b['priority'] <=> $a['priority'];
    });


    return $alerts;
}

// recent activitylogs
function getSupervisorActivities($conn, $superID)
{
    $activities = [];

    $sql = "
        SELECT 
            al.description,
            al.target_type,
            al.created_at

        FROM activity_log al

        WHERE al.target_type IN ('attendance', 'assignment')

        AND (
            al.target_id IN (
                SELECT ss.studentID
                FROM student_supervisor ss
                WHERE ss.superID = ?
                AND ss.status = 'ACTIVE'
            )
        )

        ORDER BY al.created_at DESC
        LIMIT 10
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $superID);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {

        $type = "info";

        if ($row['target_type'] === 'attendance') {
            $type = "primary";
        } elseif ($row['target_type'] === 'assignment') {
            $type = "success";
        } elseif (strpos(strtolower($row['description']), 'late') !== false) {
            $type = "warning";
        } elseif (strpos(strtolower($row['description']), 'failed') !== false) {
            $type = "danger";
        } else {
            $type = "info";
        }

        $activities[] = [
            "type" => $type,
            "message" => $row['description'],
            "time" => timeAgo($row['created_at'])
        ];
    }

    return $activities;
}


// student alerts
function getStudentAlerts($conn, $studentID)
{
    $alerts = [];

    $sql1 = "
        SELECT COUNT(*) AS attendance_count
        FROM attendance_logs
        WHERE studentID = ?
        AND log_date >= DATE_SUB(CURDATE(), INTERVAL 5 DAY)
    ";

    $stmt1 = $conn->prepare($sql1);

    if (!$stmt1) {
        die("SQL1 Error: " . $conn->error);
    }

    $stmt1->bind_param("s", $studentID);
    $stmt1->execute();

    $attendanceData = $stmt1->get_result()->fetch_assoc();

    if ($attendanceData['attendance_count'] == 0) {

        $alerts[] = [
            "type" => "warning",
            "priority" => 1,
            "message" => "You have no attendance recorded in the last 5 days.",
            "action" => "openAttendance",
            "id" => null
        ];
    }

    $sql2 = "
        SELECT taskID, title
        FROM student_tasks
        WHERE studentID = ?
        AND status IN ('NOT STARTED', 'IN PROGRESS')
        AND due_date < CURDATE()
    ";

    $stmt2 = $conn->prepare($sql2);

    if (!$stmt2) {
        die("SQL2 Error: " . $conn->error);
    }

    $stmt2->bind_param("s", $studentID);
    $stmt2->execute();

    $taskResults = $stmt2->get_result();

    while ($row = $taskResults->fetch_assoc()) {

        $alerts[] = [
            "type" => "danger",
            "priority" => 2,
            "message" => "Task '" . $row['title'] . "' is overdue.",
            "action" => "viewTask",
            "id" => $row['taskID'] ?? null
        ];
    }

    $sql3 = "
        SELECT required_hours, completed_hours
        FROM student_progress
        WHERE studentID = ?
    ";

    $stmt3 = $conn->prepare($sql3);

    if (!$stmt3) {
        die("SQL3 Error: " . $conn->error);
    }

    $stmt3->bind_param("s", $studentID);
    $stmt3->execute();

    $progress = $stmt3->get_result()->fetch_assoc();

    if ($progress) {

        $requiredHours = (int)$progress['required_hours'];
        $completedHours = (int)$progress['completed_hours'];

        $progressPercentage = 0;

        if ($requiredHours > 0) {
            $progressPercentage = ($completedHours / $requiredHours) * 100;
        }

        if ($progressPercentage < 50) {

            $alerts[] = [
                "type" => "critical",
                "priority" => 3,
                "message" => "You are at risk of not completing your required OJT hours.",
                "action" => "viewProgress",
                "id" => null
            ];
        }
    }

    $sqlStage = "
            SELECT COALESCE(SUM(total_hours), 0) as hours
            FROM attendance_logs
            WHERE studentID = ?
        ";

        $stmtStage = $conn->prepare($sqlStage);

        if (!$stmtStage) {
            die("STAGE SQL Error: " . $conn->error);
        }

        $stmtStage->bind_param("s", $studentID);
        $stmtStage->execute();

        $stageData = $stmtStage->get_result()->fetch_assoc();

        $hours = floatval($stageData['hours'] ?? 0);
        
        if ($hours <= 20) {

            $alerts[] = [
                "type" => "info",
                "priority" => 3,
                "message" => "ORIENTATION stage is now active (0–20 hours).",
                "action" => "viewStage",
                "id" => "ORIENTATION"
            ];

        } elseif ($hours <= 100) {

            $alerts[] = [
                "type" => "info",
                "priority" => 3,
                "message" => "You are now in BASIC TRAINING stage (21–100 hours).",
                "action" => "viewStage",
                "id" => "BASIC_TRAINING"
            ];

        } elseif ($hours <= 250) {

            $alerts[] = [
                "type" => "info",
                "priority" => 3,
                "message" => "SKILL DEVELOPMENT stage is now active.",
                "action" => "viewStage",
                "id" => "SKILL_DEVELOPMENT"
            ];

        } elseif ($hours <= 400) {

            $alerts[] = [
                "type" => "info",
                "priority" => 3,
                "message" => "ACTIVE DEPLOYMENT stage started. Handle real tasks carefully.",
                "action" => "viewStage",
                "id" => "ACTIVE_DEPLOYMENT"
            ];

        } else {

            $alerts[] = [
                "type" => "success",
                "priority" => 0,
                "message" => "FINAL PHASE reached. Prepare your final report.",
                "action" => "viewStage",
                "id" => "FINAL_PHASE"
            ];
        }

    usort($alerts, function ($a, $b) {
        return $b['priority'] <=> $a['priority'];
    });

    return $alerts;
}

// student attendance card
function renderStudentAttendanceTable($conn, $studentID, $category = ''){
    $sql = "
        SELECT 
            log_date,
            first_time_in,
            final_time_out,
            status,
            total_hours,
            current_state
        FROM attendance_logs
        WHERE studentID = ?
    ";

    $params = [$studentID];
    $types = "s";

    if (!empty($category)) {
        $sql .= " AND status = ?";
        $params[] = $category;
        $types .= "s";
    }

    $sql .= " ORDER BY log_date DESC";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    $result = $stmt->get_result();

    $output = '';

    if ($result->num_rows > 0) {

        while ($row = $result->fetch_assoc()) {

            $timeIn = $row['first_time_in']
                ? date("h:i A", strtotime($row['first_time_in']))
                : '--';

            $timeOut = $row['final_time_out']
                ? date("h:i A", strtotime($row['final_time_out']))
                : '--';

            $hours = $row['total_hours']
                ? number_format($row['total_hours'], 2)
                : '0.00';

            $output .= "
            <tr>
                <td>" . date("M d, Y", strtotime($row['log_date'])) . "</td>
                <td>{$timeIn}</td>
                <td>{$timeOut}</td>
                <td>{$row['status']}</td>
                <td>{$hours}</td>
                <td>{$row['current_state']}</td>
            </tr>";
        }

    } else {

        $output .= "
        <tr>
            <td colspan='6' style='text-align:center;'>No attendance found</td>
        </tr>";
    }

    return $output;
}


// student reports
function renderReports($conn, $studentID)
{
    $progressData = $conn->query("
        SELECT completed_hours, required_hours
        FROM student_progress
        WHERE studentID = '$studentID'
        LIMIT 1
    ")->fetch_assoc();

    $completed = floatval($progressData['completed_hours'] ?? 0);
    $required = floatval($progressData['required_hours'] ?? 500);

    $percent = ($required > 0)
        ? ($completed / $required) * 100
        : 0;

    $stages = [
        [
            "type" => "ORIENTATION_REPORT",
            "title" => "Orientation Report",
            "desc" => "Submit onboarding reflection",
            "min" => 0,
            "max" => 20
        ],
        [
            "type" => "WEEKLY_REPORT",
            "title" => "Weekly Training Report",
            "desc" => "Submit weekly learning progress",
            "min" => 20,
            "max" => 60
        ],
        [
            "type" => "DEPLOYMENT_REPORT",
            "title" => "Deployment Report",
            "desc" => "Worksite performance report",
            "min" => 60,
            "max" => 90
        ],
        [
            "type" => "FINAL_REPORT",
            "title" => "Final Report",
            "desc" => "Final internship summary",
            "min" => 90,
            "max" => 100
        ]
    ];

    $output = '<div class="report-cards">';

    foreach ($stages as $stage) {

        $isUnlocked = $percent >= $stage['min'];

        $class = $isUnlocked ? "clickable" : "locked";
        $status = $isUnlocked ? "active" : "locked";
        $text = $isUnlocked ? "Click to submit →" : "Locked";

        $onclick = $isUnlocked
            ? "openReportForm('{$stage['type']}', 1)"
            : "";

        $output .= "
        <div class='report-card $class' onclick=\"$onclick\">

            <div class='card-header'>
                <div>
                    <h4>{$stage['title']}</h4>
                    <small>{$stage['min']}% - {$stage['max']}% Progress</small>
                </div>

                <span class='status $status'>
                    " . strtoupper($status) . "
                </span>
            </div>

            <div class='card-body'>
                <p>{$stage['desc']}</p>

                <div class='card-action-hint'>
                    $text
                </div>
            </div>

        </div>";
    }

    $output .= '</div>';

    return $output;
}

function renderProgressHeader($conn, $studentID)
{
    $progress = $conn->query("
        SELECT completed_hours, required_hours
        FROM student_progress
        WHERE studentID = '$studentID'
        LIMIT 1
    ")->fetch_assoc();

    $hours = floatval($progress['completed_hours'] ?? 0);
    $required = floatval($progress['required_hours'] ?? 500);

    $percent = $required > 0 ? min(($hours / $required) * 100, 100) : 0;

    if ($hours <= 20) {
        $stage = "Orientation Stage";
        $next = "Training Stage";
        $nextThreshold = 21;
    } elseif ($hours <= 200) {
        $stage = "Training Stage";
        $next = "Deployment Stage";
        $nextThreshold = 201;
    } elseif ($hours <= 400) {
        $stage = "Deployment Stage";
        $next = "Final Stage";
        $nextThreshold = 401;
    } else {
        $stage = "Final Stage";
        $next = "Completed";
        $nextThreshold = $required;
    }

    return "
    <div class='reports-progress-header'>

        <div class='reports-progress-info'>
            <h3>{$stage}</h3>
            <p>You are currently in: {$hours} / {$required} hours</p>
        </div>

        <div class='reports-progress-bar'>
            <div class='reports-progress-fill' style='width: {$percent}%;'></div>
        </div>

        <div class='reports-progress-meta'>
            <span>Next: {$next} ({$nextThreshold} hrs)</span>
        </div>

    </div>
    ";
}
