<?php
// views/subviews/users/add.php
// Modal Thêm người dùng — được include vào users.php
// Mở bằng JS: openModal('modalAdd')
?>

<div class="u-modal-backdrop" id="modalAdd" onclick="closeModal('modalAdd')">
  <div class="u-modal" onclick="event.stopPropagation()">

    <!-- Header -->
    <div class="u-modal__head">
      <span class="u-modal__title">+ THÊM NGƯỜI DÙNG</span>
      <button class="u-modal__close" onclick="closeModal('modalAdd')">✕</button>
    </div>

    <!-- Alert lỗi (chỉ hiện khi submit bị lỗi) -->
    <?php if (($flash['form'] ?? '') === 'add'): ?>
      <div class="u-modal__alert u-modal__alert--<?= $flash['type'] ?>">
        <?= htmlspecialchars($flash['msg']) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="?action=add">
      <div class="u-modal__grid">

        <div class="form-group">
          <label class="form-label">Họ tên <span class="u-req">*</span></label>
          <input class="form-input" type="text" name="hoten"
                 value="<?= htmlspecialchars($_POST['hoten'] ?? '') ?>"
                 placeholder="Nguyễn Văn A">
        </div>

        <div class="form-group">
          <label class="form-label">Username <span class="u-req">*</span></label>
          <input class="form-input" type="text" name="username"
                 value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                 placeholder="nguyenvana">
        </div>

        <div class="form-group">
          <label class="form-label">Email <span class="u-req">*</span></label>
          <input class="form-input" type="email" name="email"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                 placeholder="example@email.com">
        </div>

        <div class="form-group">
          <label class="form-label">Mật khẩu <span class="u-req">*</span>
            <small style="font-size:10px;opacity:.6">(≥ 6 ký tự)</small>
          </label>
          <input class="form-input" type="password" name="password" placeholder="••••••••">
        </div>

        <div class="form-group">
          <label class="form-label">Số điện thoại <span class="u-req">*</span></label>
          <input class="form-input" type="text" name="sdt" maxlength="10"
                 value="<?= htmlspecialchars($_POST['sdt'] ?? '') ?>"
                 placeholder="09xxxxxxxx">
        </div>

        <div class="form-group">
          <label class="form-label">Loại tài khoản <span class="u-req">*</span></label>
          <select class="form-input" name="phanloai">
            <?php foreach ($userTypes as $ut): ?>
              <option value="<?= $ut['idType'] ?>"
                <?= (($_POST['phanloai'] ?? 1) == $ut['idType']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($ut['Ten']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group u-full-col">
          <label class="form-label">Địa chỉ <span class="u-req">*</span></label>
          <input class="form-input" type="text" name="address"
                 value="<?= htmlspecialchars($_POST['address'] ?? '') ?>"
                 placeholder="123 Đường ABC, Quận XYZ, TP.HCM">
        </div>

      </div><!-- /.u-modal__grid -->

      <div class="u-modal__foot">
        <button type="button" class="btn-outline" onclick="closeModal('modalAdd')">Huỷ</button>
        <button type="submit" class="btn-sm">💾 Lưu người dùng</button>
      </div>
    </form>

  </div><!-- /.u-modal -->
</div><!-- /.u-modal-backdrop -->
