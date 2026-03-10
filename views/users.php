<?php
// views/users.php
$currentPage = 'users';
$pageTitle   = 'NGƯỜI DÙNG';
$breadcrumb  = 'Quản lý / Người dùng';

// ── Load services ──────────────────────────────────────────
include_once __DIR__ . '/../database/db.php';
include_once __DIR__ . '/../services/users/get_user_start.php';
include_once __DIR__ . '/../services/users/get_user_list.php';
include_once __DIR__ . '/../services/users/add_user.php';
include_once __DIR__ . '/../services/users/update_user.php';
include_once __DIR__ . '/../services/users/remove_user.php';

// ── Xử lý action ──────────────────────────────────────────
// $flash = ['type'=>'success'|'error', 'msg'=>string, 'form'=>'add'|'edit'|'']
$flash  = null;
$action = $_REQUEST['action'] ?? '';
$id     = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;

// Khoá
if ($action === 'lock' && $id) {
    $r = lockUser($pdo, $id);
    $flash  = ['type' => $r['ok'] ? 'success' : 'error', 'msg' => $r['msg'], 'form' => ''];
    $action = '';
}
// Mở khoá
if ($action === 'unlock' && $id) {
    $r = unlockUser($pdo, $id);
    $flash  = ['type' => $r['ok'] ? 'success' : 'error', 'msg' => $r['msg'], 'form' => ''];
    $action = '';
}
// Xoá
if ($action === 'delete' && $id) {
    $r = deleteUser($pdo, $id);
    $flash  = ['type' => $r['ok'] ? 'success' : 'error', 'msg' => $r['msg'], 'form' => ''];
    $action = '';
}
// Thêm (POST)
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $r = addUser($pdo, $_POST);
    if ($r['ok']) {
        $flash  = ['type' => 'success', 'msg' => $r['msg'], 'form' => ''];
        $action = '';
    } else {
        $flash = ['type' => 'error', 'msg' => $r['msg'], 'form' => 'add'];
        // giữ $action='add' → JS tự mở lại modal
    }
}
// Sửa (POST)
if ($action === 'edit' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $r = updateUser($pdo, $id, $_POST);
    if ($r['ok']) {
        $flash  = ['type' => 'success', 'msg' => $r['msg'], 'form' => ''];
        $action = '';
    } else {
        $flash = ['type' => 'error', 'msg' => $r['msg'], 'form' => 'edit'];
    }
}

// ── Dữ liệu cho view ──────────────────────────────────────
$filterPhanloai  = $_GET['phanloai']  ?? '';
$filterTrangthai = $_GET['trangthai'] ?? '';
$filterKeyword   = trim($_GET['keyword'] ?? '');

$users     = getUserList($pdo, [
    'phanloai'  => $filterPhanloai,
    'trangthai' => $filterTrangthai,
    'keyword'   => $filterKeyword,
]);
$userTypes = getUserTypes($pdo);
$stats     = getUserStats($pdo);

require_once 'includes/header.php';
?>

<!-- Truyền trạng thái lỗi sang user.js qua data-* -->
<script>
  document.body.dataset.errorForm = '<?= $flash['form'] ?? '' ?>';
  document.body.dataset.editId    = '<?= $id ?>';
</script>

<!-- ── Flash message toàn trang ───────────────────────────── -->
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
        <div class="stat-label">Tổng người dùng</div>
        <div class="stat-value"><?= number_format($stats['total_users']) ?></div>
      </div>
      <div class="stat-icon" style="background:rgba(59,130,246,.15);font-size:20px">👥</div>
    </div>
  </div>

  <div class="stat-card" style="--card-color:var(--green)">
    <div class="stat-top">
      <div>
        <div class="stat-label">Hoạt động</div>
        <div class="stat-value"><?= number_format($stats['activity_users']) ?></div>
      </div>
      <div class="stat-icon" style="background:rgba(34,197,94,.15);font-size:20px">✅</div>
    </div>
  </div>

  <div class="stat-card" style="--card-color:var(--red)">
    <div class="stat-top">
      <div>
        <div class="stat-label">Đã khoá</div>
        <div class="stat-value"><?= number_format($stats['activity_lock']) ?></div>
      </div>
      <div class="stat-icon" style="background:rgba(239,68,68,.15);font-size:20px">🔒</div>
    </div>
  </div>

  <div class="stat-card" style="--card-color:var(--accent)">
    <div class="stat-top">
      <div>
        <div class="stat-label">Quản lý</div>
        <div class="stat-value"><?= number_format($stats['admin_counts']) ?></div>
      </div>
      <div class="stat-icon" style="background:rgba(245,166,35,.15);font-size:20px">🛡️</div>
    </div>
  </div>

