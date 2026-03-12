<?php
// views/inventory.php
$currentPage = 'inventory';
$pageTitle   = 'TỒN KHO & THỐNG KÊ';
$breadcrumb  = 'Tiện ích / Tồn kho & Thống kê';

// ── Load services ──────────────────────────────────────────
include_once __DIR__ . '/../database/db.php';
include_once __DIR__ . '/../services/inventories/get_inventority_start.php';
include_once __DIR__ . '/../services/inventories/get_detail_list.php';
include_once __DIR__ . '/../services/inventories/get_warning_list.php';

// ── Dữ liệu ───────────────────────────────────────────────
$filterIdDM    = $_GET['idDM']    ?? '';
$filterStatus  = $_GET['status']  ?? '';
$filterKeyword = trim($_GET['keyword'] ?? '');

$stats      = getInventoryStats($pdo);
$details    = getInventoryDetail($pdo, [
    'idDM'    => $filterIdDM,
    'status'  => $filterStatus,
    'keyword' => $filterKeyword,
]);
$warnings   = getWarningList($pdo);
$danhMucs   = getDanhMucList($pdo);

require_once 'includes/header.php';
?>


<!-- ── Stat Cards ─────────────────────────────────────────── -->
<div class="section-head" style="margin-bottom:0">
  <div class="section-title">TỒN KHO & THỐNG KÊ</div>
</div>

<div class="stat-grid">

  <div class="stat-card" style="--card-color:var(--blue)">
    <div class="stat-top">
      <div class="stat-label">Tổng SKU</div>
      <div class="stat-icon">🗂</div>
    </div>
    <div class="stat-value"><?= number_format($stats['total_sku']) ?></div>
    <div class="stat-change up">▲ Đang hoạt động</div>
  </div>

  <div class="stat-card" style="--card-color:var(--red)">
    <div class="stat-top">
      <div class="stat-label">Hết hàng</div>
      <div class="stat-icon">❗</div>
    </div>
    <div class="stat-value"><?= number_format($stats['out_stock']) ?></div>
    <div class="stat-change down">▼ Cần nhập ngay</div>
  </div>

  <div class="stat-card" style="--card-color:var(--accent)">
    <div class="stat-top">
      <div class="stat-label">Sắp hết</div>
      <div class="stat-icon">⚠️</div>
    </div>
    <div class="stat-value"><?= number_format($stats['low_stock']) ?></div>
    <div class="stat-change">Dưới ngưỡng cảnh báo</div>
  </div>

  <div class="stat-card" style="--card-color:var(--green)">
    <div class="stat-top">
      <div class="stat-label">Còn hàng</div>
      <div class="stat-icon">✅</div>
    </div>
    <div class="stat-value"><?= number_format($stats['in_stock']) ?></div>
    <div class="stat-change up">▲ Đủ cung ứng</div>
  </div>

</div><!-- /.stat-grid -->


