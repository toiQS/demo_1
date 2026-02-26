/**
 * admin.js — Shared JavaScript for Admin Panel
 * Website Bán Hàng | Version 1.0
 */

/* ========================= LIVE CLOCK ========================= */
function initClock() {
  const el = document.getElementById('clock');
  if (!el) return;

  function tick() {
    const now = new Date();
    const pad = n => String(n).padStart(2, '0');
    const time = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
    const date = `${pad(now.getDate())}/${pad(now.getMonth() + 1)}/${now.getFullYear()}`;
    el.textContent = `${time} | ${date}`;
  }

  tick();
  setInterval(tick, 1000);
}

/* ========================= SIDEBAR TOGGLE (mobile) ========================= */
function initSidebarToggle() {
  const toggle = document.getElementById('sidebar-toggle');
  const sidebar = document.querySelector('.sidebar');
  const overlay = document.getElementById('sidebar-overlay');
  if (!toggle || !sidebar) return;

  toggle.addEventListener('click', () => {
    sidebar.classList.toggle('open');
    if (overlay) overlay.classList.toggle('active');
  });

  if (overlay) {
    overlay.addEventListener('click', () => {
      sidebar.classList.remove('open');
      overlay.classList.remove('active');
    });
  }
}

/* ========================= ACTIVE NAV ITEM ========================= */
function initActiveNav() {
  const currentPath = window.location.pathname.split('/').pop();
  document.querySelectorAll('.nav-item').forEach(item => {
    const href = item.getAttribute('href');
    if (href && href === currentPath) {
      item.classList.add('active');
    } else {
      item.classList.remove('active');
    }
  });
}

/* ========================= TOAST NOTIFICATION ========================= */
/**
 * Hiển thị toast thông báo
 * @param {string} message  - Nội dung thông báo
 * @param {'success'|'error'|'warning'|'info'} type - Loại thông báo
 * @param {number} duration - Thời gian hiển thị (ms), mặc định 3000
 */
function showToast(message, type = 'info', duration = 3000) {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    container.style.cssText = `
      position: fixed; bottom: 24px; right: 24px;
      display: flex; flex-direction: column; gap: 10px;
      z-index: 9999; pointer-events: none;
    `;
    document.body.appendChild(container);
  }

  const icons = {
    success: 'fa-circle-check',
    error:   'fa-circle-xmark',
    warning: 'fa-triangle-exclamation',
    info:    'fa-circle-info',
  };
  const colors = {
    success: 'var(--green)',
    error:   'var(--red)',
    warning: 'var(--orange)',
    info:    'var(--blue)',
  };

  const toast = document.createElement('div');
  toast.style.cssText = `
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-left: 3px solid ${colors[type]};
    border-radius: var(--radius-sm);
    padding: 12px 16px;
    display: flex; align-items: center; gap: 10px;
    font-family: var(--font); font-size: 13px; font-weight: 500;
    color: var(--text-primary);
    box-shadow: var(--shadow);
    pointer-events: auto;
    animation: slideIn .3s ease;
    min-width: 280px; max-width: 380px;
  `;
  toast.innerHTML = `
    <i class="fa-solid ${icons[type]}" style="color:${colors[type]};font-size:16px;flex-shrink:0"></i>
    <span style="flex:1">${message}</span>
    <button onclick="this.parentElement.remove()" style="
      background:none;border:none;color:var(--text-muted);
      font-size:14px;cursor:pointer;padding:0;line-height:1;
    "><i class="fa-solid fa-xmark"></i></button>
  `;

  container.appendChild(toast);
  setTimeout(() => {
    toast.style.animation = 'slideOut .3s ease forwards';
    setTimeout(() => toast.remove(), 300);
  }, duration);
}

/* ========================= CONFIRM DIALOG ========================= */
/**
 * Hộp thoại xác nhận hành động nguy hiểm (thay thế window.confirm)
 * @param {string} message   - Nội dung câu hỏi
 * @param {Function} onConfirm - Callback khi xác nhận
 * @param {string} confirmText - Nhãn nút xác nhận
 */
function showConfirm(message, onConfirm, confirmText = 'Xác nhận') {
  const overlay = document.createElement('div');
  overlay.style.cssText = `
    position: fixed; inset: 0; background: rgba(0,0,0,.6);
    display: flex; align-items: center; justify-content: center;
    z-index: 9998; animation: fadeIn .2s ease;
  `;

  overlay.innerHTML = `
    <div style="
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 28px;
      max-width: 380px; width: 90%;
      box-shadow: var(--shadow);
      animation: scaleIn .2s ease;
    ">
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
        <div style="
          width:40px;height:40px;border-radius:10px;
          background:rgba(248,81,73,.15);
          display:flex;align-items:center;justify-content:center;
          font-size:18px;color:var(--red);flex-shrink:0;
        "><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div>
          <div style="font-size:14px;font-weight:700;margin-bottom:2px">Xác nhận hành động</div>
          <div style="font-size:12px;color:var(--text-muted)">${message}</div>
        </div>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end">
        <button id="confirm-cancel" class="btn btn-secondary">Huỷ</button>
        <button id="confirm-ok" class="btn btn-danger">${confirmText}</button>
      </div>
    </div>
  `;

  document.body.appendChild(overlay);

  overlay.querySelector('#confirm-cancel').onclick = () => overlay.remove();
  overlay.querySelector('#confirm-ok').onclick = () => {
    overlay.remove();
    onConfirm();
  };
}

/* ========================= TABLE SEARCH (client-side) ========================= */
/**
 * Lọc bảng theo từ khoá nhập vào ô search
 * @param {string} inputId - id của ô input
 * @param {string} tableId - id của bảng
 */
function initTableSearch(inputId, tableId) {
  const input = document.getElementById(inputId);
  const table = document.getElementById(tableId);
  if (!input || !table) return;

  input.addEventListener('input', () => {
    const kw = input.value.toLowerCase().trim();
    table.querySelectorAll('tbody tr').forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(kw) ? '' : 'none';
    });
  });
}

/* ========================= CSS KEYFRAMES (inject once) ========================= */
(function injectKeyframes() {
  if (document.getElementById('admin-js-styles')) return;
  const style = document.createElement('style');
  style.id = 'admin-js-styles';
  style.textContent = `
    @keyframes slideIn  { from { opacity:0; transform:translateX(20px); } to { opacity:1; transform:translateX(0); } }
    @keyframes slideOut { from { opacity:1; transform:translateX(0); } to { opacity:0; transform:translateX(20px); } }
    @keyframes fadeIn   { from { opacity:0; } to { opacity:1; } }
    @keyframes scaleIn  { from { opacity:0; transform:scale(.95); } to { opacity:1; transform:scale(1); } }
  `;
  document.head.appendChild(style);
})();

/* ========================= INIT ON DOM READY ========================= */
document.addEventListener('DOMContentLoaded', () => {
  initClock();
  initSidebarToggle();
  initActiveNav();
});


// get count users

function count_users(){
  var xmlHttp = new XMLHttpRequest();
  xmlHttp.onreadystatechange = function(){
    xmlHttp.open("get","get_count_users.php",true);
    xmlHttp.send();
  }
}