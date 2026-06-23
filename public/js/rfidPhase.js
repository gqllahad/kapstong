const input = document.getElementById("rfidInput");
let timer = null;
let isSubmitting = false;
let lastScanTime = 0;

let allowRfidFocus = true;

function updateClock() {
    const el = document.getElementById("clock");
    if (!el) return;

    const now = new Date();
    el.innerText = now.toLocaleTimeString();
}

function showToast(message, isSuccess = true) {
    const container = document.getElementById("toastContainer");

    const toast = document.createElement("div");
    toast.classList.add("toast", isSuccess ? "success" : "error");

    const icon = document.createElement("div");
    icon.classList.add("toast-icon");
    icon.textContent = isSuccess ? "✓" : "!";
    
    const text = document.createElement("div");
    text.classList.add("toast-message");
    text.textContent = message;

    toast.appendChild(icon);
    toast.appendChild(text);
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = "0";
        toast.style.transform = "translateX(-20px)";
        toast.style.transition = "0.3s ease";

        setTimeout(() => toast.remove(), 300);
    }, 2500);
}

// function showModal(message, isSuccess = true) {
//     const modal = document.getElementById("statusModal");
//     const text = document.getElementById("modalText");
//     const icon = document.getElementById("modalIcon");

//     text.innerText = message;
//     if (isSuccess) {
//         icon.innerText = "✅";
//         text.style.color = "#00a86b";
//     } else {
//         icon.innerText = "⚠️";
//         text.style.color = "#e63946";
//     }

//     modal.classList.add("show");

//     setTimeout(closeModal, 2200);
// }

function closeModal() {
    document.getElementById("statusModal").classList.add("hide");

    setTimeout(() => {document.getElementById("statusModal").classList.remove("show")
        document.getElementById("statusModal").classList.remove("hide")
    } , 100);
    input.value = "";
    input.focus();
}

function loadAttendance() {
    fetch("../php/rfid_functions/get_live_attendance.php")
        .then(res => res.json())
        .then(data => {
           const now = new Date();

            const formattedDate = now.toLocaleDateString('en-PH', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            let html = `

                <div class="attendance-topbar">

                    <div class="attendance-day">
                        <i class='bx bx-calendar'></i>
                        <span>${formattedDate}</span>
                    </div>

                    <div class="attendance-count">
                        <i class='bx bx-user'></i>
                        <span>${data.length} Active Records</span>
                    </div>

                </div>

                <table class="attendance-table">

                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Hours</th>
                            <th>Status</th>
                            <th>Current State</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>

                    <tbody>
            `;

            data.forEach(row => {

                let statusClass = "present";

                if (row.status?.toLowerCase().includes("late")) {
                    statusClass = "late";
                } else if (
                    row.remarks?.toLowerCase().includes("lunch")
                ) {
                    statusClass = "lunch";
                } else if (
                    row.remarks?.toLowerCase().includes("snack")
                ) {
                    statusClass = "snack";
                } else if (
                    row.remarks?.toLowerCase().includes("completed")
                ) {
                    statusClass = "timeout";
                }

                const timeIn = row.first_time_in
                    ? new Date(row.first_time_in).toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit'
                    })
                    : '-';

                const timeOut = row.final_time_out
                    ? new Date(row.final_time_out).toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit'
                    })
                    : '-';

                html += `
                    <tr>

                        <td>
                            <div class="student-cell">

                                <div class="student-avatar">
                                    ${row.name.charAt(0)}
                                </div>

                                <div class="student-info">
                                    <span class="student-name">
                                        ${row.name}
                                    </span>
                                </div>

                            </div>
                        </td>

                        <td>
                            <span class="time-badge in">
                                <i class='bx bx-log-in'></i>
                                ${timeIn}
                            </span>
                        </td>

                        <td>
                            <span class="time-badge out">
                                <i class='bx bx-log-out'></i>
                                ${timeOut}
                            </span>
                        </td>

                        <td>
                            <span class="hours-pill">
                                ${row.total_hours || 0} hrs
                            </span>
                        </td>

                        <td>
                            <span class="badge ${statusClass}">
                                ${row.status}
                            </span>
                        </td>

                        <td>
                            <span class="badge ${statusClass}">
                                ${row.current_state}
                            </span>
                        </td>

                        <td class="remarks">
                            ${row.remarks || '-'}
                        </td>

                    </tr>
                `;
            });

            html += `
                    </tbody>
                </table>
            `;

            document.getElementById("attendanceTable").innerHTML = html;
        });
}



