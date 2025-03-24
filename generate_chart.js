const { createCanvas } = require('canvas');
const fs = require('fs');

const chartData = JSON.parse(fs.readFileSync('chart_data.json', 'utf8'));
const canvas = createCanvas(800, 400);
const ctx = canvas.getContext('2d');

const Chart = require('chart.js/auto');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: chartData.labels,
        datasets: [{
            label: 'Ventas Totales',
            data: chartData.data,
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// Guardar la imagen
const buffer = canvas.toBuffer('image/png');
fs.writeFileSync('ventas_chart.png', buffer);
console.log('Grafica generada: ventas_chart.png');
