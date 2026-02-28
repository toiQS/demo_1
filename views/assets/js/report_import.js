/* ================================================================
   import_export.js — Charts cho trang Thống kê Nhập xuất
   Dữ liệu nhận từ window.IE_DATA (inject bởi import_export.php)
   ================================================================ */

document.addEventListener('DOMContentLoaded', function () {

  const {
    labels,
    importData, exportData, profitData,
    supplierLabels, supplierData,
    receiptCounts,
  } = window.IE_DATA;

  /* Shared defaults */
  Chart.defaults.color       = '#8b949e';
  Chart.defaults.borderColor = 'rgba(255,255,255,0.06)';
  Chart.defaults.font.family = "var(--mono, monospace)";

  const fmtM = v => (v / 1_000_000).toFixed(0) + 'M';

  /* ── 1. Grouped Bar — Nhập vs Xuất theo tháng ── */
  new Chart(document.getElementById('chartImportExport'), {
    type: 'bar',
    data: {
      labels,
      datasets: [
        {
          label: 'Giá trị Nhập',
          data: importData,
          backgroundColor: 'rgba(88,166,255,.75)',
          borderColor: '#58a6ff',
          borderWidth: 1.5,
          borderRadius: 5,
          borderSkipped: false,
        },
        {
          label: 'Giá trị Xuất',
          data: exportData,
          backgroundColor: 'rgba(63,185,80,.75)',
          borderColor: '#3fb950',
          borderWidth: 1.5,
          borderRadius: 5,
          borderSkipped: false,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#1c2230',
          borderColor: 'rgba(255,255,255,.1)',
          borderWidth: 1,
          callbacks: { label: ctx => ` ${ctx.dataset.label}: ${fmtM(ctx.raw)}₫` },
        },
      },
      scales: {
        x: { grid: { display: false } },
        y: {
          grid: { color: 'rgba(255,255,255,.04)' },
          ticks: { callback: v => fmtM(v) + '₫' },
        },
      },
    },
  });

  /* ── 2. Line — Lợi nhuận (Xuất - Nhập) ── */
  const profCtx  = document.getElementById('chartProfit').getContext('2d');
  const gradProf = profCtx.createLinearGradient(0, 0, 0, 200);
  gradProf.addColorStop(0, 'rgba(240,165,0,.35)');
  gradProf.addColorStop(1, 'rgba(240,165,0,0)');

  new Chart(profCtx, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Lợi nhuận',
        data: profitData,
        borderColor: '#f0a500',
        backgroundColor: gradProf,
        borderWidth: 2.5,
        tension: .4,
        fill: true,
        pointRadius: 4,
        pointBackgroundColor: '#f0a500',
        pointHoverRadius: 7,
      }],
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: ctx => ` Lợi nhuận: ${fmtM(ctx.raw)}₫` } },
      },
      scales: {
        x: { grid: { display: false } },
        y: {
          grid: { color: 'rgba(255,255,255,.04)' },
          ticks: { callback: v => fmtM(v) + '₫' },
        },
      },
    },
  });

  /* ── 3. Doughnut — Tỷ trọng nhập theo nhà cung cấp ── */
  new Chart(document.getElementById('chartSupplier'), {
    type: 'doughnut',
    data: {
      labels: supplierLabels,
      datasets: [{
        data: supplierData,
        backgroundColor: [
          'rgba(88,166,255,.85)',
          'rgba(63,185,80,.85)',
          'rgba(240,165,0,.85)',
          'rgba(163,113,247,.85)',
          'rgba(248,81,73,.85)',
        ],
        borderColor: ['#58a6ff','#3fb950','#f0a500','#a371f7','#f85149'],
        borderWidth: 2,
        hoverOffset: 8,
      }],
    },
    options: {
      cutout: '60%',
      responsive: true,
      plugins: {
        legend: { position: 'bottom', labels: { boxWidth: 10, padding: 10, font: { size: 10 } } },
        tooltip: {
          callbacks: { label: ctx => ` ${ctx.label}: ${fmtM(ctx.raw)}₫` },
        },
      },
    },
  });

  /* ── 4. Bar — Số phiếu nhập mỗi tháng ── */
  new Chart(document.getElementById('chartReceipts'), {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Số phiếu nhập',
        data: receiptCounts,
        backgroundColor: 'rgba(163,113,247,.7)',
        borderColor: '#a371f7',
        borderWidth: 1.5,
        borderRadius: 6,
        borderSkipped: false,
      }],
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: ctx => ` ${ctx.raw} phiếu` } },
      },
      scales: {
        x: { grid: { display: false } },
        y: { grid: { color: 'rgba(255,255,255,.04)' }, ticks: { stepSize: 5 } },
      },
    },
  });

});

/* ── Export ── */
function exportImportExport() {
  if (typeof showToast === 'function') showToast('Đang xuất báo cáo nhập xuất...', 'info');
}
