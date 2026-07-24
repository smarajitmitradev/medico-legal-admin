


    const ctx = document.getElementById('salesChart');

    new Chart(ctx, {

        type: 'line',

        data: {

            labels: [
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July'
            ],

            datasets: [

                {
                    label: 'Sales',

                    data: [28, 48, 40, 19, 86, 27, 90],

                    borderColor: '#0d6efd',

                    backgroundColor: 'rgba(13,110,253,0.25)',

                    fill: true,

                    tension: 0.4,

                    borderWidth: 3,

                    pointRadius: 0,

                    pointHoverRadius: 5
                },

                {
                    label: 'Digital Goods',

                    data: [65, 59, 80, 81, 56, 55, 40],

                    borderColor: '#ced4da',

                    backgroundColor: 'rgba(206,212,218,0.45)',

                    fill: true,

                    tension: 0.4,

                    borderWidth: 3,

                    pointRadius: 0
                }

            ]
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
                    display: false
                }

            },

            scales: {

                x: {

                    grid: {
                        display: false
                    }

                },

                y: {

                    beginAtZero: true,

                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    },

                    ticks: {
                        stepSize: 10
                    }

                }

            }

        }

    });

    const pieCtx = document.getElementById('pieChart');

    new Chart(pieCtx, {

        type: 'doughnut',

        data: {

            labels: [
                'Chrome',
                'IE',
                'FireFox',
                'Safari',
                'Opera',
                'Navigator'
            ],

            datasets: [{

                data: [700, 500, 400, 600, 300, 100],

                backgroundColor: [
                    '#dc3545',
                    '#198754',
                    '#ffc107',
                    '#0dcaf0',
                    '#0d6efd',
                    '#6c757d'
                ],

                borderWidth: 1
            }]
        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            plugins: {

                legend: {
                    display: false
                }

            }

        }

    });

    