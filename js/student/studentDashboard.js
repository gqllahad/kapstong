const sideBar = document.querySelector('.sidebar');
const mainContent = document.querySelector('.content');

// menu profile
const menuToggle = document.getElementById("menuToggle");
const profileMenu = document.getElementById("profileMenu");

const navBar = document.querySelector('.navbar');

const menuItems = document.querySelectorAll('.menu li');

// profile modal
const openProfileBtn = document.getElementById('openProfileBtn');
const profileModal = document.getElementById('profileModal');

const profileUpload = document.getElementById('profileUpload');
const profilePreview = document.getElementById('profilePreview');

const profileContainer = document.querySelector('.profile-picture-container');
const profileInput = document.getElementById('profileInput');
const profilePic = document.getElementById('profilePic');

const closeModalProfileBtn = document.getElementById('closeProfileModal');

// Account Settings
const openAccountSettings = document.getElementById('openAccountSettingsBtn');
const accountSettings = document.getElementById('account-settings');

const changePasswordModal = document.getElementById('change-password-modal');
const forgotPinModal = document.getElementById('forgot-pin-modal');


const closeModalAccountBtn = document.getElementById('closeAccountModal');



// unverified Students

// layouts 
const unverifiedStudentDashboardButton  = document.getElementById("unverified-student-dashboard-button");
const unverifiedStudentDocumentsButton  = document.getElementById("unverified-student-documents-button");

const unverifiedStudentDashboard  = document.getElementById("unverified-student-dashboard");
const unverifiedStudentDocuments  = document.getElementById("unverified-student-documents");

// uploads/ preview
const uploadBtnNow = document.getElementById('btn-upload-now');
const cancelUploadNow = document.getElementById('cancelUploadModal');

const changeFilesBtn = document.getElementById('btn-change-files');
const removeFilesBtn = document.getElementById('btn-remove-files');
const overlay = document.getElementById('overlay');
const uploadModal = document.getElementById('uploadModal');
const closeModalBtn = document.getElementById('closeModal');
const cancelModalBtn = document.getElementById('cancelModal');

const previewFilesBtn = document.getElementById('btn-preview');
const previewFilesModal = document.getElementById('previewFilesModal');
const closePreviewModalBtn = document.getElementById('closePreviewModal');

// edit info
const editInfo = document.getElementById("btn-edit");
const editModal = document.getElementById('editModal');
const cancelEditModalBtn = document.getElementById('cancelEditModal');
const closeEditModalBtn = document.getElementById('closeEditModal');

// unverified


// functions

// unverified

function previewImage(src) {
    const modal = document.getElementById("imagePreviewModal");
    const img = document.getElementById("previewImg");

    img.src = src;
    modal.style.display = "flex";
}

function previewImage(src) {
    const modal = document.getElementById("imagePreviewModal");
    const img = document.getElementById("previewImg");

    img.src = src;
    modal.style.display = "flex";
}


// unverified


function handleFilePreview(inputElem, previewElem) {
    inputElem.addEventListener('change', () => {
        const file = inputElem.files[0];
        previewElem.innerHTML = '';

        if (!file) return;

        // Only allow jpg/png
        const allowedTypes = ['image/jpeg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            alert('Only JPG or PNG files are allowed!');
            inputElem.value = ''; 
            return;
        }

        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.classList.add('preview-img');
        previewElem.appendChild(img);
    });
}


// profile menu
menuToggle.addEventListener("click", (e) => {
    e.stopPropagation(); 
    profileMenu.hidden = !profileMenu.hidden;
});

document.addEventListener("click", (e) => {
    if (!menuToggle.contains(e.target) && !profileMenu.contains(e.target)) {
        profileMenu.hidden = true;
    }
});

menuItems.forEach(item => {
    item.addEventListener('click', () => {

        menuItems.forEach(li => li.classList.remove('active'));

        item.classList.add('active');

    });
});

// sideMenu galaw
sideBar.addEventListener('mouseenter', () => {
      mainContent.classList.add('shifted');
      navBar.classList.add('shifted');
    });

sideBar.addEventListener('mouseleave', () => {
    mainContent.classList.remove('shifted');
    navBar.classList.remove('shifted');
});


// Chartss
function loadPieChart() {
    fetch("../../php/admin/functions/getChartData.php")
        .then(res => res.json())
        .then(data => {

            const ctx = document.getElementById('pieChart');

            //colors
            const pieColors = data.labels.map(label => {
                if (label === "ADMIN") {
                    return "#5c77f0";
                } else if (label === "student") {
                    return "#4e69e0"; 
                }
                return "#6c757d"; 
            });

            // Destroy previous chart
            if (window.pieChartInstance) {
                window.pieChartInstance.destroy();
            }

            window.pieChartInstance = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.values,
                         backgroundColor: pieColors
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

        })
        .catch(err => console.error("Pie chart error:", err));
}

