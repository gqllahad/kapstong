<?php
require_once("../../kapstongConnection.php");

$search = $_POST['search'] ?? '';
$type = 'student';

function renderApprovalTable($conn, $type, $search = '')
{
    $where = "WHERE users.role = '$type'";

    $where .= " AND users.isVerified != 'VERIFIED'";

    if (!empty($search)) {
        $where .= " AND (
            users.studentID LIKE '%$search%' OR
            users.name LIKE '%$search%' OR
            users.email LIKE '%$search%'
        )";
    }


    $sql = "SELECT 
        users.studentID,
        users.name,
        users.email,
        users.isVerified,
        students.course,
        students.yearLevel
    FROM users
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

                <td>
                    <button class="view-btn" onclick="viewUser(' . $row['studentID'] . ')">View</button>
                    <button class="approve-btn" onclick="approveUser(' . $row['studentID'] . ')">Approve</button>
                    <button class="reject-btn" onclick="rejectUser(' . $row['studentID'] . ')">Reject</button>
                </td>
            </tr>';
        }
    } else {
        $output .= '<tr>
            <td colspan="7" style="text-align:center;padding:15px;">
                No results found
            </td>
        </tr>';
    }

    return $output;
}

echo renderApprovalTable($conn, $type, $search);
