<?php
// views/subviews/promos/update.php
// Modal Sửa mã khuyến mãi — include vào promo.php
// Mở bằng JS: openEditPromo(id, giatri, soluong, ngayapdung, hansudung, trangthai)
?>

<div class="u-modal-backdrop" id="modalEditPromo" onclick="closeModal('modalEditPromo')">
  <div class="u-modal" onclick="event.stopPropagation()">

    <div class="u-modal__head">
      <span class="u-modal__title">✏️ SỬA MÃ KHUYẾN MÃI</span>
      <button class="u-modal__close" onclick="closeModal('modalEditPromo')">✕</button>
    </div>

    <?php if (($flash['form'] ?? '') === 'edit'): ?>
      <div class="u-modal__alert u-modal__alert--<?= $flash['type'] ?>">
        <?= htmlspecialchars($flash['msg']) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="" id="formEditPromo">
      <div class="u-modal__grid">

        <div class="form-group">
          <label class="form-label">Mã CODE <small style="opacity:.5">(không thể đổi)</small></label>
          <input class="form-input" type="text" id="editPromoCode" disabled
                 style="opacity:.5;cursor:not-allowed;font-family:'JetBrains Mono',monospace;
                        letter-spacing:2px;font-weight:700">
        </div>

        <div class="form-group">
          <label class="form-label">Giá trị giảm <span class="u-req">*</span>
            <small style="font-size:10px;opacity:.6">(0.01–1.00)</small>
          </label>
          <input class="form-input" type="number" name="giatri" id="editPromoGiatri"
                 step="0.01" min="0.01" max="1" placeholder="0.10">
        </div>

        <div class="form-group">
          <label class="form-label">Số lượt dùng <span class="u-req">*</span></label>
          <input class="form-input" type="number" name="soluong" id="editPromoSoluong"
                 min="1" placeholder="100">
        </div>

        <div class="form-group">
          <label class="form-label">Trạng thái</label>
          <label style="display:flex;align-items:center;gap:8px;margin-top:10px;cursor:pointer">
            <input type="checkbox" name="trangthai" id="editPromoTrangthai"
                   style="width:16px;height:16px;accent-color:var(--accent)">
            <span style="font-size:13px;color:var(--text2)">Tạm dừng</span>
          </label>
        </div>

        <div class="form-group">
          <label class="form-label">Ngày áp dụng <span class="u-req">*</span></label>
          <input class="form-input" type="date" name="ngayapdung" id="editPromoNgay">
        </div>

        <div class="form-group">
          <label class="form-label">Hạn sử dụng <span class="u-req">*</span></label>
          <input class="form-input" type="date" name="hansudung" id="editPromoHan">
        </div>

      </div><!-- /.u-modal__grid -->

      <div class="u-modal__foot">
        <button type="button" class="btn-outline" onclick="closeModal('modalEditPromo')">Huỷ</button>
        <button type="submit" class="btn-sm">💾 Cập nhật</button>
      </div>
    </form>

  </div>
</div>