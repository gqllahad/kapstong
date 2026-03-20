const sideBar = document.querySelector('.sidebar');
const mainContent = document.querySelector('.content');
const navBar = document.querySelector('.navbar');

const menuItems = document.querySelectorAll('.menu li');

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
function loadChart() {
    fetch("../../php/admin/functions/getChartData.php")
        .then(res => res.json())
        .then(data => {

            const chartData = [{
                x: data.labels,
                y: data.values,
                type: "bar",
                text: data.values,
                textposition: "outside"
            }];

            const layout = {
                title: "Users per Role",
                yaxis : {
                    dtick : 1
                }
                
            };

            Plotly.newPlot("myPlot", chartData, layout);
        })
        .catch(err => console.error(err));
}

// bar chart (go)
document.addEventListener("DOMContentLoaded", loadChart);
setInterval(loadChart, 5000);

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