</div><!-- /.stat-grid -->


<!-- ── Thanh tiêu đề + bộ lọc + nút thêm ─────────────────── -->
<div class="section-head">
  <div class="section-title">QUẢN LÝ NGƯỜI DÙNG</div>

  <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">

    <!-- Bộ lọc (GET form, tự submit khi đổi) -->
    <form method="GET" style="display:flex;gap:8px;align-items:center">

      <!-- Tìm kiếm theo tên/username/email -->
      <div style="display:flex;align-items:center;gap:6px;
                  background:var(--surface2);border:1px solid var(--border);
                  border-radius:7px;padding:5px 12px">
        <span style="color:var(--text3);font-size:13px">🔍</span>
        <input type="text" name="keyword" value="<?= htmlspecialchars($filterKeyword) ?>"
               placeholder="Tìm tên, username, email…"
               style="background:none;border:none;outline:none;color:var(--text);
                      font-size:13px;font-family:'DM Sans',sans-serif;width:180px;"
               onkeydown="if(event.key==='Enter')this.form.submit()">
      </div>

      <select class="filter-select" name="phanloai" onchange="this.form.submit()">
        <option value="">Tất cả loại</option>
        <?php foreach ($userTypes as $ut): ?>
          <option value="<?= $ut['idType'] ?>"
            <?= ($filterPhanloai == $ut['idType']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($ut['Ten']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <select class="filter-select" name="trangthai" onchange="this.form.submit()">
        <option value="">Trạng thái</option>
        <option value="1" <?= ($filterTrangthai === '1') ? 'selected' : '' ?>>Hoạt động</option>
        <option value="0" <?= ($filterTrangthai === '0') ? 'selected' : '' ?>>Đã khoá</option>
      </select>

    </form>

    <!-- Nút mở modal Thêm -->
    <button class="btn-sm" onclick="openAddModal()">+ Thêm người dùng</button>

  </div>
</div>


<!-- ── Bảng danh sách ─────────────────────────────────────── -->
<div class="panel">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Họ tên</th>
          <th>Username</th>
          <th>Email</th>
          <th>SĐT</th>
          <th>Loại</th>
          <th>Trạng thái</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>

        <?php if (empty($users)): ?>
          <tr>
            <td colspan="8"
                style="text-align:center;color:var(--text3);
                       padding:40px;font-family:'JetBrains Mono',monospace;
                       font-size:13px;">
              — Không có người dùng nào —
            </td>
          </tr>

        <?php else: ?>
          <?php foreach ($users as $u): ?>
          <tr>
            <td class="td-mono td-muted">
              <?= str_pad($u['idTK'], 3, '0', STR_PAD_LEFT) ?>
            </td>

            <td class="td-main"><?= htmlspecialchars($u['HOTEN']) ?></td>

            <td class="td-mono" style="color:var(--text2)">
              <?= htmlspecialchars($u['USERNAME']) ?>
            </td>

            <td style="font-size:13px;color:var(--text2)">
              <?= htmlspecialchars($u['EMAIL']) ?>
            </td>

            <td class="td-mono"><?= htmlspecialchars($u['SDT']) ?></td>

            <td>
              <?php if ($u['PHANLOAI'] == 2): ?>
                <span class="badge badge-amber">Quản lý</span>
              <?php else: ?>
                <span class="badge badge-blue">Khách hàng</span>
              <?php endif; ?>
            </td>

            <td>
              <?php if ($u['TRANGTHAI']): ?>
                <span class="badge badge-green">Hoạt động</span>
              <?php else: ?>
                <span class="badge badge-red">Đã khoá</span>
              <?php endif; ?>
            </td>

            <td style="white-space:nowrap">

              <!-- Sửa → data-* attributes, JS đọc trong handler -->
              <button type="button" class="action-link btn-edit-user"
                data-id="<?= $u['idTK'] ?>"
                data-hoten="<?= htmlspecialchars($u['HOTEN'],    ENT_QUOTES|ENT_HTML5, 'UTF-8') ?>"
                data-username="<?= htmlspecialchars($u['USERNAME'], ENT_QUOTES|ENT_HTML5, 'UTF-8') ?>"
                data-email="<?= htmlspecialchars($u['EMAIL'],    ENT_QUOTES|ENT_HTML5, 'UTF-8') ?>"
                data-sdt="<?= htmlspecialchars($u['SDT'],        ENT_QUOTES|ENT_HTML5, 'UTF-8') ?>"
                data-address="<?= htmlspecialchars($u['ADDRESS'], ENT_QUOTES|ENT_HTML5, 'UTF-8') ?>"
                data-phanloai="<?= (int)$u['PHANLOAI'] ?>">Sửa</button>

              <!-- Khoá / Mở khoá -->
              <?php if ($u['TRANGTHAI']): ?>
                <a href="?action=lock&id=<?= $u['idTK'] ?>"
                   class="action-link del btn-lock-user"
                   data-username="<?= htmlspecialchars($u['USERNAME'], ENT_QUOTES|ENT_HTML5, 'UTF-8') ?>">
                  Khoá
                </a>
              <?php else: ?>
                <a href="?action=unlock&id=<?= $u['idTK'] ?>"
                   class="action-link success btn-unlock-user"
                   data-username="<?= htmlspecialchars($u['USERNAME'], ENT_QUOTES|ENT_HTML5, 'UTF-8') ?>">
                  Mở khoá
                </a>
              <?php endif; ?>

              <!-- Xoá → data-* attributes -->
              <button type="button" class="action-link del btn-delete-user"
                data-id="<?= $u['idTK'] ?>"
                data-username="<?= htmlspecialchars($u['USERNAME'], ENT_QUOTES|ENT_HTML5, 'UTF-8') ?>">Xoá</button>

            </td>
          </tr>
          <?php endforeach; ?>

        <?php endif; ?>

      </tbody>
    </table>
  </div><!-- /.table-wrap -->

  <!-- Footer bảng: đếm kết quả -->
  <div style="padding:12px 20px;border-top:1px solid var(--border);
              font-size:12px;color:var(--text3);
              font-family:'JetBrains Mono',monospace">
    Hiển thị <?= count($users) ?> / <?= number_format($stats['total_users']) ?> người dùng
  </div>

</div><!-- /.panel -->


<!-- ══════════════════════════════════════════════════════════
     INCLUDE CÁC MODAL (subviews)
     ════════════════════════════════════════════════════════ -->
<?php include_once __DIR__ . '/subviews/users/add.php'; ?>
<?php include_once __DIR__ . '/subviews/users/update.php'; ?>
<?php include_once __DIR__ . '/subviews/users/delete.php'; ?>


<!-- ── CSS modal (thêm vào admin.css nếu muốn) ─────────────── -->
<style>
/* Backdrop */
.u-modal-backdrop {
  display: none;
  position: fixed; inset: 0;
  background: rgba(0,0,0,.6);
  backdrop-filter: blur(4px);
  z-index: 900;
  align-items: center;
  justify-content: center;
  padding: 16px;
}
.u-modal-backdrop.u-modal--open { display: flex; }

/* Box */
.u-modal {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 14px;
  width: 100%;
  max-width: 640px;
  max-height: 90vh;
  overflow-y: auto;
  padding: 24px;
  box-shadow: 0 30px 70px rgba(0,0,0,.6);
  animation: uModalIn .18s ease;
}
.u-modal--sm { max-width: 420px; }
.u-modal::-webkit-scrollbar { width: 3px; }
.u-modal::-webkit-scrollbar-thumb { background: var(--border); }

@keyframes uModalIn {
  from { opacity: 0; transform: translateY(-18px) scale(.97); }
  to   { opacity: 1; transform: none; }
}

/* Header */
.u-modal__head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
  padding-bottom: 14px;
  border-bottom: 1px solid var(--border);
}
.u-modal__title {
  font-family: 'Bebas Neue', sans-serif;
  font-size: 18px;
  letter-spacing: 1.5px;
  color: var(--text);
}
.u-modal__close {
  background: none; border: none;
  color: var(--text2); font-size: 16px;
  cursor: pointer; padding: 4px 8px;
  border-radius: 6px; line-height: 1;
  transition: background .15s, color .15s;
}
.u-modal__close:hover {
  background: rgba(255,255,255,.08);
  color: var(--text);
}

/* Grid 2 cột */
.u-modal__grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
}
.u-full-col { grid-column: 1 / -1; }

