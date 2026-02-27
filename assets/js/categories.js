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