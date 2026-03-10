<?php
// views/promo.php
$currentPage = 'promo';
$pageTitle   = 'KHUYẾN MÃI';
$breadcrumb  = 'Tiện ích / Khuyến mãi';

// ── Load services ──────────────────────────────────────────
include_once __DIR__ . '/../database/db.php';
include_once __DIR__ . '/../services/promos/get_promo_start.php';
include_once __DIR__ . '/../services/promos/get_promo_list.php';
include_once __DIR__ . '/../services/promos/add_promo.php';
include_once __DIR__ . '/../services/promos/update_promo.php';
include_once __DIR__ . '/../services/promos/remove_promo.php';

// ── Xử lý action ──────────────────────────────────────────
$flash  = null;
$action = $_REQUEST['action'] ?? '';
$id     = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;

// Bật / Tắt toggle
if ($action === 'toggle' && $id) {
    $r = togglePromo($pdo, $id);
    $flash  = ['type' => $r['ok'] ? 'success' : 'error', 'msg' => $r['msg'], 'form' => ''];
    $action = '';
}

// Xoá
if ($action === 'delete' && $id) {
    $r = deletePromo($pdo, $id);
    $flash  = ['type' => $r['ok'] ? 'success' : 'error', 'msg' => $r['msg'], 'form' => ''];
    $action = '';
}

// Thêm (POST)
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $r = addPromo($pdo, $_POST);
    if ($r['ok']) {
        $flash  = ['type' => 'success', 'msg' => $r['msg'], 'form' => ''];
        $action = '';
    } else {
        $flash = ['type' => 'error', 'msg' => $r['msg'], 'form' => 'add'];
    }
}

// Sửa (POST)
if ($action === 'edit' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $r = updatePromo($pdo, $id, $_POST);
    if ($r['ok']) {
        $flash  = ['type' => 'success', 'msg' => $r['msg'], 'form' => ''];
        $action = '';
    } else {
        $flash = ['type' => 'error', 'msg' => $r['msg'], 'form' => 'edit'];
    }
}

// ── Dữ liệu cho view ──────────────────────────────────────
$filterTrangthai = $_GET['trangthai'] ?? '';
$filterKeyword   = trim($_GET['keyword'] ?? '');

$promos = getPromoList($pdo, [
    'trangthai' => $filterTrangthai,
    'keyword'   => $filterKeyword,
]);
$stats  = getPromoStats($pdo);
$today  = date('Y-m-d');

require_once 'includes/header.php';
?>

<!-- Truyền trạng thái lỗi cho JS -->
<script>
  document.body.dataset.errorForm = '<?= $flash['form'] ?? '' ?>';
</script>

<!-- ── Flash message ──────────────────────────────────────── -->
<?php if ($flash && $flash['form'] === ''): ?>
  <div class="u-flash u-flash--<?= $flash['type'] ?>">
    <?= $flash['type'] === 'success' ? '✅' : '❌' ?>
    <?= htmlspecialchars($flash['msg']) ?>
  </div>
<?php endif; ?>


<!-- ── Stat Cards ─────────────────────────────────────────── -->
<div class="stat-grid">

  <div class="stat-card" style="--card-color:var(--blue)">
    <div class="stat-top">
      <div>
        <div class="stat-label">Tổng mã KM</div>
        <div class="stat-value"><?= number_format($stats['total']) ?></div>
      </div>
      <div class="stat-icon" style="background:rgba(59,130,246,.15);font-size:20px">🎫</div>
    </div>
  </div>

  <div class="stat-card" style="--card-color:var(--green)">
    <div class="stat-top">
      <div>
        <div class="stat-label">Đang chạy</div>
        <div class="stat-value"><?= number_format($stats['running']) ?></div>
      </div>
      <div class="stat-icon" style="background:rgba(34,197,94,.15);font-size:20px">▶️</div>
    </div>
  </div>

  <div class="stat-card" style="--card-color:var(--accent)">
    <div class="stat-top">
      <div>
        <div class="stat-label">Tạm dừng</div>
        <div class="stat-value"><?= number_format($stats['paused']) ?></div>
      </div>
      <div class="stat-icon" style="background:rgba(245,166,35,.15);font-size:20px">⏸️</div>
    </div>
  </div>

  <div class="stat-card" style="--card-color:var(--red)">
    <div class="stat-top">
      <div>
        <div class="stat-label">Hết hạn</div>
        <div class="stat-value"><?= number_format($stats['expired']) ?></div>
      </div>
      <div class="stat-icon" style="background:rgba(239,68,68,.15);font-size:20px">⌛</div>
    </div>
  </div>