/* Alert trong modal */
.u-modal__alert {
  padding: 10px 14px;
  border-radius: 8px;
  font-size: 13px;
  margin-bottom: 16px;
  font-family: 'DM Sans', sans-serif;
}
.u-modal__alert--error   { background:rgba(239,68,68,.12);  color:var(--red);   border:1px solid rgba(239,68,68,.3);  }
.u-modal__alert--success { background:rgba(34,197,94,.12);  color:var(--green); border:1px solid rgba(34,197,94,.3);  }

/* Footer */
.u-modal__foot {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
  padding-top: 16px;
  border-top: 1px solid var(--border);
}

/* Flash toàn trang */
.u-flash {
  padding: 12px 18px;
  border-radius: 9px;
  font-size: 13px;
  font-family: 'DM Sans', sans-serif;
  margin-bottom: 4px;
  animation: fadeUp .3s ease;
}
.u-flash--success { background:rgba(34,197,94,.12);  color:var(--green); border:1px solid rgba(34,197,94,.3); }
.u-flash--error   { background:rgba(239,68,68,.12);  color:var(--red);   border:1px solid rgba(239,68,68,.3); }

/* Dấu * bắt buộc */
.u-req { color: var(--red); }

/* Nút dạng link trong bảng */
button.action-link {
  background: none; border: none;
  padding: 0; cursor: pointer;
  font-size: inherit;
  font-family: 'JetBrains Mono', monospace;
}

