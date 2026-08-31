<?php
require_once("../../Shared/kapstongConnection.php");
require_once("../../auth/admin_auth.php");

function e($val) {
    return htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8');
}

$studentID = $_POST['studentID'] ?? '';

$sql = "SELECT 
            users.*,
            students.course,
            students.yearLevel,
            students.address,
            students.semester,
            students.schoolYear,
            docs.idUpload,
            docs.regFormUpload,
            docs.profilePicture,
            docs.status AS docStatus
        FROM users
        LEFT JOIN ojtstudent students 
            ON users.studentID = students.studentID
        LEFT JOIN student_documents docs
            ON users.studentID = docs.studentID
        WHERE users.studentID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {

    // Build a safe URL + JS-safe encoded version for each document, once
    $docs = ['idUpload' => 'Student ID', 'regFormUpload' => 'Registration Form', 'profilePicture' => 'Student Picture'];
    $docData = [];
    foreach ($docs as $field => $label) {
        $hasFile = !empty($row[$field]); // check RAW value, not escaped
        $path = $hasFile ? '../../uploads/student_uploads/' . $studentID . '/' . $row[$field] : '';
        $docData[$field] = [
            'label' => $label,
            'hasFile' => $hasFile,
            'jsPath' => json_encode($path), // safe for onclick
        ];
    }
?>
    <div class="modal-header">
        <h3>Student Details</h3>
        <button onclick="closeModal()" class="modal-close">&times;</button>
    </div>

    <div class="supervisor-tabs">
        <button type="button" class="tab-btn active" data-tab="basic">Basic Info</button>
        <button type="button" class="tab-btn" data-tab="academic">Academic Info</button>
        <button type="button" class="tab-btn" data-tab="documents">Documents</button>
    </div>

    <div class="student-modal-content">

        <!-- BASIC INFO TAB -->
        <div class="tab-panel active" id="tab-basic">
            <div class="info-card">
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label">Student ID</span>
                        <span class="info-value"><?= e($row['studentID']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Name</span>
                        <span class="info-value"><?= e($row['name']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email</span>
                        <span class="info-value"><?= e($row['email']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Mobile Number</span>
                        <span class="info-value"><?= e($row['mobileNumber']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Address</span>
                        <span class="info-value"><?= e($row['address']) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ACADEMIC INFO TAB -->
        <div class="tab-panel" id="tab-academic">
            <div class="info-card">
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label">Course</span>
                        <span class="info-value"><?= e($row['course']) ?: '-' ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Year Level</span>
                        <span class="info-value"><?= e($row['yearLevel']) ?: '-' ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Semester</span>
                        <span class="info-value"><?= e($row['semester']) ?: '-' ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">School Year</span>
                        <span class="info-value"><?= e($row['schoolYear']) ?: '-' ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="info-value"><?= e($row['isVerified']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Date Admitted</span>
                        <span class="info-value"><?= e($row['dateCreated']) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- DOCUMENTS TAB -->
        <div class="tab-panel" id="tab-documents">
             <div class="documents-grid">
            <!-- ID -->
                <div class="doc-card">
                    <div class="doc-header">
                        <span class="doc-title">Student ID</span>
                    </div>

                    <?php if (!empty(e($row['idUpload']))): ?>
                        <button class="btn-preview"
                            onclick="previewImage('<?php echo '../../uploads/student_uploads/' . $studentID . '/' . e($row['idUpload']); ?>')">
                            👁 View Document
                        </button>
                    <?php else: ?>
                        <span class="status-badge missing">Not Uploaded</span>
                    <?php endif; ?>
                </div>

                <!-- Registration Form -->
                <div class="doc-card">
                    <div class="doc-header">
                        <span class="doc-title">Registration Form</span>
                    </div>

                    <?php if (!empty(e($row['regFormUpload']))): ?>
                        <button class="btn-preview"
                            onclick="previewImage('<?php echo '../../uploads/student_uploads/' . $studentID . '/' . e($row['regFormUpload']); ?>')">
                            👁 View Document
                        </button>
                    <?php else: ?>
                        <span class="status-badge missing">Not Uploaded</span>
                    <?php endif; ?>
                </div>

                <!-- profile pic -->
                <div class="doc-card">
                    <div class="doc-header">
                        <span class="doc-title">Student Picture</span>
                    </div>

                    <?php if (!empty(e($row['profilePicture']))): ?>
                        <button class="btn-preview"
                            onclick="previewImage('<?php echo '../../uploads/student_uploads/' . $studentID . '/' . e($row['profilePicture']); ?>')">
                            👁 View Document
                        </button>
                    <?php else: ?>
                        <span class="status-badge missing">Not Uploaded</span>
                    <?php endif; ?>
                </div>
                </div>
        </div>

    </div>
<?php
}
?>



