<?php
/**
 * revenue.php — Thống kê Doanh thu
 * CSS : css/revenue.css
 * JS  : js/revenue.js
 */

$page_title    = 'Thống kê Doanh thu';
$page_subtitle = 'Phân tích doanh thu, đơn hàng và tăng trưởng theo tháng';
$active_nav    = 'revenue';
$pending_orders  = 27;
$low_stock_count = 5;

/* ── Dữ liệu mẫu (thay bằng query DB) ───────────────────────────── */
$months       = ['T8/25','T9/25','T10/25','T11/25','T12/25','T1/26','T2/26'];
$revenue_data = [820000000, 950000000, 1100000000, 1350000000, 1620000000, 1080000000, 1285600000];
$order_data   = [312, 378, 421, 530, 648, 401, 984];   // số đơn hàng từng tháng

$category_labels = ['Điện thoại', 'Laptop', 'Máy tính bảng', 'Âm thanh', 'Phụ kiện'];
$category_data   = [623040000, 389760000, 343200000, 167895000, 80000000]; // DT tháng 02

$top_products = [
  ['name'=>'iPhone 15 Pro Max 256GB',  'category'=>'Điện thoại',    'qty'=>28, 'revenue'=>882000000, 'profit'=>126000000],
  ['name'=>'Samsung Galaxy S24 Ultra', 'category'=>'Điện thoại',    'qty'=>24, 'revenue'=>623040000, 'profit'=>96000000],
  ['name'=>'MacBook Air M3 13" 16GB',  'category'=>'Laptop',         'qty'=>12, 'revenue'=>389760000, 'profit'=>54000000],
  ['name'=>'iPad Pro 12.9" M4',        'category'=>'Máy tính bảng', 'qty'=>13, 'revenue'=>343200000, 'profit'=>52000000],
  ['name'=>'Tai nghe Sony WH-1000XM5', 'category'=>'Âm thanh',       'qty'=>21, 'revenue'=>167895000, 'profit'=>23000000],
];

$summary = [
  'revenue_cur'  => 1285600000,
  'revenue_prev' => 1080000000,
  'orders_cur'   => 984,
  'orders_prev'  => 401,
  'avg_order'    => 1305894,       // doanh thu / số đơn
  'avg_prev'     => 1272568,
  'profit_cur'   => 300600000,
  'profit_prev'  => 290000000,
];

/* ── Helpers ─────────────────────────────────────────────────────── */
function fmtM(int $n): string {
  if ($n >= 1_000_000_000) return number_format($n / 1_000_000_000, 2) . 'B₫';
  if ($n >= 1_000_000)     return number_format($n / 1_000_000, 0)     . 'M₫';
  return number_format($n) . '₫';
}
function pct(int $cur, int $prev): float {
  return $prev ? round(($cur - $prev) / $prev * 100, 1) : 0;
}
function caret(float $v): string { return $v >= 0 ? '▲' : '▼'; }
function cls(float $v): string   { return $v >= 0 ? 'up' : 'down'; }

$rv_chg  = pct($summary['revenue_cur'],  $summary['revenue_prev']);
$ord_chg = pct($summary['orders_cur'],   $summary['orders_prev']);
$avg_chg = pct($summary['avg_order'],    $summary['avg_prev']);
$pr_chg  = pct($summary['profit_cur'],   $summary['profit_prev']);

require_once 'includes/layout.php';
?>

<link rel="stylesheet" href="assets/css/report_revenue.css">

<!-- ================================================================
     KPI CARDS