@media (max-width: 560px) {
  .u-modal__grid { grid-template-columns: 1fr; }
  .u-modal { padding: 16px; }
}
</style>

<!-- ── JS modal (inline, không phụ thuộc đường dẫn file) ──── -->
<script>
/* ── Helpers mở / đóng ───────────────────────────────────── */
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

/* ── Modal Thêm ──────────────────────────────────────────── */
function openAddModal() { openModal('modalAdd'); }

/* ── Modal Sửa ───────────────────────────────────────────── */
function openEditModal(id, hoten, username, email, sdt, address, phanloai) {
  document.getElementById('editHoten').value    = hoten;
  document.getElementById('editUsername').value = username;
  document.getElementById('editEmail').value    = email;
  document.getElementById('editSdt').value      = sdt;
  document.getElementById('editAddress').value  = address;
  var sel = document.getElementById('editPhanloai');
  for (var i = 0; i < sel.options.length; i++) {
    sel.options[i].selected = (parseInt(sel.options[i].value) === parseInt(phanloai));
  }
  document.getElementById('formEdit').action = '?action=edit&id=' + id;
  openModal('modalEdit');
}

/* ── Modal Xoá ───────────────────────────────────────────── */
function openDeleteModal(id, username) {
  document.getElementById('deleteUsername').textContent = username;
  document.getElementById('formDelete').action = '?action=delete&id=' + id;
  openModal('modalDelete');
}

/* ── Event delegation — đọc data-* attributes ────────────── */
document.addEventListener('DOMContentLoaded', function() {

  // Nút Sửa
  document.addEventListener('click', function(e) {
    var btn = e.target.closest('.btn-edit-user');
    if (!btn) return;
    var d = btn.dataset;
    openEditModal(
      d.id, d.hoten, d.username,
      d.email, d.sdt, d.address, d.phanloai
    );
  });

  // Nút Xoá
  document.addEventListener('click', function(e) {
    var btn = e.target.closest('.btn-delete-user');
    if (!btn) return;
    openDeleteModal(btn.dataset.id, btn.dataset.username);
  });

  // Link Khoá
  document.addEventListener('click', function(e) {
    var a = e.target.closest('.btn-lock-user');
    if (!a) return;
    if (!confirm('Khoá tài khoản «' + a.dataset.username + '»?')) e.preventDefault();
  });

  // Link Mở khoá
  document.addEventListener('click', function(e) {
    var a = e.target.closest('.btn-unlock-user');
    if (!a) return;
    if (!confirm('Mở khoá «' + a.dataset.username + '»?')) e.preventDefault();
  });

  // Tự mở lại modal nếu PHP báo lỗi form
  var errForm = document.body.dataset.errorForm;
  if (errForm === 'add')  openModal('modalAdd');
  if (errForm === 'edit') openModal('modalEdit');
});
</script>

<?php require_once 'includes/footer.php'; ?>