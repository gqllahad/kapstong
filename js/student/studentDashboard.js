const sideBar = document.querySelector('.sidebar');
const mainContent = document.querySelector('.content');

// menu profile
const menuToggle = document.getElementById("menuToggle");
const profileMenu = document.getElementById("profileMenu");

const navBar = document.querySelector('.navbar');

const menuItems = document.querySelectorAll('.menu li');

const overlay = document.getElementById('overlay');

// profile modal
const openProfileBtn = document.getElementById('openProfileBtn');
const openProfilePendingBtn = document.getElementById('openProfilePendingBtn');

const profilePendingModal = document.getElementById('profilePendingModal');
const profileModal = document.getElementById('profileModal');

const openProfileVerifiedBtn = document.getElementById('openProfileVerifiedBtn');
const profileVerifiedModal = document.getElementById('profileVerifiedModal');

const profileUpload = document.getElementById('profileUpload');
const profilePreview = document.getElementById('profilePreview');



const profileContainer = document.querySelector('.profile-picture-container');
// const profileInput = document.getElementById('profileInput');
// const profilePic = document.getElementById('profilePic');
const profileInput = document.getElementById('profileInput');
const profileForm = document.getElementById('profileUpload');

const closeModalProfileBtn = document.getElementById('closeProfileModal');
const closeModalProfilePendingBtn = document.getElementById('closeProfilePendingModal');

// Account Settings
const openAccountSettings = document.getElementById('openAccountSettingsBtn');
const accountSettings = document.getElementById('account-settings');

const changePasswordModal = document.getElementById('change-password-modal');
const forgotPinModal = document.getElementById('forgot-pin-modal');


const closeModalAccountBtn = document.getElementById('closeAccountModal');


// layouts feautres verified student
const studentDashboardBtn = document.getElementById("student-dashboard-btn");
const studentTasksBtn = document.getElementById("student-tasks-btn");
const studentDocumentsBtn = document.getElementById("student-documents-btn");

const studentDashboard = document.getElementById("student-dashboard");
const studentTasks = document.getElementById("student-tasks");
const studentDocuments = document.getElementById("student-documents");

// unverified Students

// layouts 
const unverifiedStudentDashboardButton = document.getElementById("unverified-student-dashboard-button");
const unverifiedStudentDocumentsButton = document.getElementById("unverified-student-documents-button");

const unverifiedStudentDashboard = document.getElementById("unverified-student-dashboard");
const unverifiedStudentDocuments = document.getElementById("unverified-student-documents");

// uploads/ preview
const uploadBtnNow = document.getElementById('btn-upload-now');
const cancelUploadNow = document.getElementById('cancelUploadModal');

const changeFilesBtn = document.getElementById('btn-change-files');
const removeFilesBtn = document.getElementById('btn-remove-files');

const unverifiedPreview = document.getElementById("closeUnverifiedImagePreview");
const verifiedPreview = document.getElementById("closeImagePreview");

const uploadModal = document.getElementById('uploadModal');
const closeModalBtn = document.getElementById('closeModal');
const cancelModalBtn = document.getElementById('cancelModal');

const previewFilesBtn = document.getElementById('btn-preview');
const previewFilesModal = document.getElementById('previewFilesModal');
const closePreviewModalBtn = document.getElementById('closePreviewModal');

// edit info
const editInfo = document.getElementById("btn-edit");
const editModal = document.getElementById('editModal');

const editForm = document.querySelector('#editModal form');

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

function previewImageUnverified(src){
    const modal = document.getElementById("imageUnverifiedPreviewModal");
    const img = document.getElementById("previewUnverifiedImg");

    img.src = src;
    modal.style.display = "flex";
}

function closeEditModal() {
    overlay.classList.remove('show');
    editModal.classList.remove('show');

    editForm.reset();
}

// unverified