================================================================= -->
<div class="kpi-grid">

  <div class="kpi-card kc-amber">
    <i class="fa-solid fa-chart-line kpi-icon"></i>
    <div class="kpi-label">Doanh thu tháng 02</div>
    <div class="kpi-value"><?= fmtM($summary['revenue_cur']) ?></div>
    <span class="kpi-change <?= cls($rv_chg) ?>"><?= caret($rv_chg) ?> <?= abs($rv_chg) ?>% so với T1</span>
  </div>

  <div class="kpi-card kc-purple">
    <i class="fa-solid fa-receipt kpi-icon"></i>
    <div class="kpi-label">Số đơn hàng T02</div>
    <div class="kpi-value"><?= number_format($summary['orders_cur']) ?></div>
    <span class="kpi-change <?= cls($ord_chg) ?>"><?= caret($ord_chg) ?> <?= abs($ord_chg) ?>% so với T1</span>
  </div>

  <div class="kpi-card kc-blue">
    <i class="fa-solid fa-cart-shopping kpi-icon"></i>
    <div class="kpi-label">Giá trị đơn TB</div>
    <div class="kpi-value"><?= fmtM($summary['avg_order']) ?></div>
    <span class="kpi-change <?= cls($avg_chg) ?>"><?= caret($avg_chg) ?> <?= abs($avg_chg) ?>% so với T1</span>
  </div>

  <div class="kpi-card kc-green">
    <i class="fa-solid fa-sack-dollar kpi-icon"></i>
    <div class="kpi-label">Lợi nhuận T02</div>
    <div class="kpi-value"><?= fmtM($summary['profit_cur']) ?></div>
    <span class="kpi-change <?= cls($pr_chg) ?>"><?= caret($pr_chg) ?> <?= abs($pr_chg) ?>% so với T1</span>
  </div>

</div>

<!-- ================================================================
     ROW 1 — Line chart DT + Số đơn | Donut danh mục
================================================================= -->
<div class="chart-grid-main">

  <!-- Line: Doanh thu & Số đơn -->
  <div class="chart-card">
    <div class="chart-card-header">
      <div class="chart-card-title"><i class="fa-solid fa-chart-area"></i> Doanh thu & Số đơn 7 tháng</div>
      <div class="chart-legend">
        <div class="legend-item"><div class="legend-dot" style="background:#f0a500"></div>Doanh thu</div>
        <div class="legend-item"><div class="legend-dot" style="background:#a371f7"></div>Số đơn</div>
      </div>
    </div>
    <canvas id="chartRevenue"></canvas>
    <div class="chart-summary">
      <div class="cs-item">
        <div class="cs-label">Tổng DT 7T</div>
        <div class="cs-val" style="color:var(--accent)"><?= fmtM(array_sum($revenue_data)) ?></div>
      </div>
      <div class="cs-item">
        <div class="cs-label">Tổng đơn 7T</div>
        <div class="cs-val" style="color:var(--purple)"><?= number_format(array_sum($order_data)) ?></div>
      </div>
      <div class="cs-item">
        <div class="cs-label">DT cao nhất</div>
        <div class="cs-val" style="color:var(--green)"><?= fmtM(max($revenue_data)) ?></div>
      </div>
    </div>
  </div>

  <!-- Donut: Cơ cấu DT theo danh mục -->
  <div class="chart-card">
    <div class="chart-card-header">
      <div class="chart-card-title"><i class="fa-solid fa-chart-pie"></i> Cơ cấu DT theo danh mục (T02)</div>
    </div>
    <canvas id="chartDonut" class="canvas-sm"></canvas>
    <div class="chart-summary" style="margin-top:14px">
      <div class="cs-item">
        <div class="cs-label">Nhóm dẫn đầu</div>
        <div class="cs-val" style="color:var(--accent)">Điện thoại</div>
      </div>
      <div class="cs-item">
        <div class="cs-label">Tỷ trọng</div>
        <div class="cs-val" style="color:var(--accent)">
          <?= round($category_data[0] / array_sum($category_data) * 100) ?>%
        </div>
      </div>
      <div class="cs-item">
        <div class="cs-label">Số danh mục</div>
        <div class="cs-val"><?= count($category_labels) ?></div>
      </div>
    </div>
  </div>

</div>

