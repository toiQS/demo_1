<?php
// views/category.php
$currentPage = 'category';
$pageTitle   = 'DANH MỤC';
$breadcrumb  = 'Quản lý / Danh mục';

// ── Load services ──────────────────────────────────────────
include_once __DIR__ . '/../database/db.php';
include_once __DIR__ . '/../services/categories/get_start_category.php';
include_once __DIR__ . '/../services/categories/get_category_list.php';
include_once __DIR__ . '/../services/categories/add_category.php';
include_once __DIR__ . '/../services/categories/update_category.php';
include_once __DIR__ . '/../services/categories/remove_category.php';

// ── Xử lý action ──────────────────────────────────────────
$flash  = null;
$action = $_REQUEST['action'] ?? '';
$id     = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;

// Xoá
if ($action === 'delete' && $id) {
    $r = deleteCategory($pdo, $id);
    $flash  = ['type' => $r['ok'] ? 'success' : 'error', 'msg' => $r['msg'], 'form' => ''];
    $action = '';
}
// Thêm (POST)
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $r = addCategory($pdo, $_POST);
    if ($r['ok']) {
        $flash  = ['type' => 'success', 'msg' => $r['msg'], 'form' => ''];
        $action = '';
    } else {
        $flash = ['type' => 'error', 'msg' => $r['msg'], 'form' => 'add'];
    }
}
// Sửa (POST)
if ($action === 'edit' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $r = updateCategory($pdo, $id, $_POST);
    if ($r['ok']) {
        $flash  = ['type' => 'success', 'msg' => $r['msg'], 'form' => ''];
        $action = '';
    } else {
        $flash = ['type' => 'error', 'msg' => $r['msg'], 'form' => 'edit'];
    }
}

// ── Dữ liệu cho view ──────────────────────────────────────
$filterKeyword = trim($_GET['keyword'] ?? '');
$categories    = getCategoryList($pdo, ['keyword' => $filterKeyword]);
$stats         = getCategoryStats($pdo);

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
        <div class="stat-label">Tổng danh mục</div>
        <div class="stat-value"><?= number_format($stats['total']) ?></div>
      </div>
      <div class="stat-icon" style="background:rgba(59,130,246,.15);font-size:20px">📂</div>
    </div>
  </div>

  <div class="stat-card" style="--card-color:var(--purple)">
    <div class="stat-top">
      <div>
        <div class="stat-label">Tổng sản phẩm</div>
        <div class="stat-value"><?= number_format($stats['total_san_pham']) ?></div>
      </div>
      <div class="stat-icon" style="background:rgba(168,85,247,.15);font-size:20px">📦</div>
    </div>
  </div>

  <div class="stat-card" style="--card-color:var(--green)">
    <div class="stat-top">
      <div>
        <div class="stat-label">Có hàng</div>
        <div class="stat-value"><?= number_format($stats['co_hang']) ?></div>
      </div>
      <div class="stat-icon" style="background:rgba(34,197,94,.15);font-size:20px">✅</div>
    </div>
  </div>

  <div class="stat-card" style="--card-color:var(--red)">
    <div class="stat-top">
      <div>
        <div class="stat-label">Hết hàng</div>
        <div class="stat-value"><?= number_format($stats['het_hang']) ?></div>
      </div>
      <div class="stat-icon" style="background:rgba(239,68,68,.15);font-size:20px">❗</div>
    </div>
  </div>

</div><!-- /.stat-grid -->


<!-- ── Tiêu đề + Bộ lọc + Nút thêm ───────────────────────── -->
<div class="section-head">
  <div class="section-title">QUẢN LÝ DANH MỤC</div>

  <div style="display:flex;gap:8px;align-items:center">

    <form method="GET" style="display:flex;gap:8px;align-items:center">
      <div style="display:flex;align-items:center;gap:6px;
                  background:var(--surface2);border:1px solid var(--border);
                  border-radius:7px;padding:5px 12px">
        <span style="color:var(--text3);font-size:13px">🔍</span>
        <input type="text" name="keyword"
               value="<?= htmlspecialchars($filterKeyword) ?>"
               placeholder="Tìm danh mục…"
               style="background:none;border:none;outline:none;color:var(--text);
                      font-size:13px;font-family:'DM Sans',sans-serif;width:160px"
               onkeydown="if(event.key==='Enter')this.form.submit()">
      </div>
    </form>

    <button class="btn-sm" onclick="openModal('modalAddCat')">+ Thêm danh mục</button>

  </div>
</div>


