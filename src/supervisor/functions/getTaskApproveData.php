<?php
require_once("../../auth/supervisor_auth.php");
require_once("../../Shared/kapstongConnection.php");

$taskID = $_POST['taskID'] ?? '';

$sql = "
    SELECT 
        student_tasks.*,
        ojtstudent.name
    FROM student_tasks
    INNER JOIN ojtstudent
        ON student_tasks.studentID = ojtstudent.studentID
    WHERE student_tasks.taskID = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $taskID);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {

    $files = explode(",", $row['submission_file']);
    $firstFile = trim($files[0]);
?>

    <div class="modal-header">
        <h3>Task Review</h3>
        <button onclick="closeTaskModal()" class="modal-close">&times;</button>
    </div>

    <div class="task-profile-strip">
        <div class="task-strip-icon">
            <i class='bx bx-task'></i>
        </div>
        <div class="task-strip-info">
            <span class="task-strip-title"><?= $row['title'] ?></span>
            <span class="task-strip-meta"><?= $row['name'] ?> • <?= $row['studentID'] ?></span>
        </div>
        <span class="status-badge"><?= $row['status'] ?></span>
    </div>

    <div class="supervisor-tabs">
        <button type="button" class="tab-btn active" data-tab="info">
            <i class='bx bx-info-circle'></i> Task Info
        </button>
        <button type="button" class="tab-btn" data-tab="file">
            <i class='bx bx-file'></i> Submitted File
        </button>
        <button type="button" class="tab-btn" data-tab="feedback">
            <i class='bx bx-message-square-edit'></i> Feedback
        </button>
    </div>

    <div class="student-modal-content">

        <!-- TASK INFO TAB -->
        <div class="tab-panel active" id="tab-info">
            <div class="info-card">
                <div class="card-body">

                    <div class="info-row">
                        <span class="info-label">Student ID</span>
                        <span class="info-value"><?= $row['studentID'] ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Student Name</span>
                        <span class="info-value"><?= $row['name'] ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Task Title</span>
                        <span class="info-value"><?= $row['title'] ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Student Note</span>
                        <span class="info-value"><?= $row['student_note'] ?: '-' ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="info-value"><?= $row['status'] ?></span>
                    </div>

                </div>
            </div>
        </div>

        <!-- FILE TAB -->
        <div class="tab-panel" id="tab-file">
            <div class="documents-grid">
                <div class="doc-card">

                    <?php if (!empty($row['submission_file'])): ?>
                        <div class="doc-header">
                            <span class="doc-title">Submitted File</span>
                        </div>
                        <button class="btn-preview"
                            onclick="previewImage('../../uploads/student_tasks/<?= $row['studentID'] ?>/<?= $firstFile ?>')">
                            👁 View File
                        </button>

                        <span class="status-badge success">Uploaded</span>
                    <?php else: ?>
                        <span class="status-badge missing">No File Uploaded</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- FEEDBACK TAB -->
        <div class="tab-panel" id="tab-feedback">
            <div class="info-card">
                <div class="card-body">

                    <div class="form-group">
                        <label>Supervisor Feedback</label>

                        <textarea
                            id="supervisorFeedback"
                            class="feedback-textarea"
                            placeholder="Write recommendations, corrections, or comments for the student...">
        </textarea>

                        <small class="feedback-hint">
                            Provide suggestions before approving or rejecting the task.
                        </small>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <div class="task-action-buttons">

        <button
            class="reject-btn"
            onclick="updateTaskStatus(<?= $row['taskID'] ?>, 'REJECTED')">
            <i class='bx bx-x-circle'></i> Reject
        </button>

        <button
            class="approve-btn"
            onclick="updateTaskStatus(<?= $row['taskID'] ?>, 'APPROVED')">
            <i class='bx bx-check-circle'></i> Approve
        </button>
    </div>


<?php }
