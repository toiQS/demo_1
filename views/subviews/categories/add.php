<?php
// views/subviews/categories/add.php
// Modal Thêm danh mục — include vào category.php
// Mở bằng JS: openModal('modalAddCat')
?>

<div class="u-modal-backdrop" id="modalAddCat" onclick="closeModal('modalAddCat')">
  <div class="u-modal u-modal--sm" onclick="event.stopPropagation()">

    <div class="u-modal__head">
      <span class="u-modal__title">+ THÊM DANH MỤC</span>
      <button class="u-modal__close" onclick="closeModal('modalAddCat')">✕</button>
    </div>

    <?php if (($flash['form'] ?? '') === 'add'): ?>
      <div class="u-modal__alert u-modal__alert--<?= $flash['type'] ?>">
        <?= htmlspecialchars($flash['msg']) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="?action=add">
      <div class="form-group">
        <label class="form-label">Tên danh mục <span class="u-req">*</span>
          <small style="font-size:10px;opacity:.6">(tối đa 20 ký tự)</small>
        </label>
        <input class="form-input" type="text" name="loaisp" maxlength="20"
               value="<?= htmlspecialchars($_POST['loaisp'] ?? '') ?>"
               placeholder="VD: Điện thoại" autofocus>
      </div>

      <div class="u-modal__foot">
        <button type="button" class="btn-outline" onclick="closeModal('modalAddCat')">Huỷ</button>
        <button type="submit" class="btn-sm">💾 Lưu</button>
      </div>
    </form>

  </div>
</div>