</div><!-- /.stat-grid -->


<!-- ── Tiêu đề + Bộ lọc ───────────────────────────────────── -->
<div class="section-head">
  <div class="section-title">QUẢN LÝ KHUYẾN MÃI</div>

  <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">

    <form method="GET" style="display:flex;gap:8px;align-items:center">

      <!-- Tìm theo code -->
      <div style="display:flex;align-items:center;gap:6px;
                  background:var(--surface2);border:1px solid var(--border);
                  border-radius:7px;padding:5px 12px">
        <span style="color:var(--text3);font-size:13px">🔍</span>
        <input type="text" name="keyword"
               value="<?= htmlspecialchars($filterKeyword) ?>"
               placeholder="Tìm mã CODE…"
               style="background:none;border:none;outline:none;color:var(--text);
                      font-size:13px;font-family:'JetBrains Mono',monospace;
                      width:140px;text-transform:uppercase"
               onkeydown="if(event.key==='Enter')this.form.submit()">
      </div>

      <select class="filter-select" name="trangthai" onchange="this.form.submit()">
        <option value="">Tất cả trạng thái</option>
        <option value="0" <?= ($filterTrangthai === '0') ? 'selected' : '' ?>>Đang chạy</option>
        <option value="1" <?= ($filterTrangthai === '1') ? 'selected' : '' ?>>Tạm dừng</option>
      </select>

    </form>

    <button class="btn-sm" onclick="openModal('modalAddPromo')">+ Tạo mã mới</button>

  </div>
</div>


<!-- ── Lưới card khuyến mãi ───────────────────────────────── -->
<?php if (empty($promos)): ?>
  <div class="panel" style="padding:40px;text-align:center;
       color:var(--text3);font-family:'JetBrains Mono',monospace;font-size:13px">
    — Không có mã khuyến mãi nào —
  </div>

