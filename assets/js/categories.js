/**
 * assets/js/categories.js
 * Quản lý danh mục — View + AJAX actions
 * FIX: xử lý lỗi tốt hơn, redirect đến error.php khi server error
 */

const API      = 'controllers/categories_action.php';
const ERROR_PAGE = 'error.php';

/* ── Helpers ─────────────────────────────────────────────────── */

/**
 * Parse JSON an toàn. Nếu server trả về HTML (lỗi PHP 500),
 * trả về null và caller sẽ xử lý.
 */
async function safeFetch(url, options) {
  const res  = await fetch(url, options);
  const text = await res.text();

  // Server trả về lỗi HTTP
  if (!res.ok) {
    const errDetail = text.substring(0, 300).replace(/<[^>]+>/g, ' ').trim();
    throw new FetchError(res.status, res.statusText, errDetail);
  }

  // Thử parse JSON
  try {
    return JSON.parse(text);
  } catch (_) {
    // Server trả HTML (warning, notice hoặc fatal PHP error)
    const preview = text.substring(0, 500).replace(/<[^>]+>/g, ' ').trim();
    console.error('[categories.js] Server trả về HTML thay vì JSON:\n', preview);
    throw new FetchError(res.status, 'Invalid JSON', preview);
  }
}

class FetchError extends Error {
  constructor(status, statusText, detail = '') {
    super(`HTTP ${status}: ${statusText}`);
    this.status = status;
    this.detail = detail;
  }
}

/* ── View mode ───────────────────────────────────────────────── */
let curView = localStorage.getItem('catView') || 'grid';
document.addEventListener('DOMContentLoaded', () => applyView(curView));

function setView(mode) {
  curView = mode;
  localStorage.setItem('catView', mode);
  applyView(mode);
}

function applyView(mode) {
  const grid = document.getElementById('catGrid');
  const list = document.getElementById('catList');
  const bG   = document.getElementById('btnGrid');
  const bL   = document.getElementById('btnList');

  if (mode === 'grid') {
    grid.style.display = '';
    list.style.display = 'none';
    bG.classList.add('is-active');
    bL.classList.remove('is-active');
  } else {
    grid.style.display = 'none';
    list.style.display = '';
    bG.classList.remove('is-active');
    bL.classList.add('is-active');
  }
}

/* ── Filter client-side ──────────────────────────────────────── */
function filterCats() {
  const kw = document.getElementById('searchInput').value.toLowerCase().trim();
  const st = document.getElementById('filterStatus').value;

  document.querySelectorAll('#catGrid .cat-card:not(.cat-add-btn)').forEach(el => {
    const match = (!kw || el.dataset.name.toLowerCase().includes(kw))
               && (st === '' || el.dataset.status === st);
    el.style.display = match ? '' : 'none';
  });

  let cnt = 0;
  document.querySelectorAll('#catList tbody tr').forEach(row => {
    const match = (!kw || row.dataset.name.toLowerCase().includes(kw))
               && (st === '' || row.dataset.status === st);
    row.style.display = match ? '' : 'none';
    if (match) cnt++;
  });

  const lbl = document.getElementById('listCount');
  if (lbl) lbl.textContent = cnt + ' danh mục';
}

/* ── Modal ───────────────────────────────────────────────────── */
function openModal(c = null) {
  document.getElementById('cId').value              = c ? c.id   : '';
  document.getElementById('cName').value            = c ? c.name : '';
  document.getElementById('cDesc').value            = c ? (c.desc || '') : '';
  document.getElementById('cStatusToggle').checked  = c ? c.status == 1  : true;
  document.getElementById('cStatus').value          = c ? c.status : 1;
  document.getElementById('modalTitle').textContent = c ? 'Sửa danh mục' : 'Thêm danh mục';

  clearError();
  syncStatus();
  document.getElementById('catModal').classList.add('show');
  setTimeout(() => document.getElementById('cName').focus(), 120);
}

function closeModal() {
  document.getElementById('catModal').classList.remove('show');
}

function syncStatus() {
  const checked = document.getElementById('cStatusToggle').checked;
  document.getElementById('cStatus').value = checked ? 1 : 0;
  const lbl = document.getElementById('statusLabel');
  lbl.textContent = checked ? 'Hoạt động' : 'Ẩn';
  lbl.style.color = checked ? 'var(--green)' : 'var(--red)';
}

document.getElementById('catModal').addEventListener('click', e => {
  if (e.target === e.currentTarget) closeModal();
});
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') closeModal();
});

/* ── Validate ────────────────────────────────────────────────── */
function showError(msg) {
  const el = document.getElementById('nameError');
  el.textContent = msg;
  el.style.display = 'block';
  document.getElementById('cName').style.borderColor = 'var(--red)';
}

