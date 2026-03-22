<?php
// views/subviews/categories/update.php
// Modal Sửa danh mục — include vào category.php
// Mở bằng JS: openEditCat(id, loaisp)
?>

<div class="u-modal-backdrop" id="modalEditCat" onclick="closeModal('modalEditCat')">
  <div class="u-modal u-modal--sm" onclick="event.stopPropagation()">

    <div class="u-modal__head">
      <span class="u-modal__title">✏️ SỬA DANH MỤC</span>
      <button class="u-modal__close" onclick="closeModal('modalEditCat')">✕</button>
    </div>

    <?php if (($flash['form'] ?? '') === 'edit'): ?>
      <div class="u-modal__alert u-modal__alert--<?= $flash['type'] ?>">
        <?= htmlspecialchars($flash['msg']) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="" id="formEditCat">
      <div class="form-group">
        <label class="form-label">Tên danh mục <span class="u-req">*</span>
          <small style="font-size:10px;opacity:.6">(tối đa 20 ký tự)</small>
        </label>
        <input class="form-input" type="text" name="loaisp" id="editCatName"
               maxlength="20" placeholder="VD: Điện thoại">
      </div>

      <div class="u-modal__foot">
        <button type="button" class="btn-outline" onclick="closeModal('modalEditCat')">Huỷ</button>
        <button type="submit" class="btn-sm">💾 Cập nhật</button>
      </div>
    </form>

  </div>
</div>