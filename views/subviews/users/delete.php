<?php
// views/subviews/users/delete.php
// Modal xác nhận Xoá người dùng — include vào users.php
// Mở bằng JS: openDeleteModal(id, username)
?>

<div class="u-modal-backdrop" id="modalDelete" onclick="closeModal('modalDelete')">
  <div class="u-modal u-modal--sm" onclick="event.stopPropagation()">

    <!-- Header -->
    <div class="u-modal__head">
      <span class="u-modal__title" style="color:var(--red)">🗑️ XOÁ TÀI KHOẢN</span>
      <button class="u-modal__close" onclick="closeModal('modalDelete')">✕</button>
    </div>

    <!-- Nội dung cảnh báo -->
    <div style="text-align:center;padding:24px 0 16px">
      <div style="font-size:52px;line-height:1;margin-bottom:16px">⚠️</div>

      <p style="font-size:14px;color:var(--text);margin:0 0 8px">
        Bạn chắc chắn muốn <strong style="color:var(--red)">xoá vĩnh viễn</strong> tài khoản:
      </p>

      <p id="deleteUsername"
         style="font-family:'JetBrains Mono',monospace;font-size:18px;
                font-weight:700;color:var(--accent);margin:0 0 16px">
        —
      </p>

      <p style="font-size:12px;color:var(--text2);line-height:1.6;margin:0">
        Hành động này <strong>không thể hoàn tác</strong>.<br>
        Nếu tài khoản có hóa đơn, hãy dùng <em>Khoá</em> thay vì Xoá.
      </p>
    </div>

    <!-- Form GET để submit xoá -->
    <form method="GET" id="formDelete" action="">
      <div class="u-modal__foot" style="justify-content:center;gap:16px">
        <button type="button" class="btn-outline" onclick="closeModal('modalDelete')">
          Không, giữ lại
        </button>
        <button type="submit"
                style="background:var(--red);color:#fff;border:none;border-radius:7px;
                       padding:8px 20px;font-size:12px;font-weight:600;
                       font-family:'DM Sans',sans-serif;cursor:pointer;
                       transition:opacity .2s;"
                onmouseover="this.style.opacity='.8'"
                onmouseout="this.style.opacity='1'">
          🗑️ Xoá tài khoản
        </button>
      </div>
    </form>

  </div><!-- /.u-modal -->
</div><!-- /.u-modal-backdrop -->
