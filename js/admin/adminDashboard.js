const sideBar = document.querySelector('.sidebar');
const mainContent = document.querySelector('.content');
const navBar = document.querySelector('.navbar');

// menu profile
const menuToggle = document.getElementById("menuToggle");
const profileMenu = document.getElementById("profileMenu");

const menuItems = document.querySelectorAll('.menu li');

// layouts
const adminDashboardBtn = document.getElementById('admin-dashboard-btn');
const adminApprovalBtn = document.getElementById('admin-approval-btn');
const adminPreparationBtn = document.getElementById('admin-preparation-btn');

const adminDashboard = document.getElementById('admin-dashboard');
const adminApproval = document.getElementById('admin-approval');
const adminPreparation = document.getElementById('admin-preparation');

// colors
const statusColors = {
    good: "#6c5eec",     
    warning: "#1b2cc4",  
    danger: "#dc3545"    
};

// bar chart
const xArray = ["Italy", "France", "Spain", "USA", "Argentina"];
const yArray = [55, 49, 44, 24, 15];

const data = [{
    x: xArray,
    y: yArray,
    type: "bar",
}];

const layout = {
    title: "World Wide Wine Production"
};

// search tables
let searchTimer;
const tableBody = document.getElementById("approvalTableBody");
const unTableBody = document.getElementById("unverifiedTableBody");
const allStudentBody = document.getElementById("allStudentBody");
const allSupervisorBody = document.getElementById("allSupervisorBody");
const activityLogBody = document.getElementById("activityLogTableBody");

// modals
const overlay = document.getElementById("overlay");

const allStudent = document.getElementById("all-student-modal");
const allStudentBtn = document.getElementById("viewAllStudentsBtn");
const allStudentClose = document.getElementById("closeAllStudentModal");


const studentApplicationView = document.getElementById("student-application-view");
// const studentApplicationViewBtn = document.getElementById("student-application-view-btn");
// const studentApplicationClose = document.getElementById("closeStudentViewModal");
let previousModal = null;

const studentApplicationApprove = document.getElementById("student-application-approve");
// const studentApplicationApproveBtn = document.getElementById("student-application-approve-btn");
// const studentApplicationApproveClose = document.getElementById("closeStudentApproveModal");

const studentApplicationReject = document.getElementById("student-application-reject");
// const studentApplicationRejectBtn = document.getElementById("student-application-reject-btn");
// const studentApplicationRejectClose = document.getElementById("closeStudentRejectModal");

const allUnverified = document.getElementById("all-unverified-modal");
const allUnverifiedBtn = document.getElementById("viewAllUnverifiedBtn");
const allUnverifiedClose = document.getElementById("closeAllUnverifiedModal");

const allSupervisor = document.getElementById("all-supervisor-modal");
const allSupervisorBtn = document.getElementById("viewAllSupervisorBtn");
const allSupervisorClose = document.getElementById("closeAllSupervisorModal");

const superCreate = document.getElementById("supervisor-container");
const superCreateBtn = document.getElementById("supervisor-btn");
const superCloseBtn = document.getElementById("closeCreateSupervisorModal");

const superView = document.getElementById("supervisor-view");
// const superAssignView = document.getElementById("supervisor-assigned-view");

const AssignStudent = document.getElementById("assign-student-container");
const AssignStudentBtn = document.getElementById("assign-student-btn");
const AssignCloseBtn = document.getElementById("closeAssignModal");

let selectedStudentIDs = [];
let selectedSupervisorID = null;

// form
const supervisorForm = document.getElementById("createSupervisorForm");

const messageBox = document.getElementById("formMessage");

const assignForm = document.getElementById("assignStudentSupervisorForm");
const studentList = document.getElementById("studentList");
const supervisorList = document.getElementById("supervisorList");

let activitySearchTimer;
let assignSearchTimer;


// Functions 

