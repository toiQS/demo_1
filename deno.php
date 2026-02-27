<div class="modal-overlay" id="catModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="modalTitle">Thêm danh mục</div>
      <button class="btn-close-modal" onclick="closeModal()">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <div class="modal-body">
      <!-- ID ẩn -->
      <input type="hidden" id="cId">

      <!-- Tên danh mục -->
      <div class="form-group">
        <label class="form-label">
          Tên danh mục <span style="color:var(--red)">*</span>
        </label>
        <input type="text" class="form-control" id="cName"
          placeholder="VD: Điện thoại, Laptop..." maxlength="255"
          onkeydown="if(event.key==='Enter') saveCat()">
        <!-- JS dùng element này để hiện lỗi -->
        <div id="nameError"
          style="display:none;color:#f85149;font-size:12px;margin-top:5px;font-weight:600">
        </div>
      </div>

      <!-- Mô tả -->
      <div class="form-group">
        <label class="form-label">Mô tả</label>
        <textarea class="form-control" id="cDesc" rows="3"
          placeholder="Mô tả ngắn về danh mục..."
          style="resize:vertical"></textarea>
      </div>

      <!-- Trạng thái — toggle switch (JS tìm cStatusToggle + statusLabel + cStatus hidden) -->
      <div class="form-group" style="margin-bottom:0">
        <label class="form-label">Trạng thái</label>
        <div class="toggle-wrap">
          <label class="toggle-switch">
            <input type="checkbox" id="cStatusToggle" checked onchange="syncStatus()">
            <span class="toggle-slider"></span>
          </label>
          <span id="statusLabel"
            style="font-size:13px;font-weight:700;color:var(--green)">
            Hoạt động
          </span>
          <!-- Giá trị thực JS đọc khi submit -->
          <input type="hidden" id="cStatus" value="1">
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal()">Huỷ</button>
      <!-- id="btnSave" là bắt buộc, JS tìm element này để loading state -->
      <button class="btn btn-primary" id="btnSave" onclick="saveCat()">
        <i class="fa-solid fa-floppy-disk"></i> Lưu
      </button>
    </div>
  </div>
</div>

<script src="assets/js/categories.js"></script>