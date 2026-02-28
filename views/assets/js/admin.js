// ========== ADMIN PANEL JS ==========

// Highlight active nav item based on current page
document.addEventListener('DOMContentLoaded', function () {
  const currentPage = window.location.pathname.split('/').pop();
  document.querySelectorAll('.nav-item').forEach(function (item) {
    const href = item.getAttribute('href');
    if (href && href.includes(currentPage)) {
      item.classList.add('active');
    }
  });
});

// Generic confirm delete
function confirmDelete(message) {
  return confirm(message || 'Bạn có chắc muốn xoá?');
}

// Toast notification (simple)
function showToast(msg, type) {
  type = type || 'success';
  var toast = document.createElement('div');
  toast.textContent = msg;
  toast.style.cssText = [
    'position:fixed', 'bottom:24px', 'right:24px', 'z-index:9999',
    'background:' + (type === 'success' ? '#22c55e' : '#ef4444'),
    'color:#fff', 'padding:10px 20px', 'border-radius:8px',
    'font-family:"JetBrains Mono",monospace', 'font-size:13px',
    'animation:fadeUp .3s ease', 'box-shadow:0 4px 20px rgba(0,0,0,.4)'
  ].join(';');
  document.body.appendChild(toast);
  setTimeout(function () { toast.remove(); }, 3000);
}

// Format number to VND
function formatVND(n) {
  return Number(n).toLocaleString('vi-VN') + 'đ';
}
