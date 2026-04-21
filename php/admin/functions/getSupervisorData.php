<?php
require_once("../../kapstongConnection.php");
require_once("../../functions.php");

$superID = $_POST['superID'] ?? '';

$sql = "SELECT 
            superID,
            name,
            email,
            number,
            department,
            date_created
        FROM supervisor
        WHERE superID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $superID);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
?>

    <div class="modal-header">
        <h3>Supervisor Details</h3>
        <button onclick="closeSuperViewModal()" class="modal-close">&times;</button>
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
                    <span class="info-label">Supervisor ID</span>
                    <span class="info-value"><?= $row['superID'] ?></span>
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
                    <span class="info-label">Contact Number</span>
                    <span class="info-value"><?= $row['number'] ?></span>
                </div>

            </div>
        </div>

        <!-- WORK INFO -->
        <div class="info-card">

            <div class="card-header" onclick="toggleSection(this)">
                <h4>Work Information</h4>
                <span class="arrow">▼</span>
            </div>

            <div class="card-body">

                <div class="info-row">
                    <span class="info-label">Department</span>
                    <span class="info-value"><?= $row['department'] ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Role</span>
                    <span class="info-value">
                        <span class="status-badge supervisor" style="color:whitesmoke;">SUPERVISOR</span>
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">Date Created</span>
                    <span class="info-value"><?= date("M d, Y h:i A", strtotime($row['date_created'])) ?></span>
                </div>

            </div>

        </div>

        <div class="info-card">

            <div class="card-header" onclick="toggleSection(this)">
                <h4>Student Assignments</h4>
                <span class="arrow">▼</span>
            </div>

            <div class="card-body">
                <div class="student-scroll-box">
                    <?php echo renderSupervisorAssignedStudents($conn, $superID); ?>
                </div>

            </div>

        </div>

    </div>

    </div>

<?php
}
?>