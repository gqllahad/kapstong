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

const adminDashboard = document.getElementById('admin-dashboard');
const adminApproval = document.getElementById('admin-approval');

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

// modals
const overlay = document.getElementById("overlay");

const allStudent = document.getElementById("all-student-modal");
const allStudentBtn = document.getElementById("viewAllStudentsBtn");
const allStudentClose = document.getElementById("closeAllStudentModal");
const allStudentBody = document.getElementById("allStudentBody");

const studentApplicationView = document.getElementById("student-application-view");
const studentApplicationViewBtn = document.getElementById("student-application-view-btn");
const studentApplicationClose = document.getElementById("closeStudentViewModal");
let previousModal = null;

// const modal = document.getElementById("imagePreviewModal");
// const img = document.getElementById("previewImg");

// Functions 

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

// pie chart
function loadPieChart() {
    fetch("../../php/admin/functions/getChartData.php")
        .then(res => res.json())
        .then(data => {

            const ctx = document.getElementById('pieChart2');

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
});

adminApprovalBtn.addEventListener("click", () => {

    adminApproval.style.display = "block";

    adminDashboard.style.display = "none";
});

document.querySelector(".search-container form")
.addEventListener("submit", function (e) {
    e.preventDefault();
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
});

allStudentBtn.addEventListener("click", () => {
    overlay.classList.add("show");

    allStudent.classList.add("show");
});

allStudentClose.addEventListener("click", () => {
    overlay.classList.remove("show");
    allStudent.classList.remove("show");

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
            body: "search=" + encodeURIComponent(value)
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

studentApplicationClose.addEventListener("click", () => {
    overlay.classList.remove("show");
    studentApplicationView.classList.remove("show");
});

function viewUser(studentID, source) {

    previousModal = source;

    if (source === "allStudent") {
        allStudent.classList.remove("show");
    }
    
    overlay.classList.add("show");
    studentApplicationView.classList.add("show");

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

function closeModal() {

   studentApplicationView.classList.remove("show");

    if (previousModal === "allStudent") {
        allStudent.classList.add("show");
        overlay.classList.add("show");
    } else {
        overlay.classList.remove("show");
    }

    previousModal = null;
}

function previewImage(src) {
    document.getElementById("previewImg").src = src;
    document.getElementById("imagePreviewModal").style.display = "flex";
}

document.addEventListener("click", function (e) {

    // CLOSE IMAGE MODAL
    if (e.target.id === "closeImagePreview") {
        document.getElementById("imagePreviewModal").style.display = "none";
        document.getElementById("previewImg").src = "";
    }

    // OPTIONAL: click outside image closes modal
    if (e.target.id === "imagePreviewModal") {
        document.getElementById("imagePreviewModal").style.display = "none";
        document.getElementById("previewImg").src = "";
    }
})