function loadLineChart() {
    fetch("../../php/admin/functions/getChartData.php")
        .then(res => res.json())
        .then(data => {

            const ctx = document.getElementById('lineChart');

            //colors
            const pieColors = data.labels.map(label => {
                if (label === "ADMIN") {
                    return "#5c77f0";
                } else if (label === "student") {
                    return "#4e69e0"; 
                }
                return "#6c757d"; 
            });

            // Destroy previous chart
            if (window.lineChartInstance) {
                window.lineChartInstance.destroy();
            }

            window.lineChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Users',
                        data: data.values,
                        borderColor: '#5c77f0',
                        backgroundColor: 'rgba(92, 119, 240, 0.2)',
                        fill: true,
                        tension: 0.3,
                        pointHoverRadius : 6,
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    tooltip: {
                        enabled: true
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

        })
        .catch(err => console.error("Line chart error:", err));
}

// unverified features

// layouts

unverifiedStudentDashboardButton.addEventListener("click", () => {

    unverifiedStudentDashboard.style.display = "block";
    unverifiedStudentDocuments.style.display = "none";

});

unverifiedStudentDocumentsButton.addEventListener("click", () =>{

    unverifiedStudentDashboard.style.display = "none";
    unverifiedStudentDocuments.style.display = "block";

});




// dashboard features
editInfo.addEventListener('click', () => {
    overlay.classList.add('show');
    editModal.classList.add('show');
});

closeEditModalBtn.addEventListener('click', () => {
   overlay.classList.remove('show');
    editModal.classList.remove('show');
});

cancelEditModalBtn.addEventListener('click', () => {
   overlay.classList.remove('show');
   editModal.classList.remove('show');
});


previewFilesBtn?.addEventListener('click', () => {
    overlay.classList.add('show');
    previewFilesModal.classList.add('show');
});

closePreviewModalBtn?.addEventListener('click', () => {
    overlay.classList.remove('show');
    previewFilesModal.classList.remove('show');
});

if (uploadBtnNow) {
    uploadBtnNow.addEventListener('click', () => {
        overlay.classList.add('show');
        uploadModal.classList.add('show');
    });
}

if (cancelUploadNow) {
    cancelUploadNow.addEventListener('click', () => {
        overlay.classList.remove('show');
        uploadModal.classList.remove('show');
    });
}

changeFilesBtn?.addEventListener('click', () => {
    overlay.classList.add('show');
    uploadModal.classList.add('show'); 
    previewFilesModal.classList.remove('show'); 
});

removeFilesBtn?.addEventListener('click', () => {
    const confirmDelete = confirm("Are you sure you want to remove your uploaded documents?");
    if (!confirmDelete) return;

    fetch("delete_documents_action.php", { method: "POST" })
        .then(res => res.text())
        .then(() => window.location.reload())
        .catch(err => console.error(err));
});

closeModalBtn.addEventListener('click', () => {
   overlay.classList.remove('show');
    uploadModal.classList.remove('show');
});

document.getElementById("closeImagePreview").addEventListener("click", () => {
    document.getElementById("imagePreviewModal").style.display = "none";
});

// unverified features

// profiel modal

openProfileBtn.addEventListener("click", () => {

    overlay.classList.add("show");

    profileModal.classList.add("show");

});

closeModalProfileBtn.addEventListener('click', () => {
   overlay.classList.remove('show');
    profileModal.classList.remove('show');
});

profileContainer.addEventListener('click', () => {
    profileInput.click();
});

profileInput.addEventListener('change', (event) => {
    const file = event.target.files[0];
    if(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            profilePic.src = e.target.result; 
        }
        reader.readAsDataURL(file);

        // Optionally, here you can add an AJAX call to upload the file to the server
        // e.g., using fetch() to save the profile picture in the database
    }
});


// Account Settings 

openAccountSettings.addEventListener("click", () => {

    overlay.classList.add("show");
    accountSettings.classList.add("show");

});

closeModalAccountBtn.addEventListener('click', () => {
    overlay.classList.remove('show');
    accountSettings.classList.remove('show');
});

// document.getElementById('saveAccountSettings').addEventListener('click', () => {
//     const oldPass = document.getElementById('oldPassword').value;
//     const newPass = document.getElementById('newPassword').value;
//     const confirmPass = document.getElementById('confirmPassword').value;
//     const pin = document.getElementById('forgotPin').value;
//     const emailNotif = document.getElementById('emailNotif').checked;

//     if(newPass !== confirmPass) {
//         alert("New password and confirmation do not match!");
//         return;
//     }

//     // TODO: Send data via AJAX to PHP backend for saving
//     console.log({oldPass, newPass, pin, emailNotif});
// });

document.getElementById('openChangePassword').addEventListener('click', () => {
    accountSettings.classList.remove('show');
    changePasswordModal.classList.add('show');
});

document.getElementById('openForgotPIN').addEventListener('click', () => {
    accountSettings.classList.remove('show');
    forgotPinModal.classList.add('show');
});

// Back buttons
document.getElementById('backToAccountSettings1').addEventListener('click', () => {
    changePasswordModal.classList.remove('show');
    accountSettings.classList.add('show');
});

document.getElementById('backToAccountSettings2').addEventListener('click', () => {
    forgotPinModal.classList.remove('show');
    accountSettings.classList.add('show');
});

// Close main modal example
document.getElementById('closeAccountModal').addEventListener('click', () => {
    accountSettings.classList.remove('show');
});



overlay.addEventListener('click', () => {
    overlay.classList.remove('show');
    uploadModal.classList.remove('show');
    previewFilesModal.classList.remove('show');
    editModal.classList.remove('show');
    profileModal.classList.remove('show');

    accountSettings.classList.remove("show");
    changePasswordModal.classList.remove('show');
    forgotPinModal.classList.remove('show');
    
});


window.addEventListener("DOMContentLoaded", () => {
    loadLineChart();
    loadPieChart();
});

handleFilePreview(document.getElementById('idUpload'), document.getElementById('idPreview'));
handleFilePreview(document.getElementById('regFormUpload'), document.getElementById('regPreview'));
