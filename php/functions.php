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
                <td>' . $row['studentID'] . '</td>
                <td>' . $row['name'] . '</td>
                <td>' . $row['title'] . '</td>
                <td>' . $row['status'] . '</td>
                <td>' . date("M d, Y h:i A", strtotime($row['date_created'])) . '</td>
                <td>
                    <button 
                        class="view-btn" 
                        onclick="viewTask(' . $row['taskID'] . ')">
                        Review
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
                <small>' . $row['email'] . '</small>

            </div>';
        }
    } else {
        $output .= '<div class="list-item">No students found</div>';
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
                <small>' . $row['email'] . '</small>

            </div>';
        }
    } else {
        $output .= '<div class="list-item">No supervisors found</div>';
    }

    return $output;
}

// admin activity_log view
function renderActivityLogTable($conn, $search = '')
{
    $where = "";

    if (!empty($search)) {
        $where = "WHERE (
            users.name LIKE '%$search%' OR
            users.email LIKE '%$search%' OR
            activity_log.action LIKE '%$search%' OR
            activity_log.module LIKE '%$search%' OR
            activity_log.target_id LIKE '%$search%'
        )";
    }

    $sql = "SELECT 
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
            $where
            ORDER BY activity_log.created_at DESC";

    $result = $conn->query($sql);

    $output = '';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $role = strtoupper($row['role']);

            switch ($role) {
                case 'STUDENT':
                    $roleColor = '#3B82F6';
                    break;
                case 'SUPERVISOR':
                    $roleColor = '#F59E0B';
                    break;
                case 'ADMIN':
                    $roleColor = '#374151';
                    break;
                default:
                    $roleColor = '#9CA3AF';
            }

            $output .= '
            <tr>
                <td>' . $row['name'] . '</td>
                <td style="color: ' . $roleColor . '; font-weight:600;"> ' . $role . '</td>
                <td>' . $row['action'] . '</td>
                <td>' . $row['module'] . '</td>
                <td>' . $row['target_type'] . '</td>
                <td>' . $row['target_id'] . '</td>
                <td>' . $row['ip_address'] . '</td>
                <td>' . date("M d, Y h:i A", strtotime($row['created_at'])) . '</td>
            </tr>';
        }
    } else {
        $output .= '
        <tr>
            <td colspan="8" style="text-align:center; padding:15px; font-weight:500;">
                No activity logs found
            </td>
        </tr>';
    }

    return $output;
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
    if ($count >= 20) return "Hot";
    if ($count >= 5) return "New";
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