function handleFilePreview(inputElem, previewElem) {
    inputElem.addEventListener('change', () => {
        const file = inputElem.files[0];
        previewElem.innerHTML = '';

        if (!file) return;
        
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

// response modal
function showResponseModal(message, type = 'success') {
    const modal = document.getElementById("responseModal");
    const msg = document.getElementById("responseMessage");

    msg.innerText = message;
    
    modal.classList.remove("show");

    void modal.offsetWidth;

    setTimeout(() => {
        modal.classList.add("show");
    }, 10);

    setTimeout(() => {
        closeResponseModal();
    }, 2000);
}

function closeResponseModal() {
    const modal = document.getElementById("responseModal");
    modal.classList.remove("show");
}

function togglePassword(inputId, icon) {
    const input = document.getElementById(inputId);

    if (input.type === "password") {
        input.type = "text";
        icon.textContent = "Show"; 
    } else {
        input.type = "password";
        icon.textContent = "Hide";
    }
};

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
                        pointHoverRadius: 6,
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

// Account Settings 

if(openAccountSettings){
openAccountSettings.addEventListener("click", () => {
    overlay.classList.add("show");
    accountSettings.classList.add("show");
});

closeModalAccountBtn.addEventListener('click', () => {
    overlay.classList.remove('show');
    accountSettings.classList.remove('show');
});


document.getElementById('openChangePassword').addEventListener('click', () => {
    accountSettings.classList.remove('show');
    changePasswordModal.classList.add('show');
});

document.getElementById('openForgotPIN').addEventListener('click', () => {
    accountSettings.classList.remove('show');
    forgotPinModal.classList.add('show');
});

document.getElementById('backToAccountSettings1').addEventListener('click', () => {
    changePasswordModal.classList.remove('show');
    accountSettings.classList.add('show');
});

document.getElementById('backToAccountSettings2').addEventListener('click', () => {
    forgotPinModal.classList.remove('show');
    accountSettings.classList.add('show');
});

document.getElementById('closeAccountModal').addEventListener('click', () => {
    accountSettings.classList.remove('show');
});
}

// sideMenu galaw
sideBar.addEventListener('mouseenter', () => {
    mainContent.classList.add('shifted');
    navBar.classList.add('shifted');
});

sideBar.addEventListener('mouseleave', () => {
    mainContent.classList.remove('shifted');
    navBar.classList.remove('shifted');
});

// unverified features

// layouts

if(unverifiedStudentDashboardButton){
    unverifiedStudentDashboardButton.addEventListener("click", () => {

    unverifiedStudentDashboard.style.display = "block";
    unverifiedStudentDocuments.style.display = "none";

    });
}

if(unverifiedStudentDocumentsButton){
    unverifiedStudentDocumentsButton.addEventListener("click", () => {

    unverifiedStudentDashboard.style.display = "none";
    unverifiedStudentDocuments.style.display = "block";

    });
}

// dashboard features
// edit info 

if(editInfo){

editInfo.addEventListener('click', () => {
    overlay.classList.add('show');
    editModal.classList.add('show');

     initialFormData = new FormData(editForm);

});

editForm.addEventListener('submit', function (e) {
    let currentFormData = new FormData(editForm);

    let changed = false;

    for (let [key, value] of currentFormData.entries()) {
        if (value !== initialFormData.get(key)) {
            changed = true;
            break;
        }
    }

    if (!changed) {
        alert("No changes were made.");
        e.preventDefault();
        return;
    }

    const confirmSave = confirm("Are you sure you want to save changes?");
    if (!confirmSave) {
        e.preventDefault();
    }
});

document.addEventListener("DOMContentLoaded", function() {
    const courseSelect = document.getElementById('edit_course');

    if (!courseSelect) return;

    const currentCourse = courseSelect.dataset.current;

    fetch('../../getCourses.php')
        .then(res => res.json())
        .then(data => {

            if (data.acro && data.values) {
                data.acro.forEach((acro, index) => {
                    const option = document.createElement('option');
                    option.value = acro;
                    option.textContent = data.values[index];

                    if (acro === currentCourse) {
                        option.selected = true;
                    }

                    courseSelect.appendChild(option);
                });
            }
        })
        .catch(err => console.error("Error loading courses:", err));
});

closeEditModalBtn.addEventListener('click', closeEditModal);
cancelEditModalBtn.addEventListener('click', closeEditModal);

// edit info
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
}

if(unverifiedPreview){
    unverifiedPreview.addEventListener("click", () => {
    document.getElementById("imageUnverifiedPreviewModal").style.display = "none";
});
}

// profiel modal

// unverified profile

if (openProfilePendingBtn && profilePendingModal) {
    openProfilePendingBtn.addEventListener("click", () => {
        overlay.classList.add("show");
        profilePendingModal.classList.add("show");
    });
}

if (closeModalProfilePendingBtn && profilePendingModal) {
    closeModalProfilePendingBtn.addEventListener('click', () => {
        overlay.classList.remove('show');
        profilePendingModal.classList.remove('show');
    });
}

// unverified profile

if(openProfileBtn){
openProfileBtn.addEventListener("click", () => {

    overlay.classList.add("show");
    profileModal.classList.add("show");

});

closeModalProfileBtn.addEventListener('click', () => {
    overlay.classList.remove('show');
    profileModal.classList.remove('show');
});

// profileContainer.addEventListener('click', () => {
//     profileInput.click();
// });
}

// not included
// profileInput.addEventListener('change', function (event) {
//     const file = event.target.files[0];

//     if (file) {
//         // Check file type (MIME)
//         if (file.type !== "image/jpeg") {
//             alert("Only JPG images are allowed.");
//             profileInput.value = ""; // reset input
//             return;
//         }

//         const reader = new FileReader();

//         reader.onload = function (e) {
//             profilePic.src = e.target.result;
//         };

//         reader.readAsDataURL(file);
//     }
// });

// function uploadProfilePic() {
//     const input = document.getElementById('profileInput');
//     if (input.files.length === 0) return;

//     const file = input.files[0];

//     // Accept JPG and PNG
//     if (!file.type.match('image/jpeg') && !file.type.match('image/png')) {
//         alert("Only JPG or PNG files are allowed!");
//         input.value = "";
//         return;
//     }

//     const formData = new FormData();
//     formData.append('profileImage', file);

//     fetch('../php/student/student_functions/profile_upload.php', {
//         method: 'POST',
//         body: formData
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.success) {
//             document.getElementById('profilePic').src = data.url + '?' + new Date().getTime();
//         } else {
//             alert(data.message);
//             input.value = ""; // reset input on error
//         }
//     })
//     .catch(err => {
//         console.error(err);
//         alert("Upload failed!");
//         input.value = ""; // reset input on error
//     });
// }

if(profileInput){
profileInput.addEventListener('change', () => {
    if (profileInput.files.length > 0) {
        profileForm.submit();
    }
});
}


// verified students

if(studentDashboardBtn){
    studentDashboardBtn.addEventListener("click", () => {

        studentTasks.style.display = "none";
        studentDocuments.style.display = "none";

        studentDashboard.style.display = "block";

    });
}

if(studentTasksBtn){
    studentTasksBtn.addEventListener("click", () => {

        studentDashboard.style.display = "none";
        studentDocuments.style.display = "none";

        studentTasks.style.display = "block";
        
    });
}

if(studentDocumentsBtn){
    studentDocumentsBtn.addEventListener("click", () => {

        studentDashboard.style.display = "none";
        studentTasks.style.display = "none";

        studentDocuments.style.display = "block";
        
    });
}

if(verifiedPreview){
    verifiedPreview.addEventListener("click", () => {
        document.getElementById("imagePreviewModal").style.display = "none";
    });
}


if(overlay){
overlay.addEventListener('click', () => {
    overlay.classList.remove('show');

    uploadModal?.classList.remove('show');
    previewFilesModal?.classList.remove('show');
    editModal?.classList.remove('show');
    profileModal?.classList.remove('show');

    accountSettings?.classList.remove("show");
    changePasswordModal?.classList.remove('show');
    forgotPinModal?.classList.remove('show');

    if (editForm) editForm.reset();
});
};

if(unverifiedStudentDashboard){
    handleFilePreview(document.getElementById('idUpload'), document.getElementById('idPreview'));
    handleFilePreview(document.getElementById('regFormUpload'), document.getElementById('regPreview'));
}


// verified students
if(studentDashboardBtn){
window.addEventListener("DOMContentLoaded", () => {
    loadLineChart();
    loadPieChart();
});

document.addEventListener("DOMContentLoaded", () => {
    const bars = document.querySelectorAll(".progress-bar-fill");
    bars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = "0%";
        setTimeout(() => {
            bar.style.width = width;
        }, 100);
    });
});
};