<!-- ── Bảng danh sách ─────────────────────────────────────── -->
<div class="panel">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Tên danh mục</th>
          <th>Tổng SP</th>
          <th>SP đang bán</th>
          <th>Tồn kho</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>

        <?php if (empty($categories)): ?>
          <tr>
            <td colspan="6"
                style="text-align:center;color:var(--text3);padding:36px;
                       font-family:'JetBrains Mono',monospace;font-size:13px">
              — Không có danh mục nào —
            </td>
          </tr>

        <?php else: ?>
          <?php foreach ($categories as $cat): ?>
          <tr>
            <td class="td-mono td-muted">
              <?= str_pad($cat['idDM'], 2, '0', STR_PAD_LEFT) ?>
            </td>

            <td class="td-main"><?= htmlspecialchars($cat['LOAISP']) ?></td>

            <td class="td-mono" style="color:var(--text2)">
              <?= number_format($cat['tong_sp']) ?>
            </td>

            <td class="td-mono" style="color:var(--green)">
              <?= number_format($cat['sp_active']) ?>
            </td>

            <td class="td-mono"
                style="color:<?= $cat['tong_ton_kho'] > 0 ? 'var(--accent)' : 'var(--red)' ?>">
              <?= number_format($cat['tong_ton_kho']) ?>
            </td>

            <td style="white-space:nowrap">
              <!-- Sửa -->
              <button type="button" class="action-link btn-edit-cat"
                      data-id="<?= $cat['idDM'] ?>"
                      data-loaisp="<?= htmlspecialchars($cat['LOAISP'], ENT_QUOTES|ENT_HTML5, 'UTF-8') ?>">
                Sửa
              </button>

              <!-- Xoá (chỉ khi không có SP) -->
              <?php if ((int)$cat['tong_sp'] === 0): ?>
                <button type="button" class="action-link del btn-delete-cat"
                        data-id="<?= $cat['idDM'] ?>"
                        data-loaisp="<?= htmlspecialchars($cat['LOAISP'], ENT_QUOTES|ENT_HTML5, 'UTF-8') ?>">
                  Xoá
                </button>
              <?php else: ?>
                <span style="color:var(--text3);font-size:11px;
                             font-family:'JetBrains Mono',monospace"
                      title="Có <?= $cat['tong_sp'] ?> SP, không thể xoá">
                  🔒 Có SP
                </span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>

      </tbody>
    </table>
  </div>

  <!-- Footer đếm -->
  <div style="padding:12px 20px;border-top:1px solid var(--border);
              font-size:12px;color:var(--text3);
              font-family:'JetBrains Mono',monospace">
    <?= count($categories) ?> danh mục
  </div>
</div>


<!-- ═══════════════════════════════════════════════════════════
     MODAL SUBVIEWS
     ══════════════════════════════════════════════════════════ -->
<?php include_once __DIR__ . '/subviews/categories/add.php'; ?>
<?php include_once __DIR__ . '/subviews/categories/update.php'; ?>
<?php include_once __DIR__ . '/subviews/categories/remove.php'; ?>


<!-- ── CSS modal ─────────────────────────────────────────────── -->
<style>
.u-modal-backdrop{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);
  backdrop-filter:blur(4px);z-index:900;align-items:center;justify-content:center;padding:16px;}
.u-modal-backdrop.u-modal--open{display:flex;}
.u-modal{background:var(--surface);border:1px solid var(--border);border-radius:14px;
  width:100%;max-width:420px;max-height:90vh;overflow-y:auto;padding:24px;
  box-shadow:0 30px 70px rgba(0,0,0,.6);animation:uModalIn .18s ease;}
.u-modal--sm{max-width:380px;}
@keyframes uModalIn{from{opacity:0;transform:translateY(-18px) scale(.97)}to{opacity:1;transform:none}}
.u-modal__head{display:flex;align-items:center;justify-content:space-between;
  margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid var(--border);}
.u-modal__title{font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1.5px;color:var(--text);}
.u-modal__close{background:none;border:none;color:var(--text2);font-size:16px;cursor:pointer;
  padding:4px 8px;border-radius:6px;transition:background .15s;}
.u-modal__close:hover{background:rgba(255,255,255,.08);color:var(--text);}
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
</style>


<!-- ── JS inline ──────────────────────────────────────────────── -->
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
    var btn = e.target.closest('.btn-edit-cat');
    if (!btn) return;
    document.getElementById('editCatName').value = btn.dataset.loaisp;
    document.getElementById('formEditCat').action = '?action=edit&id=' + btn.dataset.id;
    openModal('modalEditCat');
  });

  // Nút Xoá
  document.addEventListener('click', function(e) {
    var btn = e.target.closest('.btn-delete-cat');
    if (!btn) return;
    document.getElementById('deleteCatName').textContent = btn.dataset.loaisp;
    document.getElementById('formDeleteCat').action = '?action=delete&id=' + btn.dataset.id;
    openModal('modalDeleteCat');
  });

  // Tự mở lại modal nếu PHP báo lỗi
  var errForm = document.body.dataset.errorForm;
  if (errForm === 'add')  openModal('modalAddCat');
  if (errForm === 'edit') openModal('modalEditCat');

});
</script>

<?php require_once 'includes/footer.php'; ?>