function viewUser(studentID, source) {

    previousModal = source;

    if (source === "allStudent") {
        allStudent.classList.remove("show");
        studentApplicationView.classList.add("show");
    }
    if(source === "UnverifiedStudent"){
        allUnverified.classList.remove("show");
        studentApplicationView.classList.add("show");
    }
    if(source === "supervisorView"){
        superView.classList.remove("show");
        studentApplicationView.classList.add("show");
    }
    if(source === "main"){
        studentApplicationView.classList.add("show");
    }
    
    overlay.classList.add("show");

        studentApplicationView.innerHTML = '';

    
    fetch("functions/getStudentData.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "studentID=" + encodeURIComponent(studentID)
    })
    .then(res => res.text())
    .then(data => {
        studentApplicationView.innerHTML = data;
    });
};

function viewSupervisor(superID) {
    
    allSupervisor.classList.remove("show");
    superView.classList.add("show");
    overlay.classList.add("show");

    superView.innerHTML = '';

    fetch("functions/getSupervisorData.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "superID=" + encodeURIComponent(superID)
    })
    .then(res => res.text())
    .then(data => {
        superView.innerHTML = data;
    });
}

function approveUser(studentID) {
    
    overlay.classList.add("show");
    studentApplicationApprove.classList.add("show");

    studentApplicationApprove.innerHTML = '';
    
    fetch("functions/getStudentApproveData.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "studentID=" + encodeURIComponent(studentID)
    })
    .then(res => res.text())
    .then(data => {
        studentApplicationApprove.innerHTML = data;

        const checkbox = document.getElementById("confirmApprove");
        const btn = document.getElementById("approveBtn");

        if (checkbox && btn) {
            checkbox.addEventListener("change", () => {
                btn.disabled = !checkbox.checked;
            });
        }
    });
};

function approveStudent(studentID) {
    if (!confirm("Are you sure you want to approve this student?")) return;

    fetch("functions/approveStudent.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "studentID=" + encodeURIComponent(studentID)
    })
    .then(res => res.text())
    .then(response => {
        alert("Student approved successfully!");

        closeApproveModal();
        location.reload();
    })
    .catch(err => console.error(err));
}

function rejectUser(studentID) {
    
    overlay.classList.add("show");
    studentApplicationReject.classList.add("show");

    studentApplicationReject.innerHTML = '';
    
    fetch("functions/getStudentRejectData.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "studentID=" + encodeURIComponent(studentID)
    })
    .then(res => res.text())
    .then(data => {
        studentApplicationReject.innerHTML = data;

        const checkbox = document.getElementById("confirmReject");
        const btn = document.getElementById("rejectBtn");

        if (checkbox && btn) {
            checkbox.addEventListener("change", () => {
                btn.disabled = !checkbox.checked;
            });
        }
    });
};

function rejectStudent(studentID) {

    const reason = document.getElementById("rejectReason").value;
    const note = document.getElementById("rejectNote").value;

    if (!reason) {
        alert("Please select a rejection reason");
        return;
    }

    if (!confirm("Are you sure you want to reject this student?")) return;

    fetch("functions/rejectStudent.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "studentID=" + encodeURIComponent(studentID)
    })
    .then(res => res.text())
    .then(response => {
        alert("Student rejected successfully!");

        closeRejectModal();
        location.reload();
    })
    .catch(err => console.error(err));
}

function closeSuperViewModal() {

    superView.classList.remove("show");
     allSupervisor.classList.add("show");

    overlay.classList.add("show");
    
}

function closeApproveModal() {

    studentApplicationApprove.classList.remove("show");
    overlay.classList.remove("show");
    
}

function closeRejectModal() {

    studentApplicationReject.classList.remove("show");
    overlay.classList.remove("show");
    
}

function closeModal() {

   studentApplicationView.classList.remove("show");

    if (previousModal === "allStudent") {
        allStudent.classList.add("show");
        overlay.classList.add("show");
    } else if(previousModal === "UnverifiedStudent") {
        allUnverified.classList.add("show");
        overlay.classList.add("show");
    }else if(previousModal === "supervisorView"){
        superView.classList.add("show");
        overlay.classList.add("show");

    }else{
        overlay.classList.remove("show");
    }

    previousModal = null;
}

function initSupervisorForm() {
    const checkbox = document.getElementById("confirmCreateSupervisor");
    const btn = document.getElementById("createSupervisorBtn");

    if (!checkbox || !btn) return;

    checkbox.addEventListener("change", () => {
        btn.disabled = !checkbox.checked;
    });
}

