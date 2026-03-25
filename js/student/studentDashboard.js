const sideBar = document.querySelector('.sidebar');
const mainContent = document.querySelector('.content');

// menu profile
const menuToggle = document.getElementById("menuToggle");
const profileMenu = document.getElementById("profileMenu");

const navBar = document.querySelector('.navbar');

const menuItems = document.querySelectorAll('.menu li');

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

window.addEventListener("DOMContentLoaded", () => {
    loadLineChart();
    loadPieChart();
});