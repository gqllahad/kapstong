<?php
require_once("../auth/auth_guard.php");

requireRole(['ADMIN', 'supervisor']);
?>

<?php if(isset($_SESSION['status'])): ?>

<?php unset($_SESSION['status']); ?>
<?php endif; ?>



<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>OJT Attendance</title>
    <link rel="icon" type="image/png" href="../../public/kapstongImage/logo.jpg">
     <link rel="stylesheet" href="../../public/css/rfid/rfidPhase2.css" />
     <link
  href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css'
  rel='stylesheet'
>
     <meta name="description" content="Kapstong for you and me!" />
</head>

<body>

<div class="container"> 
    <div class="header">
        <a href="../Session/loginPhase.php">
            <img class="scanner-logo" src="../../public/kapstongImage/logo.jpg" alt="Logo">
        </a>

        <h1>Granby On-the-Job Training Attendance System</h1>

    </div>

    <div class="subtitle">Tap your card to record attendance</div>

   
        <input 
            type="text" 
            name="rfid" 
            id="rfidInput"
            autofocus 
            placeholder="Waiting for scan..." 
            autocomplete="off"
        >
    

    <div class="status">
        <?php
        if(isset($_SESSION['status'])){
            $status = $_SESSION['status'];

            if(strpos($status, "SUCCESS") !== false){
                echo "<span class='success'>$status</span>";
            } else {
                echo "<span class='error'>$status</span>";
            }

            unset($_SESSION['status']);
        }
        ?>
    </div>

    <div class="clock" id="clock"></div>

</div>

<div class="dashboardOverlay" id="dashboardOverlay"></div>

<div id="dashboardPanel">

  <div class="dashboard-header">

        <div class="dashboard-title">
            <div class="dashboard-icon" onclick="toggleDashboard()">
                <i class='bx bx-bar-chart-alt-2'></i>
            </div>

            <div class="dashboard-text">
                <h2>Attendance Monitoring</h2>
                <p>Real-time OJT attendance activity and tracking</p>
            </div>
        </div>

       

        <div class="dashboard-actions">

             <div class="dashboard-status">
                <span class="live-dot"></span>
                <span>LIVE</span>
            </div>

            <button class="download-btn" onclick="downloadTodayAttendance()">
                <i class='bx bx-download'></i>
            </button>

        </div>

    </div>

    <div class="emergency-panel" id="emergency-panel">

        <div class="emergency-info">
            <i class='bx bx-error-circle'></i>

            <div>
                <h3>Emergency Time Out</h3>
                <p>Allow a student to time out before scheduled hours.</p>
            </div>
        </div>

        <button class="emergency-btn" onclick="openEmergencyModal()">
            <i class='bx bx-exit'></i>
            Authorize
        </button>

    </div>

    <div class="manual-attendance-panel" id="manual-attendance-panel">

        <div class="manual-attendance-info">
            <i class='bx bx-edit-alt'></i>

            <div>
                <h3>Manual Attendance</h3>
                <p>Record attendance for a student who lost or forgot their RFID card.</p>
            </div>
        </div>

        <button class="manual-attendance-trigger-btn" onclick="openManualAttendanceModal()">
            <i class='bx bx-user-check'></i>
            Record
        </button>

    </div>

    <div class="table-wrapper">
        <div id="attendanceTable"></div>
    </div>

</div>

<!-- <div id="statusModal" class="modal">
    <div class="modal-content">
        <div class="modal-icon" id="modalIcon"></div>

        <div id="modalText"></div>
        <button onclick="closeModal()">OK</button>

    </div>
</div> -->

<div id="toastContainer"></div>

<div id="emergencyModal" class="emergency-modal">

    <div class="emergency-box">

    <div class="emergency-header">
           <div class="emergency-icon">
        <i class='bx bx-shield-quarter'></i>
    </div>

    <div class="emergency-text">
        <h3>Emergency Time Out Request</h3>
        <p>Process urgent student early dismissal securely</p>
    </div>
    </div>

        <input type="text" id="emergencyRfid" placeholder="Scan or type RFID">

         <textarea id="emergencyReason" placeholder="Reason for emergency time out..."></textarea>

         <div class="reason-chips compact">

            <button type="button" onclick="setReason('Medical emergency')"> 
                    <i class='bx bx-plus-medical'></i>
                            Medical</button>

            <button type="button" onclick="setReason('Family emergency')"> <i class='bx bx-home-heart'></i>
    Family</button>

            <button type="button" onclick="setReason('School-related urgent matter')"><i class='bx bx-book-open'></i>
    Academic</button>

            <button type="button" onclick="setReason('Personal urgent reason')"> <i class='bx bx-error-circle'></i>
    Personal</button>

        </div>

        <div class="emergency-actions">
          
            <button onclick="closeEmergencyModal()">Cancel</button>
              <button onclick="submitEmergencyTimeout()">Confirm</button>
        </div>

    </div>

</div>

<!-- manual attendance -->
<div class="manual-attendance-modal" id="manual-attendance-modal">
    <div class="modal-header">
        <h3><i class='bx bx-edit-alt'></i> Manual Attendance Entry</h3>
        <button onclick="closeManualAttendanceModal()" class="modal-close">&times;</button>
    </div>

    <form id="manualAttendanceForm">
        <div class="form-group">
            <label>Student</label>
            <select id="manualStudentSelect" required>
                <option value="">Select a student...</option>
                <!-- populated with supervisor's assigned students -->
            </select>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Date</label>
                <input type="date" id="manualDate" max="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select id="manualStatus" required>
                    <option value="present">Present</option>
                    <option value="late">Late</option>
                    <option value="excused">Excused</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Time In</label>
                <input type="time" id="manualTimeIn" required>
            </div>
            <div class="form-group">
                <label>Time Out</label>
                <input type="time" id="manualTimeOut" required>
            </div>
        </div>

        <div class="form-group">
            <label>Reason for Manual Entry <span class="required-mark">*</span></label>
            <textarea id="manualReason" placeholder="e.g. Student forgot/lost RFID card, verified in person" required maxlength="300"></textarea>
        </div>

        <div class="manual-warning">
            <i class='bx bx-info-circle'></i>
            <span>This entry will be flagged as manually recorded and logged under your account for audit purposes.</span>
        </div>

        <div class="edit-task-actions">
            <button type="button" class="cancel-btn" onclick="closeManualAttendanceModal()">Cancel</button>
            <button type="submit" class="submit-btn">Record Attendance</button>
        </div>
    </form>
</div>


<!-- scroipts -->
<script src="../../public/js/rfidPhase.js"></script>
</body>

</html>
