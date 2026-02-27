const API = 'categories_action.php';

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
  const kw  = document.getElementById('searchInput').value.toLowerCase().trim();
  const st  = document.getElementById('filterStatus').value; // '' | '0' | '1'

  // Grid
  document.querySelectorAll('#catGrid .cat-card:not(.cat-add-btn)').forEach(el => {
    const match = (!kw || el.dataset.name.toLowerCase().includes(kw))
               && (st === '' || el.dataset.status === st);
    el.style.display = match ? '' : 'none';
  });

  // List
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
// c = {id, name, desc, status} khi sửa | null khi thêm
function openModal(c = null) {
  document.getElementById('cId').value              = c ? c.id     : '';
  document.getElementById('cName').value            = c ? c.name   : '';
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

// Đóng khi click nền tối | ESC
document.getElementById('catModal').addEventListener('click', e => { if (e.target === e.currentTarget) closeModal(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

/* ── Validate ────────────────────────────────────────────────── */
function showError(msg) {
  const el = document.getElementById('nameError');
  el.textContent = msg; el.style.display = 'block';
  document.getElementById('cName').style.borderColor = 'var(--red)';
}
function clearError() {
  document.getElementById('nameError').style.display = 'none';
  document.getElementById('cName').style.borderColor = '';
}

/* ── SAVE (thêm / sửa) ───────────────────────────────────────── */
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
    const res  = await fetch(API, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: id ? 'edit' : 'add', id, name, desc, status }),
    });
    const data = await res.json();

    if (data.success) {
      showToast(data.message, 'success');
      closeModal();
      setTimeout(() => location.reload(), 700);
    } else {
      showError(data.message);
    }
  } catch (error) {
    showError('Lỗi kết nối server. Vui lòng thử lại.'+ error );
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Lưu';
  }
}

/* ── TOGGLE trạng thái ───────────────────────────────────────── */
async function toggleStatus(id, btn) {
  btn.disabled = true;
  try {
    const res  = await fetch(API, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'toggle', id }),
    });
    const data = await res.json();

    if (data.success) {
      showToast(data.message, 'info');
      setTimeout(() => location.reload(), 600);
    } else {
      showToast(data.message, 'error');
      btn.disabled = false;
    }
  } catch {
    showToast('Lỗi kết nối.', 'error');
    btn.disabled = false;
  }
}

/* ── DELETE ──────────────────────────────────────────────────── */
function confirmDelete(id, name) {
  showConfirm(
    `Xoá danh mục "<strong>${name}</strong>"?<br>
     <span style="font-size:12px;color:var(--text-muted)">Hành động này không thể hoàn tác.</span>`,
    async () => {
      try {
        const res  = await fetch(API, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'delete', id }),
        });
        const data = await res.json();

        if (data.success) {
          showToast(data.message, 'success');
          // Xoá phần tử DOM ngay, không cần reload
          document.querySelectorAll(`[data-id="${id}"]`).forEach(el => {
            el.style.transition = 'opacity .3s, transform .3s';
            el.style.opacity    = '0';
            el.style.transform  = 'scale(.92)';
            setTimeout(() => el.remove(), 320);
          });
        } else {
          showToast(data.message, 'error');
        }
      } catch {
        showToast('Lỗi kết nối.', 'error');
      }
    },
    'Xoá'
  );
}

/* ── Fallback Toast & Confirm (nếu layout chưa có) ──────────── */
if (typeof showToast === 'undefined') {
  window.showToast = function (msg, type = 'info') {
    const color = { success: '#3fb950', error: '#f85149', warning: '#f0a500', info: '#58a6ff' };
    const el = Object.assign(document.createElement('div'), {
      textContent: msg,
    });
    Object.assign(el.style, {
      position: 'fixed', bottom: '24px', right: '24px', zIndex: '9999',
      background: color[type] || color.info, color: '#fff',
      padding: '10px 18px', borderRadius: '8px', fontSize: '13px', fontWeight: '600',
      boxShadow: '0 4px 16px rgba(0,0,0,.3)', transition: 'opacity .35s',
    });
    document.body.appendChild(el);
    setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 380); }, 2600);
  };
}

if (typeof showConfirm === 'undefined') {
  window.showConfirm = function (msg, onOk, label = 'OK') {
    if (confirm(msg.replace(/<[^>]+>/g, ''))) onOk();
  };
}
