<?php
require_once("../../kapstongConnection.php");

$studentID = $_POST['studentID'] ?? '';

$sql = "SELECT 
            users.*,
            students.course,
            students.yearLevel,
            students.address,
            students.semester,
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
?>
    <div class="modal-header">
        <h3>Pending to Approve</h3>
        <button onclick="closeApproveModal()" class="modal-close">&times;</button>
    </div>

    <div class="student-modal-content">

        <!-- BASIC INFO -->
        <div class="info-card">

            <div class="card-header" onclick="toggleSection(this)">
                <h4>Basic Information</h4>
                <span class="arrow">▼</span>
            </div>

            <div class="card-body">
                <div class="info-row">
                    <span class="info-label">Student ID</span>
                    <span class="info-value"><?= $row['studentID'] ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Name</span>
                    <span class="info-value"><?= $row['name'] ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Email</span>
                    <span class="info-value"><?= $row['email'] ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Mobile Number</span>
                    <span class="info-value"><?= $row['mobileNumber'] ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Address</span>
                    <span class="info-value"><?= $row['address'] ?></span>
                </div>
            </div>
        </div>

        <!-- ACADEMIC INFO -->
        <div class="info-card">
            <div class="card-header" onclick="toggleSection(this)">
                <h4>Academic Information</h4>
                <span class="arrow">▼</span>
            </div>

            <div class="card-body">
                <div class="info-row">
                    <span class="info-label">Course</span>
                    <span class="info-value"><?= $row['course'] ?? '-' ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Year Level</span>
                    <span class="info-value"><?= $row['yearLevel'] ?? '-' ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Semester</span>
                    <span class="info-value"><?= $row['semester'] ?? '-' ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label">School Year</span>
                    <span class="info-value"><?= $row['schoolYear'] ?? '-' ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Status</span>
                    <span class="info-value"><?= $row['isVerified'] ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Date Admitted</span>
                    <span class="info-value"><?= $row['dateCreated'] ?></span>
                </div>
            </div>

        </div>

        <!-- uploads -->
        <div class="info-card">
            <div class="card-header" onclick="toggleSection(this)">
                <h4>Uploaded Documents</h4>
                <span class="arrow">▼</span>
            </div>

            <div class="card-body">
                <div class="documents-grid">

                    <!-- ID -->
                    <div class="doc-card">
                        <div class="doc-header">
                            <span class="doc-title">Student ID</span>
                        </div>

                        <?php if (!empty($row['idUpload'])): ?>
                            <button class="btn-preview"
                                onclick="previewImage('<?php echo '../../uploads/student_uploads/' . $studentID . '/' . $row['idUpload']; ?>')">
                                👁 View Document
                            </button>
                            <span class="status-badge success">Uploaded</span>
                        <?php else: ?>
                            <span class="status-badge missing">Not Uploaded</span>
                        <?php endif; ?>
                    </div>

                    <!-- Registration Form -->
                    <div class="doc-card">
                        <div class="doc-header">
                            <span class="doc-title">Registration Form</span>
                        </div>

                        <?php if (!empty($row['regFormUpload'])): ?>
                            <button class="btn-preview"
                                onclick="previewImage('<?php echo '../../uploads/student_uploads/' . $studentID . '/' . $row['regFormUpload']; ?>')">
                                👁 View Document
                            </button>
                            <span class="status-badge success">Uploaded</span>
                        <?php else: ?>
                            <span class="status-badge missing">Not Uploaded</span>
                        <?php endif; ?>
                    </div>

                    <!-- profile pic -->
                    <div class="doc-card">
                        <div class="doc-header">
                            <span class="doc-title">Student Picture</span>
                        </div>

                        <?php if (!empty($row['profilePicture'])): ?>
                            <button class="btn-preview"
                                onclick="previewImage('<?php echo '../../uploads/student_uploads/' . $studentID . '/' . $row['profilePicture']; ?>')">
                                👁 View Document
                            </button>
                            <span class="status-badge success">Uploaded</span>
                        <?php else: ?>
                            <span class="status-badge missing">Not Uploaded</span>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

        </div>

        <div class="approval-section">

            <label class="approve-checkbox">
                <input type="checkbox" id="confirmApprove">
                <span>I confirm that this student will be approved.</span>
            </label>

            <button
                class="btn-approve"
                id="approveBtn"
                disabled
                onclick="approveStudent('<?= $row['studentID'] ?>')">
                ✔ Approve Student
            </button>

        </div>

    </div>
<?php
}
?>