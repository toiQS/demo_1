<?php
$page_title    = 'Quản lý Danh mục';
$page_subtitle = 'Thêm & quản lý loại thiết bị điện tử';
$active_nav    = 'categories';

// Lấy $categories[], $pending_orders, $low_stock_count từ DB
// require_once 'controllers\categories\gets.php';
$categories = [];

include_once 'connectDB.php';
include_once 'object_status.php';
try {
  $sp_active = trang_thai_san_pham::ACTIVE->value;

  $res = $conn->query(
    "SELECT
             dm.idDM              AS id,
             dm.LOAISP            AS name,
             ''                   AS `desc`,
             1                    AS status,
             COUNT(sp.idSP)       AS `count`
         FROM danhmuc dm
         LEFT JOIN sanpham sp
               ON sp.idDM      = dm.idDM
              AND sp.TRANGTHAI = $sp_active
         GROUP BY dm.idDM, dm.LOAISP
         ORDER BY dm.idDM ASC"
  );

  if ($res) {
    while ($row = $res->fetch_assoc()) {
      $categories[] = $row;
    }
  }
} catch (mysqli_sql_exception $e) {
  $errMsg = date('[Y-m-d H:i:s]') . ' [CATEGORIES/gets] ' . $e->getMessage() . "\n";

  // Ghi vào đúng 2 file log
  $logPath = __DIR__ . '/../../logs/category/gets.txt';
  @file_put_contents($logPath, $errMsg, FILE_APPEND);
  @file_put_contents(__DIR__ . '/../../logs/index/dashboard.text', $errMsg, FILE_APPEND);
}

// ── Badge counts cho sidebar (layout.php cần 2 biến này) ──────
$pending_orders  = 0;
$low_stock_count = 0;

