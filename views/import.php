<?php
$page_title    = 'Quản lý Nhập hàng';
$page_subtitle = 'Tạo & quản lý phiếu nhập thiết bị điện tử';
$active_nav    = 'import';
$pending_orders = 27; $low_stock_count = 5;

$suppliers = ['Apple Việt Nam (FPT Trading)', 'Samsung Electronics VN', 'Xiaomi Việt Nam', 'ASUS Authorized Distributor', 'Công ty TNHH Phân phối Điện tử Digiworld'];
$products  = ['iPhone 15 Pro Max 256GB', 'Samsung Galaxy S24 Ultra', 'Laptop ASUS Vivobook 15', 'MacBook Air M3 13"', 'iPad Pro 12.9" M4', 'AirPods Pro Gen 2', 'Samsung Smart TV 55"', 'Tai nghe Sony WH-1000XM5'];

$receipts = [
  ['id'=>'PN0042','supplier'=>'Apple Việt Nam (FPT Trading)','date'=>'20/02/2026','total'=>348000000,'items'=>3,'status'=>'done','note'=>'Lô iPhone 15 & AirPods tháng 2'],
  ['id'=>'PN0041','supplier'=>'Samsung Electronics VN','date'=>'18/02/2026','total'=>195000000,'items'=>2,'status'=>'done','note'=>''],
  ['id'=>'PN0040','supplier'=>'Công ty TNHH Phân phối Điện tử Digiworld','date'=>'15/02/2026','total'=>520000000,'items'=>5,'status'=>'done','note'=>'Laptop & tablet mùa tựu trường'],
  ['id'=>'PN0039','supplier'=>'Xiaomi Việt Nam','date'=>'10/02/2026','total'=>142000000,'items'=>6,'status'=>'done','note'=>'Điện thoại & phụ kiện Xiaomi'],
  ['id'=>'PN0038','supplier'=>'ASUS Authorized Distributor','date'=>'05/02/2026','total'=>280000000,'items'=>4,'status'=>'draft','note'=>'Đang chờ hàng về kho'],
];
require_once 'includes/layout.php';
?>
<link rel="stylesheet" href="assets\css\import.css">

<!-- Stats row -->
<div class="stats-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px">
  <div class="stat-card">
    <div class="stat-icon si-blue"><i class="fa-solid fa-file-invoice"></i></div>
    <div class="stat-info">
      <div class="stat-label">Phiếu tháng này</div>
      <div class="stat-value">76</div>
      <div class="stat-sub">Tháng 02/2026</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon si-green"><i class="fa-solid fa-sack-dollar"></i></div>
    <div class="stat-info">
      <div class="stat-label">Tổng giá trị nhập</div>
      <div class="stat-value" style="font-size:18px;color:var(--green)">104M₫</div>
      <div class="stat-sub">Tháng 02/2026</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon si-amber"><i class="fa-solid fa-clock"></i></div>
    <div class="stat-info">
      <div class="stat-label">Đang chờ hàng</div>
      <div class="stat-value" style="color:var(--orange)">1</div>
      <div class="stat-sub">Phiếu nháp</div>
    </div>
  </div>
</div>

<div class="page-actions">
  <div class="search-box">
    <i class="fa-solid fa-magnifying-glass"></i>
    <input type="text" class="form-control" id="searchInput" placeholder="Tìm phiếu nhập...">
  </div>
  <button class="btn btn-primary" onclick="openModal()"><i class="fa-solid fa-plus"></i> Tạo phiếu nhập</button>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title"><i class="fa-solid fa-truck-ramp-box"></i> Danh sách phiếu nhập</div>
  </div>
  <div style="overflow-x:auto">
    <table id="importTable">
      <thead><tr><th>Mã phiếu</th><th>Nhà cung cấp</th><th>Ngày nhập</th><th>Số mặt hàng</th><th>Tổng tiền</th><th>Trạng thái</th><th>Ghi chú</th><th>Thao tác</th></tr></thead>
      <tbody>
        <?php foreach($receipts as $r): ?>
        <tr>
          <td><span class="order-id"><?=$r['id']?></span></td>
          <td style="font-size:13px;font-weight:500"><?=htmlspecialchars($r['supplier'])?></td>
          <td class="order-date"><?=$r['date']?></td>
          <td style="text-align:center;font-family:var(--mono);color:var(--blue)"><?=$r['items']?></td>
          <td style="font-family:var(--mono);color:var(--accent);font-weight:700"><?=number_format($r['total'])?>₫</td>
          <td><?=$r['status']==='done'?'<span class="badge-done">Hoàn thành</span>':'<span class="badge-draft">Nháp</span>'?></td>
          <td style="font-size:12px;color:var(--text-muted)"><?=htmlspecialchars($r['note'])?:'-'?></td>
          <td>
            <div class="action-btns">
              <button class="btn btn-secondary btn-sm" onclick='openModal(<?=json_encode($r)?>)' title="Sửa"><i class="fa-solid fa-pen"></i></button>
              <?php if($r['status']==='draft'): ?>
              <button class="btn btn-primary btn-sm" onclick="confirmImport('<?=$r['id']?>')" title="Xác nhận nhập"><i class="fa-solid fa-check"></i></button>
              <?php endif; ?>
              <button class="btn btn-danger btn-sm" onclick="confirmDelete('<?=$r['id']?>')" title="Xoá"><i class="fa-solid fa-trash"></i></button>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Tạo/Sửa phiếu nhập -->
<div class="modal-overlay" id="importModal">
  <div class="modal-xl">
    <div class="modal-header">
      <div class="modal-title" id="modalTitle">Tạo phiếu nhập</div>
      <button class="btn-close-modal" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Nhà cung cấp *</label>
          <select class="form-control" id="iSupplier">
            <?php foreach($suppliers as $s): ?><option><?=$s?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Ngày nhập</label>
          <input type="date" class="form-control" id="iDate" value="<?=date('Y-m-d')?>">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Ghi chú</label>
        <input type="text" class="form-control" id="iNote" placeholder="Ghi chú phiếu nhập...">
      </div>

      <div class="import-items">
        <div class="import-items-header">
          <h4><i class="fa-solid fa-list" style="margin-right:6px;color:var(--accent)"></i>Danh sách hàng nhập</h4>
          <button class="btn btn-secondary" style="padding:5px 12px;font-size:12px" onclick="addItemRow()"><i class="fa-solid fa-plus"></i> Thêm hàng</button>
        </div>
        <div id="itemRows"></div>
        <div class="grand-total">
          <span>Tổng giá trị phiếu nhập:</span>
          <strong id="grandTotal">0₫</strong>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal()">Huỷ</button>
      <button class="btn btn-secondary" onclick="saveImport('draft')"><i class="fa-solid fa-floppy-disk"></i> Lưu nháp</button>
      <button class="btn btn-primary" onclick="saveImport('done')"><i class="fa-solid fa-check"></i> Xác nhận nhập kho</button>
    </div>
  </div>
</div>

<script src="../js/import.js"></script>
<?php require_once 'includes/layout_footer.php'; ?>