function previewImage(src) {
    document.getElementById("previewImg").src = src;
    document.getElementById("imagePreviewModal").style.display = "flex";
}

document.addEventListener("click", function (e) {

    if (e.target.id === "closeImagePreview") {
        document.getElementById("imagePreviewModal").style.display = "none";
        document.getElementById("previewImg").src = "";
    }

    if (e.target.id === "imagePreviewModal") {
        document.getElementById("imagePreviewModal").style.display = "none";
        document.getElementById("previewImg").src = "";
    }
});

function toggleSection(header) {
    const card = header.parentElement;

    document.querySelectorAll(".info-card").forEach(c => {
        if (c !== card) c.classList.remove("active");
    });

    card.classList.toggle("active");
}

function getStatusColor(value) {
    if (value >= 1) return statusColors.good;
    if (value >= 20) return statusColors.warning;
    return statusColors.danger;
}


function loadBarChart() {
    fetch("../../php/admin/functions/getChartData.php")
        .then(res => res.json())
        .then(data => {

            const ctx = document.getElementById('barChart');

            //colors
            const pieColors = data.labels.map(label => {
                if (label === "ADMIN") {
                    return "#5c77f0";
                } else if (label === "student") {
                    return "#4e69e0";
                }
                return "#6c757d";
            });

            if (window.barChartInstance) {
                window.barChartInstance.destroy();
            }

            window.barChartInstance = new Chart(ctx, {
                type: 'bar',
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
                    maintainAspectRatio: false,
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

// line chart
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
                    maintainAspectRatio: false,
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


// pie chart
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

            if (window.pieChartInstance) {
                window.pieChartInstance.destroy();
            }

            window.pieChartInstance = new Chart(ctx, {
                type: 'doughnut',
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
                    },
                    cutout : '70%'
                }
            });

        })
        .catch(err => console.error("Pie chart error:", err));
}

// bar chart (go)
// document.addEventListener("DOMContentLoaded", loadChart, loadPieChart);
// setInterval(loadChart, 5000);

document.addEventListener("DOMContentLoaded", () => {
    loadBarChart();   
    loadPieChart();   
    loadLineChart();
});

// menu (Upper) 
menuToggle.addEventListener("click", (e) => {
    e.stopPropagation(); 
    profileMenu.hidden = !profileMenu.hidden;
});

document.addEventListener("click", (e) => {
    if (!menuToggle.contains(e.target) && !profileMenu.contains(e.target)) {
        profileMenu.hidden = true;
    }
});


// sidebar items SIDE (active)
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

adminDashboardBtn.addEventListener("click", () => {
    adminDashboard.style.display = "block";

    adminApproval.style.display = "none";
    adminPreparation.style.display = "none";
});

adminApprovalBtn.addEventListener("click", () => {

    adminApproval.style.display = "block";

    adminDashboard.style.display = "none";
    adminPreparation.style.display = "none";
});

adminPreparationBtn.addEventListener("click", () => {

    adminPreparation.style.display = "block";

    adminDashboard.style.display = "none";
    adminApproval.style.display = "none";

});

document.getElementById("approvalSearch").addEventListener("keyup", function () {
    clearTimeout(searchTimer);

    let value = this.value;

    searchTimer = setTimeout(() => {

        tableBody.classList.add("fade-out");

        fetch("functions/searchStudent.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "search=" + encodeURIComponent(value)
        })
        .then(res => res.text())
        .then(data => {
            setTimeout(() => {
                tableBody.innerHTML = data;

                tableBody.classList.remove("fade-out");
                tableBody.classList.add("fade-in");

                setTimeout(() => {
                    tableBody.classList.remove("fade-in");
                }, 200);

            }, 200);
        });

    }, 300);
});

// modals
overlay.addEventListener('click', () => {
    overlay.classList.remove('show');

    allStudent.classList.remove("show");
    studentApplicationView.classList.remove("show");
    studentApplicationApprove.classList.remove("show");
    superCreate.classList.remove("show");
    studentApplicationReject.classList.remove("show");
     allSupervisor.classList.remove("show");
      allUnverified.classList.remove("show");
      AssignStudent.classList.remove("show");
      superView.classList.remove("show");
});

