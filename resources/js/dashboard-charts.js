// Dashboard charts functionality
// Uses lazy-loaded ApexCharts via window.loadApexCharts()
document.addEventListener("DOMContentLoaded", async function () {
    // Popular Products Chart
    if (document.querySelector("#popular-products-chart")) {
        const ApexCharts = await window.loadApexCharts();
        const popularProductsData = window.popularProductsData || { data: [], labels: [] };
        const popularProductsOptions = {
            series: [{
                data: popularProductsData.data
            }],
            chart: {
                type: 'bar',
                height: 250,
                toolbar: {
                    show: false,
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: true,
                }
            },
            colors: ['#206bc4'],
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: popularProductsData.labels,
            }
        };

        const popularProductsChart = new ApexCharts(
            document.querySelector("#popular-products-chart"),
            popularProductsOptions
        );
        popularProductsChart.render();
    }

    // Monthly Revenue Chart
    if (document.querySelector("#monthly-revenue-chart")) {
        const ApexCharts = await window.loadApexCharts();
        const revenueData = window.revenueData || { data: [], labels: [] };
        const monthlyRevenueOptions = {
            series: [{
                name: 'Revenue',
                data: revenueData.data
            }],
            chart: {
                height: 250,
                type: 'line',
                toolbar: {
                    show: false,
                }
            },
            colors: ['#2fb344'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            xaxis: {
                categories: revenueData.labels,
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return "Rp " + val.toFixed(1) + "M";
                    }
                }
            },
            markers: {
                size: 5,
                hover: {
                    size: 7
                }
            }
        };

        const monthlyRevenueChart = new ApexCharts(
            document.querySelector("#monthly-revenue-chart"),
            monthlyRevenueOptions
        );
        monthlyRevenueChart.render();
    }
});
