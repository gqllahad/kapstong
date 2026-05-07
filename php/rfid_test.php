

<?php if(isset($_SESSION['status'])): ?>

<?php unset($_SESSION['status']); ?>
<?php endif; ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>OJT Attendance</title>
     <link rel="stylesheet" href="../css/rfidPhase.css" />
     <meta name="description" content="Kapstong for you and me!" />
</head>

<body>

<div class="container"> 
    <div class="header">
    <a href="loginPhase.php"><img class="scanner-logo" src="../kapstongImage/logo.jpg" alt="Logo"></a>
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

<div id="statusModal" class="modal">
    <div class="modal-content">
        <div class="modal-icon" id="modalIcon">⚠️</div>

        <div id="modalText"></div>
        <button onclick="closeModal()">OK</button>

    </div>
</div>


<!-- scroipts -->
<script>
const input = document.getElementById("rfidInput");
let timer = null;
let isSubmitting = false;
let lastScanTime = 0;

function updateClock() {
    const el = document.getElementById("clock");
    if (!el) return;

    const now = new Date();
    el.innerText = now.toLocaleTimeString();
}

function showModal(message, isSuccess = true) {
    const modal = document.getElementById("statusModal");
    const text = document.getElementById("modalText");
    const icon = document.getElementById("modalIcon");

    text.innerText = message;
    if (isSuccess) {
        icon.innerText = "✅";
        text.style.color = "#00a86b";
    } else {
        icon.innerText = "⚠️";
        text.style.color = "#e63946";
    }

    modal.classList.add("show");

    setTimeout(closeModal, 2200);
}

function closeModal() {
    document.getElementById("statusModal").classList.add("hide");

    setTimeout(() => {document.getElementById("statusModal").classList.remove("show")
        document.getElementById("statusModal").classList.remove("hide")
    } , 100);
    input.value = "";
    input.focus();
}

input.focus();
document.addEventListener("click", () => input.focus());

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

    showModal(data, isSuccess);

    input.value = "";
    input.focus();

    isSubmitting = false;
})
.catch(err => {
    showModal("❌ SERVER ERROR", false);
    isSubmitting = false;
});
        }

    }, 200);
});

window.onload = function () {
    updateClock();
    setInterval(updateClock, 1000);
};
</script>
</body>

</html>