allStudentBtn.addEventListener("click", () => {
    overlay.classList.add("show");

    allStudent.classList.add("show");
});

allStudentClose.addEventListener("click", () => {
    overlay.classList.remove("show");
    allStudent.classList.remove("show");

});

allUnverifiedBtn.addEventListener("click", () => {
    overlay.classList.add("show");

    allUnverified.classList.add("show");
});

allUnverifiedClose.addEventListener("click", () => {
    overlay.classList.remove("show");
    allUnverified.classList.remove("show");
});

allSupervisorBtn.addEventListener("click", () => {
    overlay.classList.add("show");

    allSupervisor.classList.add("show");
});

allSupervisorClose.addEventListener("click", () => {
    overlay.classList.remove("show");
    allSupervisor.classList.remove("show");
});



document.getElementById("allStudentSearch").addEventListener("keyup", function () {
    clearTimeout(searchTimer);

    let value = this.value;

    searchTimer = setTimeout(() => {

        allStudentBody.classList.add("fade-out");

        fetch("functions/searchAllStudent.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "search=" + encodeURIComponent(value)+"&status=VERIFIED"
        })
        .then(res => res.text())
        .then(data => {
            setTimeout(() => {
                allStudentBody.innerHTML = data;

                allStudentBody.classList.remove("fade-out");
                allStudentBody.classList.add("fade-in");

                setTimeout(() => {
                    allStudentBody.classList.remove("fade-in");
                }, 200);

            }, 200);
        });

    }, 300);
});

document.getElementById("allSupervisorSearch").addEventListener("keyup", function () {
    clearTimeout(searchTimer);

    let value = this.value;

    searchTimer = setTimeout(() => {

        allSupervisorBody.classList.add("fade-out");

        fetch("functions/searchAllSupervisor.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "search=" + encodeURIComponent(value)+"&status=VERIFIED"
        })
        .then(res => res.text())
        .then(data => {
            setTimeout(() => {
                allSupervisorBody.innerHTML = data;

                allSupervisorBody.classList.remove("fade-out");
                allSupervisorBody.classList.add("fade-in");

                setTimeout(() => {
                    allSupervisorBody.classList.remove("fade-in");
                }, 200);

            }, 200);
        });

    }, 300);
});

document.getElementById("allUnverifiedSearch").addEventListener("keyup", function () {
    clearTimeout(searchTimer);

    let value = this.value;

    searchTimer = setTimeout(() => {

        unTableBody.classList.add("fade-out");

        fetch("functions/searchAllStudent.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "search=" + encodeURIComponent(value)+"&status=NOTVERIFIED"
        })
        .then(res => res.text())
        .then(data => {
            setTimeout(() => {
                unTableBody.innerHTML = data;

                unTableBody.classList.remove("fade-out");
                unTableBody.classList.add("fade-in");

                setTimeout(() => {
                    unTableBody.classList.remove("fade-in");
                }, 200);

            }, 200);
        });

    }, 300);
});

document.getElementById("activityLogSearch").addEventListener("keyup", function () {

    clearTimeout(activitySearchTimer);

    let value = this.value;

    activitySearchTimer = setTimeout(() => {

        activityLogBody.classList.add("fade-out");

        fetch("functions/searchActivityLog.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "search=" + encodeURIComponent(value)
        })
        .then(res => res.text())
        .then(data => {

            setTimeout(() => {
                activityLogBody.innerHTML = data;

                activityLogBody.classList.remove("fade-out");
                activityLogBody.classList.add("fade-in");

                setTimeout(() => {
                    activityLogBody.classList.remove("fade-in");
                }, 200);

            }, 200);

        });

    }, 300);
});

// bugg
// studentApplicationClose.addEventListener("click", () => {
//     overlay.classList.remove("show");
//     studentApplicationView.classList.remove("show");
// });


// studentApplicationApproveBtn.addEventListener("click", () => {
//     overlay.classList.add("show");

//     studentApplicationApprove.classList.add("show");
// });




// supervisor create
superCreateBtn.addEventListener("click", () => {
    overlay.classList.add("show");
    superCreate.classList.add("show");

    initSupervisorForm();
});

