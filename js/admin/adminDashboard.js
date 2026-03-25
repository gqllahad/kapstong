const sideBar = document.querySelector('.sidebar');
const mainContent = document.querySelector('.content');
const navBar = document.querySelector('.navbar');

const menuItems = document.querySelectorAll('.menu li');

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

// Functions 

function getStatusColor(value) {
    if (value >= 1) return statusColors.good;
    if (value >= 20) return statusColors.warning;
    return statusColors.danger;
}

function loadChart() {
    fetch("../../php/admin/functions/getChartData.php")
        .then(res => res.json())
        .then(data => {
            
            // color
            const colors = data.values.map(value => getStatusColor(value));

            // bar chart
            const barChartData = [{
                x: data.labels,
                y: data.values,
                type: "bar",
                marker: {
                    color: colors
                },
                text: data.values,
                textposition: "inside"
            }];

            const barLayout = {
                title: "Users per Role",
                yaxis : {
                    dtick : 1
                }
                
            };

            Plotly.newPlot("myPlot", barChartData, barLayout);
        })
        .catch(err => console.error(err));
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
    loadChart();   
    loadPieChart();   
});

// menu items (active)
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

