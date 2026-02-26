<?php
$page_title    = 'Quản lý Danh mục';
$page_subtitle = 'Thêm & quản lý loại thiết bị điện tử';
$active_nav    = 'categories';


require_once 'services/category/gets.php';


$categories = $categories ?? [];

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
    <a href="error.php" class="btn btn-secondary" title="Xem lỗi hệ thống" style="gap:6px">
      <i class="fa-solid fa-bug"></i>
      <?php
        // Hiển thị badge số lỗi nếu có trong log
        $errCount = 0;
        $logCheck = __DIR__ . '/logs/index/dashboard.text';
        if (file_exists($logCheck) && filesize($logCheck) > 0) {
            $lines = file($logCheck, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $errCount = count($lines);
        }
      ?>
      <?php if ($errCount > 0): ?>
        <span style="background:var(--red);color:#fff;font-size:10px;font-weight:700;
                     padding:1px 6px;border-radius:10px;min-width:18px;text-align:center">
          <?= $errCount ?>
        </span>
      <?php endif; ?>
    </a>
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
  <?php if (empty($categories)): ?>
    <div style="grid-column:1/-1;text-align:center;padding:48px 0;color:var(--text-muted)">
      <i class="fa-solid fa-tags" style="font-size:36px;margin-bottom:12px;display:block;opacity:.3"></i>
      Chưa có danh mục nào. Hãy thêm danh mục đầu tiên!
    </div>
  <?php else: ?>
    <?php foreach ($categories as $i => $c):
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

        <?php if (!empty($c['desc'])): ?>
          <div class="cat-desc"><?= htmlspecialchars($c['desc']) ?></div>
        <?php endif; ?>

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
  <?php endif; ?>

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
          <th>Mô tả</th>
          <th style="text-align:center;width:80px">Số SP</th>
          <th style="text-align:center;width:120px">Trạng thái</th>
          <th style="width:145px">Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($categories)): ?>
          <tr>
            <td colspan="6" style="text-align:center;padding:32px;color:var(--text-muted)">
              Chưa có danh mục nào.
            </td>
          </tr>
        <?php else: ?>
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
              <td style="color:var(--text-muted);font-size:12px;max-width:240px;
                          overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                <?= htmlspecialchars($c['desc'] ?? '') ?>
              </td>
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
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ============================================================
     MODAL
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
      <input type="hidden" id="cId">

      <div class="form-group">
        <label class="form-label">
          Tên danh mục <span style="color:var(--red)">*</span>
        </label>
        <input type="text" class="form-control" id="cName"
          placeholder="VD: Điện thoại, Laptop..." maxlength="255"
          onkeydown="if(event.key==='Enter') saveCat()">
        <div id="nameError"
          style="display:none;color:#f85149;font-size:12px;margin-top:5px;font-weight:600">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Mô tả
          <span style="color:var(--text-muted);font-weight:400;text-transform:none">
            (không lưu vào DB – chỉ hiển thị UI)
          </span>
        </label>
        <textarea class="form-control" id="cDesc" rows="3"
          placeholder="Mô tả ngắn về danh mục..."
          style="resize:vertical"></textarea>
      </div>

      <div class="form-group" style="margin-bottom:0">
        <label class="form-label">Trạng thái
          <span style="color:var(--text-muted);font-weight:400;text-transform:none">
            (chưa lưu vào DB)
          </span>
        </label>
        <div class="toggle-wrap">
          <label class="toggle-switch">
            <input type="checkbox" id="cStatusToggle" checked onchange="syncStatus()">
            <span class="toggle-slider"></span>
          </label>
          <span id="statusLabel"
            style="font-size:13px;font-weight:700;color:var(--green)">
            Hoạt động
          </span>
          <input type="hidden" id="cStatus" value="1">
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal()">Huỷ</button>
      <button class="btn btn-primary" id="btnSave" onclick="saveCat()">
        <i class="fa-solid fa-floppy-disk"></i> Lưu
      </button>
    </div>
  </div>
</div>

<script src="assets/js/categories.js"></script>
<?php
if (isset($conn)) $conn->close();
require_once 'includes/layout_footer.php';
?>