input.focus();
document.addEventListener("click", () =>  {
        if (allowRfidFocus) {
        input.focus();
    }
});

window.addEventListener("pageshow", () => {
    input.value = "";
    isSubmitting = false;
});

input.addEventListener("input", function () {

    if (isSubmitting) return;

    clearTimeout(timer);

    timer = setTimeout(() => {

        const rfid = input.value.trim();

        const now = Date.now();
        if (now - lastScanTime < 2000) {
            input.value = "";
            return;
        }

        if (rfid !== "") {

            isSubmitting = true;
            lastScanTime = now;

            fetch("rfid_login.php", {
    method: "POST",
    headers: {
        "Content-Type": "application/x-www-form-urlencoded"
    },
    body: "rfid=" + encodeURIComponent(rfid)
})
.then(res => res.text())
.then(data => {

    const isSuccess =
        data.includes("SUCCESS") ||
        data.includes("TIME IN") ||
        data.includes("TIME OUT");

    showToast(data.message || data, isSuccess);

    input.value = "";
    input.focus();

    isSubmitting = false;
})
.catch(err => {
    showToast(data.message || data, false);
    isSubmitting = false;
});
        }

    }, 200);
});

function toggleDashboard() {
     const panel = document.getElementById("dashboardPanel");
    const table = document.getElementById("attendanceTable");
    const emergency = document.getElementById("emergency-panel");

    panel.classList.toggle("active");

    if (panel.classList.contains("active")) {
        
        emergency.style.display = "flex";

        loadAttendance();
       attendanceInterval = setInterval(loadAttendance, 5000);
    } 
    
    else {

        clearInterval(attendanceInterval);
        table.innerHTML = "";
        emergency.style.display = "none";

    }
}

function downloadTodayAttendance() {

    window.open(
        "../php/rfid_functions/download_today_attendance.php",
        "_blank"
    );
}

function submitEmergencyTimeout() {

    const rfid = document.getElementById("emergencyRfid").value;
    let reason = document.getElementById("emergencyReason").value;

    if (!rfid) return alert("Please enter RFID");
    if (!reason) {
        reason = "Emergency time out (no reason selected)";
    }

    fetch("../php/rfid_functions/emergency_timeout.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body:
            "rfid=" + encodeURIComponent(rfid) +
            "&reason=" + encodeURIComponent(reason)
    })
    .then(res => res.json())
    .then(data => {
        closeEmergencyModal();
        loadAttendance();

        showToast(data.message, data.success);
    })
    .catch(err => {
        showToast("SERVER ERROR", false);
    });
}

function openEmergencyModal() {
    allowRfidFocus = false;
    const panel = document.getElementById("dashboardPanel");
     const modal = document.getElementById("emergencyModal");
     const emergency = document.getElementById("emergency-panel");
    const input = document.getElementById("emergencyRfid");
    const table = document.getElementById("attendanceTable");
    const overlay = document.getElementById("dashboardOverlay");

     panel.classList.remove("active");
    clearInterval(attendanceInterval);
    table.innerHTML = "";
    emergency.style.display = "none";

     modal.classList.add("show");
     overlay.classList.add("show");

    setTimeout(() => {
        input.focus();
    }, 50);
}

function closeEmergencyModal() {
    const overlay = document.getElementById("dashboardOverlay");
    document.getElementById("emergencyModal").classList.remove("show");
    document.getElementById("emergencyRfid").value = "";
    overlay.classList.remove("show");

     allowRfidFocus = true;
    input.focus();
}

function setReason(text) {
    const reasonInput = document.getElementById("emergencyReason");
    reasonInput.value = text;
}

document.getElementById("emergencyModal").addEventListener("click", (e) => {
    e.stopPropagation();
});


window.onload = function () {
    updateClock();
    setInterval(updateClock, 1000);
};