(function () {
  'use strict';
  if (!window.Chart || !window.AUTOSOFT_DASH) return;
  var D = window.AUTOSOFT_DASH;

  Chart.defaults.font.family = "Archivo, system-ui, sans-serif";
  Chart.defaults.color = '#646C76';

  var leadsEl = document.getElementById('chart-leads');
  if (leadsEl) {
    new Chart(leadsEl, {
      type: 'line',
      data: {
        labels: D.leadsDays,
        datasets: [{
          label: 'Contactos',
          data: D.leadsValues,
          borderColor: '#DA1E2F',
          backgroundColor: 'rgba(218,30,47,0.12)',
          tension: 0.35,
          fill: true,
          pointRadius: 2,
          pointHoverRadius: 4,
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, ticks: { precision: 0, stepSize: 1 }, grid: { color: '#F4F5F6' } },
          x: { grid: { display: false } }
        }
      }
    });
  }

  var statusEl = document.getElementById('chart-status');
  if (statusEl && D.status.length) {
    new Chart(statusEl, {
      type: 'doughnut',
      data: {
        labels: D.status.map(function (s) { return s.label; }),
        datasets: [{
          data: D.status.map(function (s) { return s.n; }),
          backgroundColor: D.status.map(function (s) { return s.color; }),
          borderColor: '#fff',
          borderWidth: 2
        }]
      },
      options: {
        cutout: '62%',
        plugins: { legend: { display: false } }
      }
    });
  }

  var brandsEl = document.getElementById('chart-brands');
  if (brandsEl && D.brandLabels.length) {
    new Chart(brandsEl, {
      type: 'bar',
      data: {
        labels: D.brandLabels,
        datasets: [{
          label: 'Viaturas',
          data: D.brandValues,
          backgroundColor: '#DA1E2F',
          borderRadius: 6,
          maxBarThickness: 36
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, ticks: { precision: 0, stepSize: 1 }, grid: { color: '#F4F5F6' } },
          x: { grid: { display: false } }
        }
      }
    });
  }
})();
