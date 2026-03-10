<?php
// views/subviews/users/update.php
// Modal Sửa người dùng — được include vào users.php
// Mở bằng JS: openEditModal(id, hoten, username, email, sdt, address, phanloai)
?>

<div class="u-modal-backdrop" id="modalEdit" onclick="closeModal('modalEdit')">
  <div class="u-modal" onclick="event.stopPropagation()">

    <!-- Header -->
    <div class="u-modal__head">
      <span class="u-modal__title">✏️ SỬA NGƯỜI DÙNG</span>
      <button class="u-modal__close" onclick="closeModal('modalEdit')">✕</button>
    </div>

    <!-- Alert lỗi -->
    <?php if (($flash['form'] ?? '') === 'edit'): ?>
      <div class="u-modal__alert u-modal__alert--<?= $flash['type'] ?>">
        <?= htmlspecialchars($flash['msg']) ?>
      </div>
    <?php endif; ?>

    <!-- id và action được JS điền vào -->
    <form method="POST" action="" id="formEdit">
      <div class="u-modal__grid">

        <div class="form-group">
          <label class="form-label">Họ tên <span class="u-req">*</span></label>
          <input class="form-input" type="text" name="hoten" id="editHoten"
                 placeholder="Nguyễn Văn A">
        </div>

        <div class="form-group">
          <label class="form-label">Username
            <small style="font-size:10px;opacity:.5">(không thể đổi)</small>
          </label>
          <input class="form-input" type="text" id="editUsername" disabled
                 style="opacity:.5;cursor:not-allowed">
        </div>

        <div class="form-group">
          <label class="form-label">Email <span class="u-req">*</span></label>
          <input class="form-input" type="email" name="email" id="editEmail"
                 placeholder="example@email.com">
        </div>

        <div class="form-group">
          <label class="form-label">Mật khẩu mới
            <small style="font-size:10px;opacity:.6">(bỏ trống = giữ nguyên)</small>
          </label>
          <input class="form-input" type="password" name="password" placeholder="••••••••">
        </div>

        <div class="form-group">
          <label class="form-label">Số điện thoại <span class="u-req">*</span></label>
          <input class="form-input" type="text" name="sdt" id="editSdt"
                 maxlength="10" placeholder="09xxxxxxxx">
        </div>

        <div class="form-group">
          <label class="form-label">Loại tài khoản <span class="u-req">*</span></label>
          <select class="form-input" name="phanloai" id="editPhanloai">
            <?php foreach ($userTypes as $ut): ?>
              <option value="<?= $ut['idType'] ?>">
                <?= htmlspecialchars($ut['Ten']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group u-full-col">
          <label class="form-label">Địa chỉ <span class="u-req">*</span></label>
          <input class="form-input" type="text" name="address" id="editAddress"
                 placeholder="123 Đường ABC, Quận XYZ, TP.HCM">
        </div>

      </div><!-- /.u-modal__grid -->

      <div class="u-modal__foot">
        <button type="button" class="btn-outline" onclick="closeModal('modalEdit')">Huỷ</button>
        <button type="submit" class="btn-sm">💾 Cập nhật</button>
      </div>
    </form>

  </div><!-- /.u-modal -->
</div><!-- /.u-modal-backdrop -->
