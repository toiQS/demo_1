// views/assets/js/user.js
// Điều khiển modal người dùng — không dùng AJAX

/* ── Helpers mở / đóng ─────────────────────────────────── */

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

// Đóng khi nhấn Escape
document.addEventListener('keydown', function (e) {
  if (e.key !== 'Escape') return;
  document.querySelectorAll('.u-modal-backdrop.u-modal--open').forEach(function (m) {
    m.classList.remove('u-modal--open');
  });
  document.body.style.overflow = '';
});

/* ── Modal Thêm ────────────────────────────────────────── */

function openAddModal() {
  openModal('modalAdd');
}

/* ── Modal Sửa ─────────────────────────────────────────── */

/**
 * @param {number} id
 * @param {string} hoten
 * @param {string} username
 * @param {string} email
 * @param {string} sdt
 * @param {string} address
 * @param {number} phanloai
 */
function openEditModal(id, hoten, username, email, sdt, address, phanloai) {
  document.getElementById('editHoten').value    = hoten;
  document.getElementById('editUsername').value = username;
  document.getElementById('editEmail').value    = email;
  document.getElementById('editSdt').value      = sdt;
  document.getElementById('editAddress').value  = address;

  // Chọn đúng option loại tài khoản
  var sel = document.getElementById('editPhanloai');
  for (var i = 0; i < sel.options.length; i++) {
    sel.options[i].selected = (parseInt(sel.options[i].value) === parseInt(phanloai));
  }

  // Gán action form
  document.getElementById('formEdit').action = '?action=edit&id=' + id;

  openModal('modalEdit');
}

/* ── Modal Xoá ─────────────────────────────────────────── */

/**
 * @param {number} id
 * @param {string} username
 */
function openDeleteModal(id, username) {
  document.getElementById('deleteUsername').textContent = username;
  document.getElementById('formDelete').action = '?action=delete&id=' + id;
  openModal('modalDelete');
}

/* ── Tự mở lại modal nếu PHP báo lỗi form ─────────────── */
document.addEventListener('DOMContentLoaded', function () {
  var errForm = document.body.dataset.errorForm;
  if (errForm === 'add')  openModal('modalAdd');
  if (errForm === 'edit') openModal('modalEdit');
});
