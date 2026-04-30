<?php
require_once("../../auth/supervisor_auth.php");
require_once("../../kapstongConnection.php");

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

    <div class="student-modal-content">

        <div class="info-card">
            <div class="card-header" onclick="toggleSection(this)">
                <h4>Task Information</h4>
                <span class="arrow">▼</span>
            </div>

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

        <div class="divider"></div>

        <div class="info-card">
            <div class="card-header" onclick="toggleSection(this)">
                <h4>Uploaded Files</h4>
                <span class="arrow">▼</span>
            </div>

            <div class="card-body">
                <div class="documents-grid">

                    <div class="doc-card">

                        <?php if (!empty($row['submission_file'])): ?>
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
        </div>

        <div class="divider"></div>

        <div class="section-title">
            <h3>Supervisor Notes</h3>
        </div>
        <div class="info-card">
            <div class="card-header" onclick="toggleSection(this)">
                <h4>Supervisor Evaluation</h4>
                <span class="arrow">▼</span>
            </div>

            <div class="card-body">

                <div class="rating-wrapper">
                    <div class="rating-select-container">
                        <select id="supervisorRating" class="rating-select">
                            <option value="">Select Rating</option>
                            <option value="A+">A+ — Excellent</option>
                            <option value="A">A — Very Good</option>
                            <option value="B+">B+ — Good</option>
                            <option value="B">B — Satisfactory</option>
                            <option value="C">C — Needs Improvement</option>
                        </select>

                        <i class="bi bi-chevron-down select-icon"></i>
                    </div>
                </div>

            </div>
        </div>

        <div class="info-card">
            <div class="card-header" onclick="toggleSection(this)">
                <h4>Supervisor Feedback</h4>
                <span class="arrow">▼</span>
            </div>

            <div class="card-body">

                <textarea
                    id="supervisorFeedback"
                    placeholder="Write recommendations, corrections, or comments for the student..."
                    style="width:100%; min-height:120px; padding:10px; border-radius:8px; border:1px solid #ccc;">
        </textarea>

                <small style="color:#666;">
                    Provide suggestions before approving or rejecting the task.
                </small>

            </div>
        </div>

        <div class="divider"></div>

        <div class="task-action-buttons">

            <button
                class="reject-btn"
                onclick="updateTaskStatus(<?= $row['taskID'] ?>, 'REJECTED')">
                Reject
            </button>

            <button
                class="approve-btn"
                onclick="updateTaskStatus(<?= $row['taskID'] ?>, 'APPROVED')">
                Approve
            </button>
        </div>

    </div>

<?php } ?>