function clearError() {
  document.getElementById('nameError').style.display = 'none';
  document.getElementById('cName').style.borderColor = '';
}

/* ── SAVE (Add / Edit) ────────────────────────────────────────── */
async function saveCat() {
  clearError();

  const id     = document.getElementById('cId').value;
  const name   = document.getElementById('cName').value.trim();
  const desc   = document.getElementById('cDesc').value.trim();
  const status = parseInt(document.getElementById('cStatus').value);

  if (!name) { showError('Vui lòng nhập tên danh mục.'); return; }

  const btn = document.getElementById('btnSave');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang lưu...';

  try {
    const data = await safeFetch(API, {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({ action: id ? 'edit' : 'add', id, name, desc, status }),
    });

    if (data.success) {
      showToast(data.message, 'success');
      closeModal();
      setTimeout(() => location.reload(), 700);
    } else {
      showError(data.message || 'Thao tác thất bại.');
    }

  } catch (err) {
    console.error('[saveCat]', err);

    if (err instanceof FetchError && err.status >= 500) {
      // Lỗi server nghiêm trọng → mở error.php trong tab mới
      showToast('Lỗi server! Chuyển đến trang Error Log...', 'error', 4000);
      setTimeout(() => window.open(ERROR_PAGE, '_blank'), 1200);
    } else {
      showError('Lỗi: ' + err.message);
    }
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Lưu';
  }
}

/* ── TOGGLE status ────────────────────────────────────────────── */
async function toggleStatus(id, btn) {
  btn.disabled = true;
  const origHTML = btn.innerHTML;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

  try {
    const data = await safeFetch(API, {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({ action: 'toggle', id }),
    });

    if (data.success) {
      showToast(data.message, 'info');
      setTimeout(() => location.reload(), 600);
    } else {
      showToast(data.message || 'Không thể thay đổi trạng thái.', 'warning');
      btn.disabled = false;
      btn.innerHTML = origHTML;
    }

  } catch (err) {
    console.error('[toggleStatus]', err);
    showToast('Lỗi kết nối: ' + err.message, 'error');
    btn.disabled = false;
    btn.innerHTML = origHTML;

    if (err instanceof FetchError && err.status >= 500) {
      setTimeout(() => window.open(ERROR_PAGE, '_blank'), 1000);
    }
  }
}

/* ── DELETE ──────────────────────────────────────────────────── */
function confirmDelete(id, name) {
  showConfirm(
    `Xoá danh mục "<strong>${name}</strong>"?<br>
     <span style="font-size:12px;color:var(--text-muted)">Hành động này không thể hoàn tác.</span>`,
    async () => {
      try {
        const data = await safeFetch(API, {
          method:  'POST',
          headers: { 'Content-Type': 'application/json' },
          body:    JSON.stringify({ action: 'delete', id }),
        });

        if (data.success) {
          showToast(data.message, 'success');
          // Xóa các element DOM liên quan ngay, không cần reload
          document.querySelectorAll(`[data-id="${id}"]`).forEach(el => {
            el.style.transition = 'opacity .3s, transform .3s';
            el.style.opacity    = '0';
            el.style.transform  = 'scale(.92)';
            setTimeout(() => el.remove(), 320);
          });
        } else {
          showToast(data.message || 'Không thể xóa danh mục.', 'error');
        }

      } catch (err) {
        console.error('[confirmDelete]', err);
        showToast('Lỗi: ' + err.message, 'error');

        if (err instanceof FetchError && err.status >= 500) {
          setTimeout(() => window.open(ERROR_PAGE, '_blank'), 1000);
        }
      }
    },
    'Xoá'
  );
}

/* ── Fallback Toast & Confirm (nếu admin.js chưa tải) ────────── */
if (typeof showToast === 'undefined') {
  window.showToast = function (msg, type = 'info', duration = 3000) {
    const colors = { success:'#3fb950', error:'#f85149', warning:'#f0a500', info:'#58a6ff' };
    const el = document.createElement('div');
    el.textContent = msg;
    Object.assign(el.style, {
      position:'fixed', bottom:'24px', right:'24px', zIndex:'9999',
      background: colors[type] || colors.info, color:'#fff',
      padding:'10px 18px', borderRadius:'8px', fontSize:'13px', fontWeight:'600',
      boxShadow:'0 4px 16px rgba(0,0,0,.3)', transition:'opacity .35s',
    });
    document.body.appendChild(el);
    setTimeout(() => {
      el.style.opacity = '0';
      setTimeout(() => el.remove(), 380);
    }, duration);
  };
}

if (typeof showConfirm === 'undefined') {
  window.showConfirm = function (msg, onOk, label = 'OK') {
    if (confirm(msg.replace(/<[^>]+>/g, ''))) onOk();
  };
}
