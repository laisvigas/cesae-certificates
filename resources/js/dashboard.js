import Chart from 'chart.js/auto';

function renderLine(ctx, labels, data){
  return new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Certificados',
        data,
        tension: 0.35,
        fill: true,
        backgroundColor: 'rgba(99, 102, 241, 0.12)',
        borderColor: 'rgba(79, 70, 229, 1)',
        borderWidth: 2,
        pointRadius: 2,
        pointHoverRadius: 4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
      scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { precision: 0 } } }
    }
  });
}

function renderDonut(ctx, values){
  return new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Futuros','A decorrer','Passados'],
      datasets: [{
        data: values,
        backgroundColor: [
          'rgba(99, 102, 241, 0.85)',
          'rgba(16, 185, 129, 0.85)',
          'rgba(251, 191, 36, 0.85)'
        ],
        borderWidth: 0
      }]
    },
    options: {
      cutout: '60%',
      plugins: { legend: { display: false } }
    }
  });
}

document.addEventListener('DOMContentLoaded', () => {
  const lineEl = document.getElementById('certificatesLine');
  if (lineEl) {
    const labels = JSON.parse(lineEl.dataset.labels || '[]');
    const series = JSON.parse(lineEl.dataset.series || '[]');
    lineEl.parentElement.style.height = '280px';
    renderLine(lineEl.getContext('2d'), labels, series);
  }

  const donutEl = document.getElementById('eventsDonut');
  if (donutEl) {
    const values = JSON.parse(donutEl.dataset.values || '[]');
    donutEl.parentElement.style.height = '260px';
    renderDonut(donutEl.getContext('2d'), values);
  }
});
