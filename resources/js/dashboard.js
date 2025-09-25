import Chart from 'chart.js/auto';

function renderLine(ctx, labels, data, datasetLabel = 'Série') {
  return new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: datasetLabel,
        data,
        tension: 0.35,
        fill: true,
        backgroundColor: 'rgba(99, 102, 241, 0.12)', // indigo-500/12
        borderColor: 'rgba(79, 70, 229, 1)',          // indigo-600
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

function renderBar(ctx, labels, data, datasetLabel = 'Inscrições') {
  return new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: datasetLabel,
        data,
        backgroundColor: 'rgba(16, 185, 129, 0.7)', // verde/emerald
        borderRadius: 4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { display: false } },
        y: { beginAtZero: true, ticks: { precision: 0 } }
      }
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
          'rgba(99, 102, 241, 0.85)',  // indigo
          'rgba(16, 185, 129, 0.85)',  // emerald
          'rgba(251, 191, 36, 0.85)'   // amber
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

// barras horizontais (tipos preferidos)
function renderBarHorizontal(ctx, labels, data){
  return new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Inscrições',
        data,
        borderWidth: 1,
        backgroundColor: 'rgba(99, 102, 241, 0.85)', // indigo
      }]
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
      scales: {
        x: { beginAtZero: true, ticks: { precision: 0 } },
        y: { ticks: { autoSkip: false } }
      }
    }
  });
}

document.addEventListener('DOMContentLoaded', () => {
  // linha: certificados
  const lineEl = document.getElementById('certificatesLine');
  if (lineEl) {
    const labels = JSON.parse(lineEl.dataset.labels || '[]');
    const series = JSON.parse(lineEl.dataset.series || '[]');
    lineEl.parentElement.style.height = '280px';
    renderLine(lineEl.getContext('2d'), labels, series, 'Certificados');
  }

  // NOVO: linha: inscrições por mês
    const enrollEl = document.getElementById('enrollmentsLine');
    if (enrollEl) {
    const labels = JSON.parse(enrollEl.dataset.labels || '[]');
    const series = JSON.parse(enrollEl.dataset.series || '[]');
    enrollEl.parentElement.style.height = '280px';
    renderBar(enrollEl.getContext('2d'), labels, series, 'Inscrições');
    }


  // donut: estado dos eventos
  const donutEl = document.getElementById('eventsDonut');
  if (donutEl) {
    const values = JSON.parse(donutEl.dataset.values || '[]');
    donutEl.parentElement.style.height = '260px';
    renderDonut(donutEl.getContext('2d'), values);
  }

  // barras horizontais: tipos preferidos
  const typeEl = document.getElementById('eventTypeBar');
  if (typeEl) {
    const labels = JSON.parse(typeEl.dataset.labels || '[]');
    const values = JSON.parse(typeEl.dataset.values || '[]');
    const h = Math.max(220, labels.length * 36);
    typeEl.parentElement.style.height = `${h}px`;
    renderBarHorizontal(typeEl.getContext('2d'), labels, values);
  }
});