<?php else: ?>
  <div class="promo-grid">
    <?php foreach ($promos as $p):
      $isRunning = ($p['TRANGTHAI'] == 0 && $p['HANSUDUNG'] >= $today);
      $isExpired = ($p['HANSUDUNG'] < $today);
      $pct       = round($p['GIATRI'] * 100);

      // Format ngày dd/mm/yyyy
      $ngayFmt = function(string $d): string {
        [$y, $m, $d2] = explode('-', $d);
        return "{$d2}/{$m}/{$y}";
      };
    ?>
    <div class="promo-card <?= ($p['TRANGTHAI'] == 1 || $isExpired) ? 'promo-disabled' : '' ?>"
         style="flex-direction:column;gap:12px;position:relative">

      <!-- Dòng 1: CODE + badge trạng thái -->
      <div style="display:flex;align-items:center;justify-content:space-between">
        <div class="promo-code"><?= htmlspecialchars($p['CODE']) ?></div>
        <?php if ($isExpired): ?>
          <span class="badge badge-red">Hết hạn</span>
        <?php elseif ($isRunning): ?>
          <span class="badge badge-green">Đang chạy</span>
        <?php else: ?>
          <span class="badge badge-gray">Tạm dừng</span>
        <?php endif; ?>
      </div>

      <!-- Dòng 2: Thông tin giảm + lượt -->
      <div style="display:flex;align-items:center;justify-content:space-between">
        <div>
          <div class="promo-discount">Giảm <?= $pct ?>%</div>
          <div class="promo-exp">
            <?= $ngayFmt($p['NGAYAPDUNG']) ?> – <?= $ngayFmt($p['HANSUDUNG']) ?>
          </div>
        </div>
        <div class="promo-meta" style="text-align:right">
          <div class="promo-qty"><?= number_format($p['SOLUONG']) ?> lượt</div>
          <div class="promo-exp">ID #<?= $p['MAKHUYENMAI'] ?></div>
        </div>
      </div>

      <!-- Dòng 3: Nút hành động -->
      <div style="display:flex;gap:10px;padding-top:8px;
                  border-top:1px solid var(--border)">

        <!-- Sửa -->
        <button type="button" class="action-link btn-edit-promo"
                data-id="<?= $p['MAKHUYENMAI'] ?>"
                data-code="<?= htmlspecialchars($p['CODE'],       ENT_QUOTES|ENT_HTML5,'UTF-8') ?>"
                data-giatri="<?= $p['GIATRI'] ?>"
                data-soluong="<?= $p['SOLUONG'] ?>"
                data-ngayapdung="<?= $p['NGAYAPDUNG'] ?>"
                data-hansudung="<?= $p['HANSUDUNG'] ?>"
                data-trangthai="<?= $p['TRANGTHAI'] ?>">
          ✏️ Sửa
        </button>

        <!-- Bật / Tắt -->
        <?php if (!$isExpired): ?>
          <a href="?action=toggle&id=<?= $p['MAKHUYENMAI'] ?>"
             class="action-link btn-toggle-promo <?= $isRunning ? 'del' : 'success' ?>"
             data-label="<?= $isRunning ? 'Tạm dừng' : 'Bật' ?> mã «<?= htmlspecialchars($p['CODE'], ENT_QUOTES|ENT_HTML5, 'UTF-8') ?>»?">
            <?= $isRunning ? '⏸️ Tắt' : '▶️ Bật' ?>
          </a>
        <?php endif; ?>

      </div>
    </div><!-- /.promo-card -->
    <?php endforeach; ?>
  </div><!-- /.promo-grid -->
<?php endif; ?>


<!-- ── Footer đếm ─────────────────────────────────────────── -->
<div style="font-size:12px;color:var(--text3);
            font-family:'JetBrains Mono',monospace;text-align:right">
  Hiển thị <?= count($promos) ?> / <?= number_format($stats['total']) ?> mã khuyến mãi
</div>


<!-- ═══════════════════════════════════════════════════════════
     MODAL SUBVIEWS
     ══════════════════════════════════════════════════════════ -->
<?php include_once __DIR__ . '/subviews/promos/add.php'; ?>
<?php include_once __DIR__ . '/subviews/promos/update.php'; ?>


<!-- ── CSS modal (dùng chung với users.php nếu đã có) ───────── -->
<style>
.u-modal-backdrop{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);
  backdrop-filter:blur(4px);z-index:900;align-items:center;justify-content:center;padding:16px;}
.u-modal-backdrop.u-modal--open{display:flex;}
.u-modal{background:var(--surface);border:1px solid var(--border);border-radius:14px;
  width:100%;max-width:560px;max-height:90vh;overflow-y:auto;padding:24px;
  box-shadow:0 30px 70px rgba(0,0,0,.6);animation:uModalIn .18s ease;}
.u-modal--sm{max-width:420px;}
.u-modal::-webkit-scrollbar{width:3px;}
.u-modal::-webkit-scrollbar-thumb{background:var(--border);}
@keyframes uModalIn{from{opacity:0;transform:translateY(-18px) scale(.97)}to{opacity:1;transform:none}}
.u-modal__head{display:flex;align-items:center;justify-content:space-between;
  margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid var(--border);}