superCloseBtn.addEventListener("click", () => {
    overlay.classList.remove("show");
    superCreate.classList.remove("show");
});

supervisorForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(supervisorForm);

    fetch("functions/createSupervisor.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        messageBox.textContent = data.message;

        if (data.status === "success") {
            messageBox.style.color = "green";

            supervisorForm.reset();
            document.getElementById("createSupervisorBtn").disabled = true;

        } else {
            messageBox.style.color = "red";
        }
    })
    .catch(err => {
        messageBox.textContent = "Something went wrong.";
        messageBox.style.color = "red";
    });
});

// assign student-supervisor btn
AssignStudentBtn.addEventListener("click", () => {
    overlay.classList.add("show");
    AssignStudent.classList.add("show");

});

AssignCloseBtn.addEventListener("click", () => {
    overlay.classList.remove("show");
    AssignStudent.classList.remove("show");
});

document.getElementById("studentList").addEventListener("click", function (e) {

    const item = e.target.closest(".student-item");

    if (!item) return;

    item.classList.toggle("selected-student");

    let id = item.dataset.id;

    if (selectedStudentIDs.includes(id)) {
        selectedStudentIDs = selectedStudentIDs.filter(i => i !== id);
    } else {
        selectedStudentIDs.push(id);
    }

});

document.getElementById("supervisorList").addEventListener("click", function (e) {

    const item = e.target.closest(".supervisor-item");

    if (!item) return;

    // document.querySelectorAll(".supervisor-item")
    //     .forEach(i => i.classList.remove("selected-supervisor"));

    // item.classList.toggle("selected-supervisor");

    // selectedSupervisorID = item.dataset.id;

    const alreadySelected = item.classList.contains("selected-supervisor");

    document.querySelectorAll(".supervisor-item")
        .forEach(i => i.classList.remove("selected-supervisor"));

    if (!alreadySelected) {
        item.classList.add("selected-supervisor");
        selectedSupervisorID = item.dataset.id;
    } else {
    
        selectedSupervisorID = null;
    }

});

// search assigns
document.getElementById("studentAssignSearch").addEventListener("keyup", function () {
    clearTimeout(assignSearchTimer);

    let value = this.value;

    assignSearchTimer = setTimeout(() => {

        studentList.classList.add("fade-out");

        fetch("functions/searchAssignStudent.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "search=" + encodeURIComponent(value)
        })
        .then(res => res.text())
        .then(data => {

            setTimeout(() => {
                studentList.innerHTML = data;

                studentList.classList.remove("fade-out");
                studentList.classList.add("fade-in");

                setTimeout(() => {
                    studentList.classList.remove("fade-in");
                }, 200);

            }, 200);

        });

    }, 300);
});

document.getElementById("supervisorAssignSearch").addEventListener("keyup", function () {
    clearTimeout(assignSearchTimer);

    let value = this.value;

    assignSearchTimer = setTimeout(() => {

        supervisorList.classList.add("fade-out");

        fetch("functions/searchAssignSupervisor.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "search=" + encodeURIComponent(value)
        })
        .then(res => res.text())
        .then(data => {

            setTimeout(() => {
                supervisorList.innerHTML = data;

                supervisorList.classList.remove("fade-out");
                supervisorList.classList.add("fade-in");

                setTimeout(() => {
                    supervisorList.classList.remove("fade-in");
                }, 200);

            }, 200);

        });

    }, 300);
});

// assign submit

document.getElementById("assign-btn").addEventListener("click", function () {

    if (selectedStudentIDs.length === 0) {
        alert("Please select at least one student.");
        return;
    }

    if (!selectedSupervisorID) {
        alert("Please select a supervisor.");
        return;
    }

    const formData = new FormData();

    formData.append("superID", selectedSupervisorID);

    selectedStudentIDs.forEach(studentID => {
        formData.append("studentIDs[]", studentID);
    });

    fetch("functions/assignStudentSupervisor.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        alert(data.message);

        if (data.status === "success") {
            location.reload();
        }
    })
    .catch(err => {
        alert("Something went wrong.");
        console.log(err);
    });

});

 