<!-- ================================================================
     ROW 2 — Bar DT danh mục | Line tăng trưởng
================================================================= -->
<div class="chart-grid-2">

  <!-- Bar: DT từng danh mục -->
  <div class="chart-card">
    <div class="chart-card-header">
      <div class="chart-card-title"><i class="fa-solid fa-chart-bar"></i> Doanh thu theo danh mục (T02)</div>
    </div>
    <canvas id="chartCategory" class="canvas-sm"></canvas>
  </div>

  <!-- Line: Tốc độ tăng trưởng -->
  <div class="chart-card">
    <div class="chart-card-header">
      <div class="chart-card-title"><i class="fa-solid fa-arrow-trend-up"></i> Tốc độ tăng trưởng DT (%)</div>
    </div>
    <canvas id="chartGrowth" class="canvas-sm"></canvas>
  </div>

</div>

<!-- ================================================================
     TOP SẢN PHẨM DOANH THU CAO NHẤT
================================================================= -->
<div class="card" style="margin-bottom:16px">
  <div class="card-header">
    <div class="card-title"><i class="fa-solid fa-trophy"></i> Top sản phẩm doanh thu cao nhất (T02)</div>
    <a href="products.php" class="btn-view-all">Xem tất cả</a>
  </div>
  <div style="overflow-x:auto">
    <table>
      <thead>
        <tr>
          <th style="width:36px">#</th>
          <th>Sản phẩm</th>
          <th>Danh mục</th>
          <th style="text-align:center">Đã bán</th>
          <th style="text-align:right">Doanh thu</th>
          <th style="text-align:right">Lợi nhuận</th>
          <th>Tỷ trọng DT</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $totalRev = array_sum(array_column($top_products, 'revenue'));
        $maxRev   = max(array_column($top_products, 'revenue'));
        foreach ($top_products as $idx => $p):
          $rankClass = match($idx) { 0=>'rank-1', 1=>'rank-2', 2=>'rank-3', default=>'rank-n' };
          $share     = round($p['revenue'] / $totalRev * 100, 1);
          $barPct    = round($p['revenue'] / $maxRev * 100);
          $margin    = round($p['profit'] / $p['revenue'] * 100, 1);
        ?>
        <tr>
          <td><span class="rank-badge <?= $rankClass ?>"><?= $idx + 1 ?></span></td>
          <td style="font-weight:600;font-size:13px"><?= htmlspecialchars($p['name']) ?></td>
          <td style="color:var(--text-muted);font-size:12px"><?= $p['category'] ?></td>
          <td style="text-align:center;font-family:var(--mono);color:var(--blue)"><?= $p['qty'] ?></td>
          <td style="text-align:right;font-family:var(--mono);font-weight:700;color:var(--accent)"><?= fmtM($p['revenue']) ?></td>
          <td style="text-align:right;font-family:var(--mono);color:var(--green)">
            <?= fmtM($p['profit']) ?>
            <span style="font-size:10px;color:var(--text-muted)">(<?= $margin ?>%)</span>
          </td>
          <td style="min-width:120px">
            <div style="display:flex;align-items:center;gap:8px">
              <div class="prod-bar-wrap"><div class="prod-bar-fill" style="width:<?= $barPct ?>%"></div></div>
              <span style="font-size:11px;color:var(--text-muted);min-width:34px"><?= $share ?>%</span>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ================================================================
     BẢNG CHI TIẾT DOANH THU THEO THÁNG
