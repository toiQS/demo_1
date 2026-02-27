<?php
/**
 * import_export.php — Thống kê Nhập xuất
 * CSS : css/import_export.css
 * JS  : js/import_export.js
 */

$page_title    = 'Thống kê Nhập xuất';
$page_subtitle = 'Phân tích giá trị nhập hàng, xuất hàng, tồn kho và nhà cung cấp';
$active_nav    = 'import_export';
$pending_orders  = 27;
$low_stock_count = 5;

/* ── Dữ liệu mẫu (thay bằng query DB) ───────────────────────────── */
$months       = ['T8/25','T9/25','T10/25','T11/25','T12/25','T1/26','T2/26'];
$import_data  = [620000000, 710000000,  830000000,  980000000, 1200000000,  790000000,  985000000];
$export_data  = [780000000, 900000000, 1050000000, 1290000000, 1540000000, 1020000000, 1190000000];
$profit_data  = array_map(fn($x, $m) => $x - $m, $export_data, $import_data);
$receipt_counts = [35, 38, 41, 48, 56, 39, 42];

$suppliers = [
  ['name'=>'Apple VN (FPT Trading)',            'receipts'=>14, 'value'=>348000000, 'products'=>3,  'avatar'=>'A'],
  ['name'=>'Samsung Electronics VN',            'receipts'=>10, 'value'=>195000000, 'products'=>4,  'avatar'=>'S'],
  ['name'=>'Digiworld Corp.',                   'receipts'=> 9, 'value'=>280000000, 'products'=>8,  'avatar'=>'D'],
  ['name'=>'Xiaomi VN',                         'receipts'=> 6, 'value'=>142000000, 'products'=>6,  'avatar'=>'X'],
  ['name'=>'ASUS Authorized Distributor',       'receipts'=> 3, 'value'=>198000000, 'products'=>5,  'avatar'=>'U'],
];

$monthly_details = [];
for ($i = 0; $i < count($months); $i++) {
  $monthly_details[] = [
    'month'         => $months[$i],
    'import'        => $import_data[$i],
    'export'        => $export_data[$i],
    'profit'        => $profit_data[$i],
    'receipts'      => $receipt_counts[$i],
    'margin'        => round($profit_data[$i] / $export_data[$i] * 100, 1),
    'balance_ratio' => round($export_data[$i] / $import_data[$i], 2),
  ];
}

