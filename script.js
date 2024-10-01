document.getElementById('loadData').addEventListener('click', function() {
    fetch('getData.php')
        .then(response => response.json())
        .then(data => {
            const labels = data.map(item => item.nombre);
            const percentages = data.map(item => item.porcentaje);

            const ctx = document.getElementById('myChart').getContext('2d');
            const myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Porcentaje de Usuarios',
                        data: percentages,
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(255, 99, 132, 0.2)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value + '%'; // Formato de porcentaje
                                }
                            }
                        }
                    }
                }
                
            });
        })
        .catch(error => console.error('Error:', error));
});