<!-- ── Panel row: Chi tiết + Cảnh báo ────────────────────── -->
<div class="panel-row">

  <!-- ════ Panel trái: Chi tiết tồn kho ════════════════════ -->
  <div class="panel">
    <div class="panel-header">
      <div class="panel-title">CHI TIẾT TỒN KHO</div>

      <!-- Bộ lọc nhỏ trong panel header -->
      <form method="GET" style="display:flex;gap:6px;align-items:center">

        <!-- Tìm theo tên -->
        <div style="display:flex;align-items:center;gap:5px;
                    background:var(--surface2);border:1px solid var(--border);
                    border-radius:6px;padding:4px 10px">
          <span style="color:var(--text3);font-size:12px">🔍</span>
          <input type="text" name="keyword"
                 value="<?= htmlspecialchars($filterKeyword) ?>"
                 placeholder="Tìm sản phẩm…"
                 style="background:none;border:none;outline:none;color:var(--text);
                        font-size:12px;font-family:'DM Sans',sans-serif;width:130px"
                 onkeydown="if(event.key==='Enter')this.form.submit()">
        </div>

        <!-- Lọc danh mục -->
        <select class="filter-select" name="idDM" onchange="this.form.submit()"
                style="font-size:11px;padding:4px 8px">
          <option value="">Tất cả DM</option>
          <?php foreach ($danhMucs as $dm): ?>
            <option value="<?= $dm['idDM'] ?>"
              <?= ($filterIdDM == $dm['idDM']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($dm['LOAISP']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <!-- Lọc tình trạng -->
        <select class="filter-select" name="status" onchange="this.form.submit()"
                style="font-size:11px;padding:4px 8px">
          <option value="">Tất cả</option>
          <option value="out" <?= ($filterStatus === 'out') ? 'selected' : '' ?>>Hết hàng</option>
          <option value="low" <?= ($filterStatus === 'low') ? 'selected' : '' ?>>Sắp hết</option>
          <option value="ok"  <?= ($filterStatus === 'ok')  ? 'selected' : '' ?>>Còn hàng</option>
        </select>

      </form>
    </div><!-- /.panel-header -->

    <div class="panel-body" style="max-height:420px;overflow-y:auto">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Sản phẩm</th>
            <th>Danh mục</th>
            <th>Tồn kho</th>
            <th>Ngưỡng</th>
            <th>Tình trạng</th>
            <th>Thao tác</th>
          </tr>
        </thead>
        <tbody>

          <?php if (empty($details)): ?>
            <tr>
              <td colspan="6"
                  style="text-align:center;color:var(--text3);padding:32px;
                         font-family:'JetBrains Mono',monospace;font-size:12px">
                — Không có sản phẩm nào —
              </td>
            </tr>

          <?php else: ?>
            <?php foreach ($details as $sp): ?>

            <?php
              // Màu cột tồn kho
              $soColor = match($sp['STATUS']) {
                'out' => 'var(--red)',
                'low' => 'var(--accent)',
                default => 'var(--green)',
              };
              // Badge tình trạng
              $badgeClass = match($sp['STATUS']) {
                'out'   => 'badge-red',
                'low'   => 'badge-amber',
                default => 'badge-green',
              };
              $badgeText = match($sp['STATUS']) {
                'out'   => 'Hết hàng',
                'low'   => 'Sắp hết',
                default => 'Còn hàng',
              };
            ?>

            <tr>
              <td class="td-main"><?= htmlspecialchars($sp['TENSP']) ?></td>

              <td style="font-size:12px;color:var(--text2)">
                <?= htmlspecialchars($sp['DANHMUC'] ?? '—') ?>
              </td>

              <td style="color:<?= $soColor ?>;
                         font-family:'JetBrains Mono',monospace;font-weight:600">
                <?= number_format($sp['SOLUONG']) ?>
              </td>

              <td style="color:var(--text3);font-family:'JetBrains Mono',monospace">
                <?= number_format($sp['NGUONG_DAT']) ?>
              </td>

              <td><span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span></td>

              <td>
                <a href="import.php?action=add&sp=<?= $sp['idSP'] ?>"
                   class="action-link">
                  📦 Nhập thêm
                </a>
              </td>
            </tr>

            <?php endforeach; ?>
          <?php endif; ?>

        </tbody>
      </table>
    </div><!-- /.table-wrap -->
    </div><!-- /.panel-body -->

    <!-- Footer đếm -->
    <div style="padding:10px 16px;border-top:1px solid var(--border);
                font-size:11px;color:var(--text3);
                font-family:'JetBrains Mono',monospace">
      Hiển thị <?= count($details) ?> / <?= number_format($stats['total_sku']) ?> sản phẩm
    </div>

  </div><!-- /.panel trái -->


  <!-- ════ Panel phải: Cảnh báo tồn kho ═══════════════════ -->
  <div class="panel">
    <div class="panel-header">
      <div class="panel-title">CẢNH BÁO TỒN KHO</div>
      <span style="font-size:11px;font-family:'JetBrains Mono',monospace;color:var(--text3)">
        <?= count($warnings) ?> mục
      </span>
    </div>

    <div class="panel-body" style="max-height:420px;overflow-y:auto">

      <?php if (empty($warnings)): ?>
        <div style="padding:32px;text-align:center;color:var(--text3);
                    font-size:12px;font-family:'JetBrains Mono',monospace">
          ✅ Tất cả sản phẩm đều đủ hàng
        </div>

      <?php else: ?>
        <?php foreach ($warnings as $w): ?>

        <?php
          $dotColor  = $w['STATUS'] === 'out' ? 'var(--red)' : 'var(--accent)';
          $txtColor  = $w['STATUS'] === 'out' ? 'var(--red)' : 'var(--accent)';
        ?>

        <div class="alert-item">
          <div class="alert-dot" style="background:<?= $dotColor ?>"></div>

          <div class="alert-name" title="<?= htmlspecialchars($w['TENSP']) ?>">
            <?= htmlspecialchars(mb_strimwidth($w['TENSP'], 0, 30, '…')) ?>
          </div>

          <div class="alert-stock" style="color:<?= $txtColor ?>">
            <?= $w['SOLUONG'] ?> / <?= $w['NGUONG_DAT'] ?>
          </div>
        </div>

        <?php endforeach; ?>
      <?php endif; ?>

    </div><!-- /.panel-body -->
  </div><!-- /.panel phải -->

</div><!-- /.panel-row -->


<style>
/* Giữ thead cố định khi cuộn bảng */
.table-wrap thead th {
  position: sticky;
  top: 0;
  background: var(--surface);
  z-index: 1;
}

/* Thanh cuộn tuỳ chỉnh cho table-wrap và panel-body */
.table-wrap::-webkit-scrollbar,
.panel-body::-webkit-scrollbar  { width: 4px; height: 4px; }
.table-wrap::-webkit-scrollbar-track,
.panel-body::-webkit-scrollbar-track  { background: var(--surface2); border-radius: 2px; }
.table-wrap::-webkit-scrollbar-thumb,
.panel-body::-webkit-scrollbar-thumb  { background: var(--border); border-radius: 2px; }
.table-wrap::-webkit-scrollbar-thumb:hover,
.panel-body::-webkit-scrollbar-thumb:hover { background: var(--text3); }
</style>

<?php require_once 'includes/footer.php'; ?>