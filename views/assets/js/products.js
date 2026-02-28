/**
 * products.js — Quản lý Sản phẩm
 */

/* ---- Filter ---- */
function applyFilters() {
  const kw  = document.getElementById('searchInput').value.toLowerCase();
  const cat = document.getElementById('filterCat').value;
  const st  = document.getElementById('filterStatus').value;
  document.querySelectorAll('#productTable tbody tr').forEach(r => {
    const ok = r.textContent.toLowerCase().includes(kw)
            && (!cat || r.dataset.cat    === cat)
            && (!st  || r.dataset.status === st);
    r.style.display = ok ? '' : 'none';
  });
}

['searchInput', 'filterCat', 'filterStatus'].forEach(id => {
  const el = document.getElementById(id);
  if (el) el.addEventListener(id === 'searchInput' ? 'input' : 'change', applyFilters);
});

/* ---- Modal ---- */
function openModal(p = null) {
  document.getElementById('pId').value     = p ? p.id       : '';
  document.getElementById('pName').value   = p ? p.name     : '';
  document.getElementById('pCat').value    = p ? p.category : '';
  document.getElementById('pStatus').value = p ? p.status   : 1;
  document.getElementById('pCost').value   = p ? p.cost     : '';
  document.getElementById('pProfit').value = p ? p.profit   : '';
  document.getElementById('pPrice').value  = p ? p.price    : '';
  document.getElementById('pStock').value  = p ? p.stock    : '';
  document.getElementById('pDesc').value   = '';
  document.getElementById('modalTitle').textContent = p ? 'Sửa sản phẩm' : 'Thêm sản phẩm';
  document.getElementById('productModal').classList.add('show');
}

function closeModal() {
  document.getElementById('productModal').classList.remove('show');
}

function calcPrice() {
  const cost   = parseFloat(document.getElementById('pCost').value)   || 0;
  const profit = parseFloat(document.getElementById('pProfit').value) || 0;
  document.getElementById('pPrice').value = Math.round(cost * (1 + profit / 100));
}

/* ---- Save / Delete ---- */
function saveProduct() {
  if (!document.getElementById('pName').value.trim()) {
    showToast('Vui lòng nhập tên sản phẩm', 'warning');
    return;
  }
  showToast('Lưu sản phẩm thành công!', 'success');
  closeModal();
}

function confirmDelete(id, name) {
  showConfirm(`Xoá sản phẩm "${name}"?`, () => showToast('Đã xoá sản phẩm', 'success'), 'Xoá');
}

/* ---- Close on overlay click ---- */
document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('productModal')
    .addEventListener('click', e => { if (e.target === e.currentTarget) closeModal(); });

  document.getElementById('pCost')
    .addEventListener('input', calcPrice);
});