.u-modal__title{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1.5px;color:var(--text);}
.u-modal__close{background:none;border:none;color:var(--text2);font-size:16px;cursor:pointer;
  padding:4px 8px;border-radius:6px;line-height:1;transition:background .15s,color .15s;}
.u-modal__close:hover{background:rgba(255,255,255,.08);color:var(--text);}
.u-modal__grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.u-full-col{grid-column:1/-1;}
.u-modal__alert{padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;}
.u-modal__alert--error{background:rgba(239,68,68,.12);color:var(--red);border:1px solid rgba(239,68,68,.3);}
.u-modal__alert--success{background:rgba(34,197,94,.12);color:var(--green);border:1px solid rgba(34,197,94,.3);}
.u-modal__foot{display:flex;justify-content:flex-end;gap:10px;
  margin-top:20px;padding-top:16px;border-top:1px solid var(--border);}
.u-flash{padding:12px 18px;border-radius:9px;font-size:13px;margin-bottom:4px;animation:fadeUp .3s ease;}
.u-flash--success{background:rgba(34,197,94,.12);color:var(--green);border:1px solid rgba(34,197,94,.3);}
.u-flash--error{background:rgba(239,68,68,.12);color:var(--red);border:1px solid rgba(239,68,68,.3);}
.u-req{color:var(--red);}
button.action-link{background:none;border:none;padding:0;cursor:pointer;
  font-size:inherit;font-family:'JetBrains Mono',monospace;}
@media(max-width:560px){.u-modal__grid{grid-template-columns:1fr}.u-modal{padding:16px}}
</style>


<!-- ── JS inline ─────────────────────────────────────────────── -->
<script>
/* ── Helpers ───────────────────────────────────────────────── */
function openModal(id) {
  var el = document.getElementById(id);
  if (!el) return;
  el.classList.add('u-modal--open');
  document.body.style.overflow = 'hidden';
}
function closeModal(id) {
  var el = document.getElementById(id);
  if (!el) return;
  el.classList.remove('u-modal--open');
  document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
  if (e.key !== 'Escape') return;
  document.querySelectorAll('.u-modal-backdrop.u-modal--open').forEach(function(m) {
    m.classList.remove('u-modal--open');
  });
  document.body.style.overflow = '';
});

/* ── Event delegation ─────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function() {

  // Nút Sửa
  document.addEventListener('click', function(e) {
    var btn = e.target.closest('.btn-edit-promo');
    if (!btn) return;
    var d = btn.dataset;
    document.getElementById('editPromoCode').value            = d.code;
    document.getElementById('editPromoGiatri').value          = d.giatri;
    document.getElementById('editPromoSoluong').value         = d.soluong;
    document.getElementById('editPromoNgay').value            = d.ngayapdung;
    document.getElementById('editPromoHan').value             = d.hansudung;
    document.getElementById('editPromoTrangthai').checked     = (d.trangthai === '1');
    document.getElementById('formEditPromo').action           = '?action=edit&id=' + d.id;
    openModal('modalEditPromo');
  });

  // Nút Xoá
  document.addEventListener('click', function(e) {
    var btn = e.target.closest('.btn-delete-promo');
    if (!btn) return;
    document.getElementById('deletePromoCode').textContent = btn.dataset.code;
    document.getElementById('formDeletePromo').action = '?action=delete&id=' + btn.dataset.id;
    openModal('modalDeletePromo');
  });

  // Link Bật/Tắt → xác nhận trước
  document.addEventListener('click', function(e) {
    var a = e.target.closest('.btn-toggle-promo');
    if (!a) return;
    if (!confirm(a.dataset.label)) e.preventDefault();
  });

  // Tự mở lại modal nếu PHP báo lỗi form
  var errForm = document.body.dataset.errorForm;
  if (errForm === 'add')  openModal('modalAddPromo');
  if (errForm === 'edit') openModal('modalEditPromo');

});
</script>

<?php require_once 'includes/footer.php'; ?>