================================================================= -->
<div class="card">
  <div class="card-header">
    <div class="card-title"><i class="fa-solid fa-table"></i> Bảng doanh thu chi tiết theo tháng</div>
    <button class="btn btn-secondary" style="padding:6px 14px;font-size:12px" onclick="exportRevenue()">
      <i class="fa-solid fa-file-export"></i> Xuất Excel
    </button>
  </div>
  <div style="overflow-x:auto">
    <table id="revenueTable">
      <thead>
        <tr>
          <th>Tháng</th>
          <th style="text-align:right">Doanh thu (₫)</th>
          <th style="text-align:center">Số đơn</th>
          <th style="text-align:right">Giá trị TB/đơn</th>
          <th style="text-align:center">Tăng trưởng</th>
          <th>So sánh</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $maxBar = max($revenue_data);
        for ($i = 0; $i < count($months); $i++):
          $growth      = $i === 0 ? null : round(($revenue_data[$i] - $revenue_data[$i-1]) / $revenue_data[$i-1] * 100, 1);
          $avgOrd      = round($revenue_data[$i] / $order_data[$i]);
          $isLast      = ($i === count($months) - 1);
          $barPct      = round($revenue_data[$i] / $maxBar * 100);
          $rowClass    = $isLast ? 'highlight-row' : '';
          $fontWeight  = $isLast ? 700 : 400;
          $growthBg    = ($growth !== null && $growth >= 0) ? 'rgba(63,185,80,.15)' : 'rgba(248,81,73,.15)';
          $growthColor = ($growth !== null && $growth >= 0) ? 'var(--green)' : 'var(--red)';
          $growthArrow = ($growth !== null && $growth >= 0) ? '▲' : '▼';
          $barStyle    = "height:100%;width:{$barPct}%;border-radius:3px;background:linear-gradient(90deg,var(--accent),#e05c00)";
        ?>
        <tr class="<?= $rowClass ?>">
          <td style="font-weight:<?= $fontWeight ?>">
            <?= $months[$i] ?>
            <?= $isLast ? '<span style="font-size:10px;color:var(--accent);margin-left:4px">(hiện tại)</span>' : '' ?>
          </td>
          <td style="text-align:right;font-family:var(--mono);color:var(--accent);font-weight:700">
            <?= number_format($revenue_data[$i]) ?>₫
          </td>
          <td style="text-align:center;font-family:var(--mono);color:var(--purple)"><?= $order_data[$i] ?></td>
          <td style="text-align:right;font-family:var(--mono);font-size:12px;color:var(--blue)">
            <?= number_format($avgOrd) ?>₫
          </td>
          <td style="text-align:center">
            <?php if ($growth === null): ?>
              <span style="color:var(--text-muted);font-size:12px">—</span>
            <?php else: ?>
              <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:<?= $growthBg ?>;color:<?= $growthColor ?>">
                <?= $growthArrow ?> <?= abs($growth) ?>%
              </span>
            <?php endif; ?>
          </td>
          <td style="min-width:100px">
            <div style="height:5px;background:var(--border);border-radius:3px">
              <div style="<?= $barStyle ?>"></div>
            </div>
          </td>
        </tr>
        <?php endfor; ?>
      </tbody>
      <tfoot>
        <tr>
          <td>TỔNG / TRUNG BÌNH</td>
          <td style="text-align:right;font-family:var(--mono);color:var(--accent)"><?= number_format(array_sum($revenue_data)) ?>₫</td>
          <td style="text-align:center;font-family:var(--mono);color:var(--purple)"><?= number_format(array_sum($order_data)) ?></td>
          <td style="text-align:right;font-family:var(--mono);font-size:12px;color:var(--blue)">
            <?= number_format(round(array_sum($revenue_data) / array_sum($order_data))) ?>₫
          </td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

<!-- ── Inject PHP data ── -->
<script>
window.REVENUE_DATA = {
  labels:          <?= json_encode($months) ?>,
  revenueData:     <?= json_encode($revenue_data) ?>,
  orderData:       <?= json_encode($order_data) ?>,
  categoryLabels:  <?= json_encode($category_labels) ?>,
  categoryData:    <?= json_encode($category_data) ?>,
  donutLabels:     <?= json_encode($category_labels) ?>,
  donutData:       <?= json_encode($category_data) ?>,
};
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="assets/js/report_revenue.js"></script>

<?php require_once 'includes/layout_footer.php'; ?>