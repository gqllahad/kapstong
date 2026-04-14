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
    if ($vf === 'NOT VERIFIED') {
        $where .= " AND users.isVerified != 'VERIFIED'";
    } elseif ($vf === 'VERIFIED') {
        $where .= " AND users.isVerified = 'VERIFIED'";
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
                            <button class="approve-btn" onclick="approveUser(' . $row['studentID'] . ')">Approve</button>
                            <button class="reject-btn" onclick="rejectUser(' . $row['studentID'] . ')">Reject</button>';
            } else {
                $output .= '<span style="color:lime;">✔</span>';
            }
        }
    } else {
        $output .= '<tr><td colspan="7">No records found</td></tr>';
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
                $output .= '<span style="color:lime;">✔</span>';
            }
        }
    } else {
        $output .= '<tr><td colspan="7">No records found</td></tr>';
    }

    $output .= '</tbody></table>';

    return $output;
}
