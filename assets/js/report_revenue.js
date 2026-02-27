/* ================================================================
   revenue.js — Charts cho trang Thống kê Doanh thu
   Dữ liệu nhận từ window.REVENUE_DATA (inject bởi revenue.php)
   ================================================================ */

document.addEventListener('DOMContentLoaded', function () {

  const {
    labels, revenueData, orderData,
    categoryData, categoryLabels,
    donutData, donutLabels,
  } = window.REVENUE_DATA;

  /* Shared defaults */
  Chart.defaults.color       = '#8b949e';
  Chart.defaults.borderColor = 'rgba(255,255,255,0.06)';
  Chart.defaults.font.family = "var(--mono, monospace)";

  const fmtM  = v => (v / 1_000_000).toFixed(0) + 'M';
  const fmtB  = v => v >= 1e9 ? (v / 1e9).toFixed(2) + 'B' : fmtM(v);

  /* ── 1. Line chart — Doanh thu theo tháng ── */
  const lineCtx = document.getElementById('chartRevenue').getContext('2d');

  const gradRev = lineCtx.createLinearGradient(0, 0, 0, 270);
  gradRev.addColorStop(0, 'rgba(240,165,0,.38)');
  gradRev.addColorStop(1, 'rgba(240,165,0,0)');

  const gradOrd = lineCtx.createLinearGradient(0, 0, 0, 270);
  gradOrd.addColorStop(0, 'rgba(163,113,247,.22)');
  gradOrd.addColorStop(1, 'rgba(163,113,247,0)');

  new Chart(lineCtx, {
    type: 'line',
    data: {
      labels,
      datasets: [
        {
          label: 'Doanh thu',
          data: revenueData,
          borderColor: '#f0a500',
          backgroundColor: gradRev,
          borderWidth: 2.5,
          tension: .42,
          fill: true,
          pointRadius: 4,
          pointBackgroundColor: '#f0a500',
          pointHoverRadius: 7,
          yAxisID: 'yRev',
        },
        {
          label: 'Số đơn',
          data: orderData,
          borderColor: '#a371f7',
          backgroundColor: gradOrd,
          borderWidth: 2,
          tension: .42,
          fill: true,
          pointRadius: 3,
          pointBackgroundColor: '#a371f7',
          pointHoverRadius: 5,
          yAxisID: 'yOrd',
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
          callbacks: {
            label: ctx => ctx.datasetIndex === 0
              ? ` Doanh thu: ${fmtM(ctx.raw)}₫`
              : ` Số đơn: ${ctx.raw}`,
          },
        },
      },
      scales: {
        x: { grid: { color: 'rgba(255,255,255,.04)' } },
        yRev: {
          position: 'left',
          grid: { color: 'rgba(255,255,255,.04)' },
          ticks: { callback: v => fmtM(v) + '₫', color: '#f0a500' },
        },
        yOrd: {
          position: 'right',
          grid: { display: false },
          ticks: { color: '#a371f7' },
        },
      },
    },
  });

  /* ── 2. Donut — Cơ cấu doanh thu theo danh mục ── */
  new Chart(document.getElementById('chartDonut'), {
    type: 'doughnut',
    data: {
      labels: donutLabels,
      datasets: [{
        data: donutData,
        backgroundColor: [
          'rgba(240,165,0,.85)',
          'rgba(88,166,255,.85)',
          'rgba(63,185,80,.85)',
          'rgba(163,113,247,.85)',
          'rgba(248,81,73,.85)',
        ],
        borderColor: ['#f0a500','#58a6ff','#3fb950','#a371f7','#f85149'],
        borderWidth: 2,
        hoverOffset: 8,
      }],
    },
    options: {
      cutout: '62%',
      responsive: true,
      plugins: {
        legend: { position: 'bottom', labels: { boxWidth: 10, padding: 12, font: { size: 11 } } },
        tooltip: {
          callbacks: {
            label: ctx => ` ${ctx.label}: ${fmtM(ctx.raw)}₫ (${ctx.parsed.toFixed(0)}%)`,
          },
        },
      },
    },
  });

  /* ── 3. Bar — Doanh thu từng danh mục ── */
  const barCatCtx  = document.getElementById('chartCategory').getContext('2d');
  const colors = ['#f0a500','#58a6ff','#3fb950','#a371f7','#f85149'];

  new Chart(barCatCtx, {
    type: 'bar',
    data: {
      labels: categoryLabels,
      datasets: [{
        label: 'Doanh thu',
        data: categoryData,
        backgroundColor: colors.map(c => c + 'cc'),
        borderColor: colors,
        borderWidth: 1.5,
        borderRadius: 6,
        borderSkipped: false,
      }],
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: ctx => ` ${fmtM(ctx.raw)}₫` } },
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

  /* ── 4. Line — Tốc độ tăng trưởng (%) ── */
  const growthData = revenueData.map((v, i) =>
    i === 0 ? 0 : +((v - revenueData[i - 1]) / revenueData[i - 1] * 100).toFixed(1)
  );

  const growthCtx = document.getElementById('chartGrowth').getContext('2d');
  const gradGrowth = growthCtx.createLinearGradient(0, 0, 0, 180);
  gradGrowth.addColorStop(0, 'rgba(63,185,80,.3)');
  gradGrowth.addColorStop(1, 'rgba(63,185,80,0)');

  new Chart(growthCtx, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Tăng trưởng',
        data: growthData,
        borderColor: '#3fb950',
        backgroundColor: gradGrowth,
        borderWidth: 2,
        tension: .35,
        fill: true,
        pointRadius: 4,
        pointBackgroundColor: ctx => ctx.raw >= 0 ? '#3fb950' : '#f85149',
        pointHoverRadius: 6,
      }],
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: ctx => ` Tăng trưởng: ${ctx.raw}%` } },
      },
      scales: {
        x: { grid: { display: false } },
        y: {
          grid: { color: 'rgba(255,255,255,.04)' },
          ticks: { callback: v => v + '%' },
        },
      },
    },
  });

});

/* ── Export ── */
function exportRevenue() {
  if (typeof showToast === 'function') showToast('Đang xuất báo cáo doanh thu...', 'info');
}