$summary = [
  'import_cur'    => 985000000,
  'import_prev'   => 790000000,
  'export_cur'    => 1190000000,
  'export_prev'   => 1020000000,
  'receipts_cur'  => 42,
  'receipts_prev' => 39,
  'margin_cur'    => round((1190000000 - 985000000) / 1190000000 * 100, 1),
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

$im_chg  = pct($summary['import_cur'],   $summary['import_prev']);
$ex_chg  = pct($summary['export_cur'],   $summary['export_prev']);
$rec_chg = pct($summary['receipts_cur'], $summary['receipts_prev']);
$maxSupplierVal = max(array_column($suppliers, 'value'));

require_once 'includes/layout.php';
?>

<link rel="stylesheet" href="assets/css/report_import.css">

<!-- ================================================================
     KPI CARDS
================================================================= -->
<div class="kpi-grid">

  <div class="kpi-card kc-blue">
    <i class="fa-solid fa-truck-ramp-box kpi-icon"></i>
    <div class="kpi-label">Giá trị Nhập T02</div>
    <div class="kpi-value"><?= fmtM($summary['import_cur']) ?></div>
    <span class="kpi-change <?= cls($im_chg) ?>"><?= caret($im_chg) ?> <?= abs($im_chg) ?>% so với T1</span>
  </div>

  <div class="kpi-card kc-green">
    <i class="fa-solid fa-shop kpi-icon"></i>
    <div class="kpi-label">Giá trị Xuất T02</div>
    <div class="kpi-value"><?= fmtM($summary['export_cur']) ?></div>
    <span class="kpi-change <?= cls($ex_chg) ?>"><?= caret($ex_chg) ?> <?= abs($ex_chg) ?>% so với T1</span>
  </div>

  <div class="kpi-card kc-amber">
    <i class="fa-solid fa-scale-balanced kpi-icon"></i>
    <div class="kpi-label">Biên lợi nhuận</div>
    <div class="kpi-value"><?= $summary['margin_cur'] ?>%</div>
    <span class="kpi-change neu">Tỷ lệ Xuất/Nhập: <?= round($summary['export_cur']/$summary['import_cur'],2) ?>x</span>
  </div>

  <div class="kpi-card kc-red">
    <i class="fa-solid fa-file-invoice kpi-icon"></i>
    <div class="kpi-label">Phiếu nhập T02</div>
    <div class="kpi-value"><?= $summary['receipts_cur'] ?></div>
    <span class="kpi-change <?= cls($rec_chg) ?>"><?= caret($rec_chg) ?> <?= abs($rec_chg) ?>% so với T1</span>
  </div>

</div>

<!-- ================================================================
     ROW 1 — Grouped Bar Nhập/Xuất + Line Lợi nhuận
================================================================= -->
<div class="chart-grid-main">

  <!-- Grouped Bar: Nhập vs Xuất -->
  <div class="chart-card">
    <div class="chart-card-header">
      <div class="chart-card-title"><i class="fa-solid fa-chart-bar"></i> Giá trị Nhập & Xuất 7 tháng</div>
      <div class="chart-legend">
        <div class="legend-item"><div class="legend-dot" style="background:#58a6ff"></div>Nhập</div>
        <div class="legend-item"><div class="legend-dot" style="background:#3fb950"></div>Xuất</div>
      </div>
    </div>
    <canvas id="chartImportExport"></canvas>
    <div class="chart-summary">
      <div class="cs-item">
        <div class="cs-label">Tổng Nhập 7T</div>
        <div class="cs-val" style="color:var(--blue)"><?= fmtM(array_sum($import_data)) ?></div>
      </div>
      <div class="cs-item">
        <div class="cs-label">Tổng Xuất 7T</div>
        <div class="cs-val" style="color:var(--green)"><?= fmtM(array_sum($export_data)) ?></div>
      </div>
      <div class="cs-item">
        <div class="cs-label">Chênh lệch</div>
        <div class="cs-val" style="color:var(--accent)"><?= fmtM(array_sum($export_data) - array_sum($import_data)) ?></div>
      </div>
    </div>
  </div>

  <!-- Line: Lợi nhuận -->
  <div class="chart-card">
    <div class="chart-card-header">
      <div class="chart-card-title"><i class="fa-solid fa-chart-line"></i> Lợi nhuận (Xuất − Nhập)</div>
    </div>
    <canvas id="chartProfit" class="canvas-sm"></canvas>
    <div class="chart-summary" style="margin-top:14px">
      <div class="cs-item">
        <div class="cs-label">Cao nhất</div>
        <div class="cs-val" style="color:var(--green)"><?= fmtM(max($profit_data)) ?></div>
      </div>
      <div class="cs-item">
        <div class="cs-label">Thấp nhất</div>
        <div class="cs-val" style="color:var(--red)"><?= fmtM(min($profit_data)) ?></div>
      </div>
      <div class="cs-item">
        <div class="cs-label">Tổng 7T</div>
        <div class="cs-val" style="color:var(--accent)"><?= fmtM(array_sum($profit_data)) ?></div>
      </div>
    </div>
  </div>

</div>

<!-- ================================================================
     ROW 2 — Donut NCC + Bar phiếu nhập + Danh sách NCC
================================================================= -->
<div class="chart-grid-3">

  <!-- Donut: Tỷ trọng nhập theo NCC -->
  <div class="chart-card">
    <div class="chart-card-header">
      <div class="chart-card-title"><i class="fa-solid fa-chart-pie"></i> Tỷ trọng nhập theo NCC (T02)</div>
    </div>
    <canvas id="chartSupplier" class="canvas-sm"></canvas>
  </div>

  <!-- Bar: Số phiếu nhập mỗi tháng -->
  <div class="chart-card">
    <div class="chart-card-header">
      <div class="chart-card-title"><i class="fa-solid fa-file-invoice"></i> Số phiếu nhập theo tháng</div>
    </div>
    <canvas id="chartReceipts" class="canvas-sm"></canvas>
    <div class="chart-summary" style="margin-top:14px">
      <div class="cs-item">
        <div class="cs-label">Tổng phiếu 7T</div>
        <div class="cs-val" style="color:var(--blue)"><?= array_sum($receipt_counts) ?></div>
      </div>
      <div class="cs-item">
        <div class="cs-label">TB/tháng</div>
        <div class="cs-val"><?= round(array_sum($receipt_counts) / count($receipt_counts)) ?></div>
      </div>
      <div class="cs-item">
        <div class="cs-label">Cao nhất</div>
        <div class="cs-val" style="color:var(--accent)"><?= max($receipt_counts) ?> phiếu</div>
      </div>
    </div>
  </div>

  <!-- Danh sách nhà cung cấp -->
  <div class="chart-card">
    <div class="chart-card-header">
      <div class="chart-card-title"><i class="fa-solid fa-handshake"></i> Nhà cung cấp chính (T02)</div>
      <a href="import.php" class="btn-view-all" style="font-size:11px">Xem phiếu</a>
    </div>
    <?php foreach ($suppliers as $s):
      $pct = round($s['value'] / $maxSupplierVal * 100);
    ?>
    <div class="supplier-item">
      <div class="supplier-avatar"><i class="fa-solid fa-building"></i></div>
      <div style="flex:1;min-width:0">
        <div style="font-size:12px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
          <?= htmlspecialchars($s['name']) ?>
        </div>
        <div style="display:flex;align-items:center;gap:8px;margin-top:5px">
          <div class="supplier-bar-wrap"><div class="supplier-bar-fill" style="width:<?= $pct ?>%"></div></div>
          <span style="font-size:10px;color:var(--text-muted);white-space:nowrap"><?= $s['receipts'] ?> phiếu</span>
        </div>
      </div>
      <div style="text-align:right;flex-shrink:0;margin-left:8px">
        <div style="font-size:12px;font-weight:700;font-family:var(--mono);color:var(--blue)"><?= fmtM($s['value']) ?></div>
        <div style="font-size:10px;color:var(--text-muted)"><?= $s['products'] ?> SP</div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

</div>

<!-- ================================================================
     BẢNG CHI TIẾT NHẬP XUẤT THEO THÁNG
================================================================= -->
<div class="card">
  <div class="card-header">
    <div class="card-title"><i class="fa-solid fa-table"></i> Bảng chi tiết Nhập xuất theo tháng</div>
    <button class="btn btn-secondary" style="padding:6px 14px;font-size:12px" onclick="exportImportExport()">
      <i class="fa-solid fa-file-export"></i> Xuất Excel
    </button>
  </div>
  <div style="overflow-x:auto">
    <table id="ieTable">
      <thead>
        <tr>
          <th>Tháng</th>
          <th style="text-align:right">Giá trị Nhập (₫)</th>
          <th style="text-align:right">Giá trị Xuất (₫)</th>
          <th style="text-align:right">Chênh lệch (₫)</th>
          <th style="text-align:center">Biên LN</th>
          <th style="text-align:center">Tỷ lệ X/N</th>
          <th style="text-align:center">Số phiếu nhập</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($monthly_details as $row):
          $isLast  = ($row['month'] === end($months));
          $balance = $row['export'] - $row['import'];
          $isSurplus = $balance >= 0;
        ?>
        <tr <?= $isLast ? 'style="background:rgba(88,166,255,.05)"' : '' ?>>
          <td style="font-weight:<?= $isLast ? 700 : 400 ?>">
            <?= $row['month'] ?>
            <?= $isLast ? '<span style="font-size:10px;color:var(--blue);margin-left:4px">(hiện tại)</span>' : '' ?>
          </td>
          <td style="text-align:right;font-family:var(--mono);font-size:12px;color:var(--blue)">
            <?= number_format($row['import']) ?>₫
          </td>
          <td style="text-align:right;font-family:var(--mono);font-size:12px;color:var(--green)">
            <?= number_format($row['export']) ?>₫
          </td>
          <?php $balanceColor = $isSurplus ? 'var(--green)' : 'var(--red)'; ?>
          <td style="text-align:right;font-family:var(--mono);font-size:12px;color:<?= $balanceColor ?>;font-weight:700">
            <?= $isSurplus ? '+' : '' ?><?= number_format($balance) ?>₫
          </td>
          <td style="text-align:center">
            <?php
              $bg  = $row['margin'] >= 20 ? 'rgba(63,185,80,.15)' : 'rgba(240,165,0,.15)';
              $clr = $row['margin'] >= 20 ? 'var(--green)' : 'var(--accent)';
            ?>
            <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:<?= $bg ?>;color:<?= $clr ?>">
              <?= $row['margin'] ?>%
            </span>
          </td>
          <?php $ratioColor = $row['balance_ratio'] >= 1 ? 'var(--green)' : 'var(--red)'; ?>
          <td style="text-align:center;font-family:var(--mono);font-size:12px;color:<?= $ratioColor ?>">
            <?= $row['balance_ratio'] ?>x
          </td>
          <td style="text-align:center;font-family:var(--mono);color:var(--text-secondary)">
            <?= $row['receipts'] ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr style="background:var(--bg-secondary);font-weight:700">
          <td>TỔNG CỘNG</td>
          <td style="text-align:right;font-family:var(--mono);color:var(--blue)"><?= number_format(array_sum($import_data)) ?>₫</td>
          <td style="text-align:right;font-family:var(--mono);color:var(--green)"><?= number_format(array_sum($export_data)) ?>₫</td>
          <td style="text-align:right;font-family:var(--mono);color:var(--green);font-weight:700">
            +<?= number_format(array_sum($profit_data)) ?>₫
          </td>
          <td style="text-align:center">
            <?php $totalMargin = round(array_sum($profit_data) / array_sum($export_data) * 100, 1); ?>
            <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:rgba(63,185,80,.15);color:var(--green)">
              <?= $totalMargin ?>%
            </span>
          </td>
          <td style="text-align:center;font-family:var(--mono)">
            <?= round(array_sum($export_data) / array_sum($import_data), 2) ?>x
          </td>
          <td style="text-align:center;font-family:var(--mono)"><?= array_sum($receipt_counts) ?></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

<!-- ── Inject PHP data ── -->
<script>
window.IE_DATA = {
  labels:         <?= json_encode($months) ?>,
  importData:     <?= json_encode($import_data) ?>,
  exportData:     <?= json_encode($export_data) ?>,
  profitData:     <?= json_encode($profit_data) ?>,
  receiptCounts:  <?= json_encode($receipt_counts) ?>,
  supplierLabels: <?= json_encode(array_column($suppliers, 'name')) ?>,
  supplierData:   <?= json_encode(array_column($suppliers, 'value')) ?>,
};
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="assets/js/report_import.js"></script>

<?php require_once 'includes/layout_footer.php'; ?>
