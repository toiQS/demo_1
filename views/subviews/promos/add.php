<?php
// views/subviews/promos/add.php
// Modal Thêm mã khuyến mãi — include vào promo.php
// Mở bằng JS: openModal('modalAddPromo')
?>

<div class="u-modal-backdrop" id="modalAddPromo" onclick="closeModal('modalAddPromo')">
  <div class="u-modal" onclick="event.stopPropagation()">

    <div class="u-modal__head">
      <span class="u-modal__title">+ TẠO MÃ KHUYẾN MÃI</span>
      <button class="u-modal__close" onclick="closeModal('modalAddPromo')">✕</button>
    </div>

    <?php if (($flash['form'] ?? '') === 'add'): ?>
      <div class="u-modal__alert u-modal__alert--<?= $flash['type'] ?>">
        <?= htmlspecialchars($flash['msg']) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="?action=add">
      <div class="u-modal__grid">

        <div class="form-group">
          <label class="form-label">Mã CODE <span class="u-req">*</span>
            <small style="font-size:10px;opacity:.6">(chữ hoa + số, 3–10 ký tự)</small>
          </label>
          <input class="form-input" type="text" name="code"
                 value="<?= htmlspecialchars($_POST['code'] ?? '') ?>"
                 placeholder="SALE10" maxlength="10"
                 style="text-transform:uppercase"
                 oninput="this.value=this.value.toUpperCase()">
        </div>

        <div class="form-group">
          <label class="form-label">Giá trị giảm <span class="u-req">*</span>
            <small style="font-size:10px;opacity:.6">(0.01 – 1.00 = 1%–100%)</small>
          </label>
          <input class="form-input" type="number" name="giatri" step="0.01" min="0.01" max="1"
                 value="<?= htmlspecialchars($_POST['giatri'] ?? '') ?>"
                 placeholder="0.10">
        </div>

        <div class="form-group">
          <label class="form-label">Số lượt dùng <span class="u-req">*</span></label>
          <input class="form-input" type="number" name="soluong" min="1"
                 value="<?= htmlspecialchars($_POST['soluong'] ?? '') ?>"
                 placeholder="100">
        </div>

        <div class="form-group">
          <label class="form-label">Trạng thái ban đầu</label>
          <label style="display:flex;align-items:center;gap:8px;margin-top:10px;cursor:pointer">
            <input type="checkbox" name="trangthai"
                   <?= isset($_POST['trangthai']) ? 'checked' : '' ?>
                   style="width:16px;height:16px;accent-color:var(--accent)">
            <span style="font-size:13px;color:var(--text2)">Tạm dừng ngay</span>
          </label>
        </div>

        <div class="form-group">
          <label class="form-label">Ngày áp dụng <span class="u-req">*</span></label>
          <input class="form-input" type="date" name="ngayapdung"
                 value="<?= htmlspecialchars($_POST['ngayapdung'] ?? date('Y-m-d')) ?>">
        </div>

        <div class="form-group">
          <label class="form-label">Hạn sử dụng <span class="u-req">*</span></label>
          <input class="form-input" type="date" name="hansudung"
                 value="<?= htmlspecialchars($_POST['hansudung'] ?? '') ?>">
        </div>

      </div><!-- /.u-modal__grid -->

      <div class="u-modal__foot">
        <button type="button" class="btn-outline" onclick="closeModal('modalAddPromo')">Huỷ</button>
        <button type="submit" class="btn-sm">💾 Tạo mã</button>
      </div>
    </form>

  </div>
</div>