<?php
// views/subviews/categories/remove.php
// Modal xác nhận Xoá danh mục — include vào category.php
// Mở bằng JS: openDeleteCat(id, loaisp)
?>

<div class="u-modal-backdrop" id="modalDeleteCat" onclick="closeModal('modalDeleteCat')">
  <div class="u-modal u-modal--sm" onclick="event.stopPropagation()">

    <div class="u-modal__head">
      <span class="u-modal__title" style="color:var(--red)">🗑️ XOÁ DANH MỤC</span>
      <button class="u-modal__close" onclick="closeModal('modalDeleteCat')">✕</button>
    </div>

    <div style="text-align:center;padding:20px 0 12px">
      <div style="font-size:48px;line-height:1;margin-bottom:12px">⚠️</div>
      <p style="font-size:14px;color:var(--text);margin:0 0 8px">
        Xoá vĩnh viễn danh mục:
      </p>
      <p id="deleteCatName"
         style="font-family:'Bebas Neue',sans-serif;font-size:22px;
                letter-spacing:2px;color:var(--accent);margin:0 0 12px">
        —
      </p>
      <p style="font-size:12px;color:var(--text2);line-height:1.6;margin:0">
        Chỉ xoá được nếu danh mục <strong>không có sản phẩm nào</strong>.
      </p>
    </div>

    <form method="GET" id="formDeleteCat" action="">
      <div class="u-modal__foot" style="justify-content:center;gap:16px">
        <button type="button" class="btn-outline" onclick="closeModal('modalDeleteCat')">
          Không, giữ lại
        </button>
        <button type="submit"
                style="background:var(--red);color:#fff;border:none;border-radius:7px;
                       padding:8px 20px;font-size:12px;font-weight:600;
                       font-family:'DM Sans',sans-serif;cursor:pointer;transition:opacity .2s"
                onmouseover="this.style.opacity='.8'"
                onmouseout="this.style.opacity='1'">
          🗑️ Xoá
        </button>
      </div>
    </form>

  </div>
</div>