try {
  $pending = trang_thai_hoa_don::PENDING->value;
  $pending_orders = (int) $conn
    ->query("SELECT COUNT(*) AS c FROM hoadon WHERE TRANGTHAI = '$pending'")
    ->fetch_assoc()['c'];

  $sp_active = trang_thai_san_pham::ACTIVE->value;
  $low_stock_count = (int) $conn
    ->query("SELECT COUNT(*) AS c
                 FROM sanpham
                 WHERE SOLUONG < 10
                   AND TRANGTHAI = $sp_active")
    ->fetch_assoc()['c'];
} catch (mysqli_sql_exception $e) {
  $errMsg = date('[Y-m-d H:i:s]') . ' [CATEGORIES/badge] ' . $e->getMessage() . "\n";
  @file_put_contents(__DIR__ . '/../../logs/index/dashboard.text', $errMsg, FILE_APPEND);
}
// Stats nhanh
$totalCat  = count($categories);
$activeCat = count(array_filter($categories, fn($c) => $c['status'] == 1));
$hiddenCat = $totalCat - $activeCat;
$totalSP   = array_sum(array_column($categories, 'count'));

require_once 'includes/layout.php';
?>

<link rel="stylesheet" href="assets/css/categories.css">

<!-- ============================================================
     STATS ROW
============================================================= -->
<div class="stats-row">
  <div class="stat-box">
    <i class="fa-solid fa-layer-group"></i>
    <div>
      <div class="stat-val"><?= $totalCat ?></div>
      <div class="stat-lbl">Tổng danh mục</div>
    </div>
  </div>
  <div class="stat-box s-green">
    <i class="fa-solid fa-circle-check"></i>
    <div>
      <div class="stat-val"><?= $activeCat ?></div>
      <div class="stat-lbl">Hoạt động</div>
    </div>
  </div>
  <div class="stat-box s-red">
    <i class="fa-solid fa-eye-slash"></i>
    <div>
      <div class="stat-val"><?= $hiddenCat ?></div>
      <div class="stat-lbl">Đang ẩn</div>
    </div>
  </div>
  <div class="stat-box s-blue">
    <i class="fa-solid fa-box-open"></i>
    <div>
      <div class="stat-val"><?= number_format($totalSP) ?></div>
      <div class="stat-lbl">Tổng sản phẩm</div>
    </div>
  </div>
</div>

<!-- ============================================================
     TOOLBAR
============================================================= -->
<div class="page-actions">
  <div class="search-wrap">
    <i class="fa-solid fa-magnifying-glass"></i>
    <input type="text" id="searchInput" class="form-control"
      placeholder="Tìm danh mục..." oninput="filterCats()">
  </div>
  <select id="filterStatus" class="form-control" style="width:160px" onchange="filterCats()">
    <option value="">Tất cả trạng thái</option>
    <option value="1">Hoạt động</option>
    <option value="0">Đang ẩn</option>
  </select>
  <div style="margin-left:auto;display:flex;gap:8px">
    <button class="btn btn-secondary btn-icon is-active" id="btnGrid"
      onclick="setView('grid')" title="Dạng lưới">
      <i class="fa-solid fa-grip"></i>
    </button>
    <button class="btn btn-secondary btn-icon" id="btnList"
      onclick="setView('list')" title="Dạng bảng">
      <i class="fa-solid fa-list"></i>
    </button>
    <button class="btn btn-primary" onclick="openModal()">
      <i class="fa-solid fa-plus"></i> Thêm danh mục
    </button>
  </div>
</div>

<!-- ============================================================
     GRID VIEW
============================================================= -->
<div id="catGrid" class="cat-grid">
  <?php foreach ($categories as $i => $c):
    // Data truyền sang JS — dùng đúng key JS đang expect
    $cJson = json_encode([
      'id'     => (int)$c['id'],
      'name'   => $c['name'],
      'desc'   => $c['desc'] ?? '',
      'status' => (int)$c['status'],
    ]);
  ?>
    <div class="cat-card"
      data-id="<?= $c['id'] ?>"
      data-name="<?= htmlspecialchars($c['name'], ENT_QUOTES) ?>"
      data-status="<?= $c['status'] ?>"
      style="animation-delay:<?= round($i * 0.06, 2) ?>s">

      <div class="status-dot <?= $c['status'] ? 'dot-green' : 'dot-red' ?>"></div>

      <div class="cat-icon <?= $c['status'] ? '' : 'cat-icon-dim' ?>">
        <i class="fa-solid fa-tags"></i>
      </div>
      <div class="cat-name"><?= htmlspecialchars($c['name']) ?></div>

      <div class="cat-meta">
        <span class="cat-count">Sản phẩm: <b><?= number_format($c['count']) ?></b></span>
        <?= $c['status']
          ? '<span class="badge-green">● Hoạt động</span>'
          : '<span class="badge-red">● Ẩn</span>' ?>
      </div>

      <div class="cat-footer">
        <div class="action-btns">
          <button class="btn btn-secondary btn-sm"
            onclick='openModal(<?= $cJson ?>)' title="Sửa">
            <i class="fa-solid fa-pen"></i>
          </button>
          <button class="btn btn-sm <?= $c['status'] ? 'btn-toggle-off' : 'btn-toggle-on' ?>"
            onclick="toggleStatus(<?= $c['id'] ?>, this)"
            title="<?= $c['status'] ? 'Ẩn danh mục' : 'Kích hoạt' ?>">
            <i class="fa-solid <?= $c['status'] ? 'fa-eye-slash' : 'fa-eye' ?>"></i>
          </button>
          <button class="btn btn-danger btn-sm"
            onclick="confirmDelete(<?= $c['id'] ?>, '<?= addslashes($c['name']) ?>')"
            <?= $c['count'] > 0
              ? 'disabled title="Còn ' . $c['count'] . ' SP, không thể xoá"'
              : 'title="Xoá danh mục"' ?>>
            <i class="fa-solid fa-trash"></i>
          </button>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

  <!-- Nút thêm nhanh -->
  <div class="cat-add-btn" onclick="openModal()">
    <i class="fa-solid fa-plus"></i>
    <span>Thêm danh mục mới</span>
  </div>
</div>

<!-- ============================================================
     LIST VIEW (bảng)
============================================================= -->
<div id="catList" class="card" style="display:none">
  <div class="card-header">
    <div class="card-title">
      <i class="fa-solid fa-list"></i> Bảng danh mục
    </div>
    <span id="listCount" style="font-size:12px;color:var(--text-muted)">
      <?= $totalCat ?> danh mục
    </span>
  </div>
  <div style="overflow-x:auto">
    <table>
      <thead>
        <tr>
          <th style="width:50px">#</th>
          <th>Tên danh mục</th>
          <th style="text-align:center;width:80px">Số SP</th>
          <th style="text-align:center;width:120px">Trạng thái</th>
          <th style="width:145px">Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($categories as $c):
          $cJson = json_encode([
            'id'     => (int)$c['id'],
            'name'   => $c['name'],
            'desc'   => $c['desc'] ?? '',
            'status' => (int)$c['status'],
          ]);
        ?>
          <tr data-id="<?= $c['id'] ?>"
            data-name="<?= htmlspecialchars($c['name'], ENT_QUOTES) ?>"
            data-status="<?= $c['status'] ?>">

            <td style="font-family:var(--mono);color:var(--text-muted)"><?= $c['id'] ?></td>

            <td style="font-weight:600"><?= htmlspecialchars($c['name']) ?></td>

            <td style="text-align:center;font-family:var(--mono);color:var(--blue)">
              <?= number_format($c['count']) ?>
            </td>
            <td style="text-align:center">
              <?= $c['status']
                ? '<span class="badge-pill green">● Hoạt động</span>'
                : '<span class="badge-pill red">● Ẩn</span>' ?>
            </td>
            <td>
              <div class="action-btns">
                <button class="btn btn-secondary btn-sm"
                  onclick='openModal(<?= $cJson ?>)' title="Sửa">
                  <i class="fa-solid fa-pen"></i>
                </button>
                <button class="btn btn-sm <?= $c['status'] ? 'btn-toggle-off' : 'btn-toggle-on' ?>"
                  onclick="toggleStatus(<?= $c['id'] ?>, this)"
                  title="<?= $c['status'] ? 'Ẩn' : 'Kích hoạt' ?>">
                  <i class="fa-solid <?= $c['status'] ? 'fa-eye-slash' : 'fa-eye' ?>"></i>
                </button>
                <button class="btn btn-danger btn-sm"
                  onclick="confirmDelete(<?= $c['id'] ?>, '<?= addslashes($c['name']) ?>')"
                  <?= $c['count'] > 0
                    ? 'disabled title="Còn ' . $c['count'] . ' SP"'
                    : '' ?>>
                  <i class="fa-solid fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ============================================================
     MODAL — HTML khớp CHÍNH XÁC với categories.js
     JS cần: cId, cName, cDesc, cStatusToggle, cStatus(hidden),
             statusLabel, nameError, btnSave, catModal, modalTitle
============================================================= -->
<div class="modal-overlay" id="catModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="modalTitle">Thêm danh mục</div>
      <button class="btn-close-modal" onclick="closeModal()">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <div class="modal-body">
      <!-- ID ẩn -->
      <input type="hidden" id="cId">

      <!-- Tên danh mục -->
      <div class="form-group">
        <label class="form-label">
          Tên danh mục <span style="color:var(--red)">*</span>
        </label>
        <input type="text" class="form-control" id="cName"
          placeholder="VD: Điện thoại, Laptop..." maxlength="255"
          onkeydown="if(event.key==='Enter') saveCat()">
        <!-- JS dùng element này để hiện lỗi -->
        <div id="nameError"
          style="display:none;color:#f85149;font-size:12px;margin-top:5px;font-weight:600">
        </div>
      </div>

      <!-- Trạng thái — toggle switch (JS tìm cStatusToggle + statusLabel + cStatus hidden) -->
      <div class="form-group" style="margin-bottom:0">
        <label class="form-label">Trạng thái</label>
        <div class="toggle-wrap">
          <label class="toggle-switch">
            <input type="checkbox" id="cStatusToggle" checked onchange="syncStatus()">
            <span class="toggle-slider"></span>
          </label>
          <span id="statusLabel"
            style="font-size:13px;font-weight:700;color:var(--green)">
            Hoạt động
          </span>
          <!-- Giá trị thực JS đọc khi submit -->
          <input type="hidden" id="cStatus" value="1">
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal()">Huỷ</button>
      <!-- id="btnSave" là bắt buộc, JS tìm element này để loading state -->
      <button class="btn btn-primary" id="btnSave" onclick="saveCat()">
        <i class="fa-solid fa-floppy-disk"></i> Lưu
      </button>
    </div>
  </div>
</div>

<script src="assets/js/categories.js"></script>
<?php
// $conn->close();
require_once 'includes/layout_footer.php'; ?>




<!-- php code -->

<?php

include_once 'connectDB.php';
include_once 'object_status.php';
function add_category($loai_san_pham, $trang_thai)
{
  global $conn;

  try {
    
    $res = $conn->query("INSERT INTO danhmuc(LOAISP, TRANGTHAI) VALUES ($loai_san_pham, $trang_thai);");
  } catch (mysqli_sql_exception $e) {
  }
}

?>