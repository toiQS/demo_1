<?php
/**
 * products.php — Quản lý Sản phẩm
 */
$page_title    = 'Quản lý Sản phẩm';
$page_subtitle = 'Danh sách thiết bị điện tử & danh mục';
$active_nav    = 'products';
$extra_css     = 'assets/css/products.css';
$extra_js      = 'assets/js/products.js';
$pending_orders = 27; $low_stock_count = 5;

$categories = ['Điện thoại', 'Laptop & Máy tính', 'Máy tính bảng', 'Âm thanh', 'Phụ kiện'];
$products = [
  ['id'=>1,'name'=>'iPhone 15 Pro Max 256GB (Titan Đen)', 'category'=>'Điện thoại',       'cost'=>27000000,'profit'=>17,'price'=>31590000,'stock'=>2, 'status'=>1],
  ['id'=>2,'name'=>'Samsung Galaxy S24 Ultra 512GB',      'category'=>'Điện thoại',       'cost'=>22000000,'profit'=>18,'price'=>25960000,'stock'=>1, 'status'=>1],
  ['id'=>3,'name'=>'Laptop ASUS Vivobook 15 i5-1335U',    'category'=>'Laptop & Máy tính','cost'=>12500000,'profit'=>24,'price'=>15500000,'stock'=>6, 'status'=>1],
  ['id'=>4,'name'=>'MacBook Air M3 13" 16GB/512GB',       'category'=>'Laptop & Máy tính','cost'=>28000000,'profit'=>16,'price'=>32480000,'stock'=>3, 'status'=>1],
  ['id'=>5,'name'=>'iPad Pro 12.9" M4 Wi-Fi 256GB',       'category'=>'Máy tính bảng',    'cost'=>22000000,'profit'=>20,'price'=>26400000,'stock'=>7, 'status'=>1],
  ['id'=>6,'name'=>'AirPods Pro Gen 2 (USB-C)',            'category'=>'Âm thanh',         'cost'=>4800000, 'profit'=>25,'price'=>6000000, 'stock'=>4, 'status'=>0],
  ['id'=>7,'name'=>'Tai nghe Sony WH-1000XM5',            'category'=>'Âm thanh',         'cost'=>6500000, 'profit'=>23,'price'=>7995000, 'stock'=>9, 'status'=>1],
  ['id'=>8,'name'=>'Cáp USB-C 240W Anker (2m)',            'category'=>'Phụ kiện',         'cost'=>180000,  'profit'=>56,'price'=>280800, 'stock'=>66,'status'=>1],
];
require_once 'includes/layout.php';
?>

<!-- Page Actions -->
<div class="page-actions">
  <div class="search-box">
    <i class="fa-solid fa-magnifying-glass"></i>
    <input type="text" class="form-control" id="searchInput" placeholder="Tìm sản phẩm...">
  </div>
  <select class="form-control filter-select" id="filterCat">
    <option value="">Tất cả danh mục</option>
    <?php foreach($categories as $c): ?><option><?=$c?></option><?php endforeach; ?>
  </select>
  <select class="form-control filter-select" id="filterStatus">
    <option value="">Tất cả trạng thái</option>
    <option value="1">Đang bán</option>
    <option value="0">Ngừng bán</option>
  </select>
  <button class="btn btn-primary" onclick="openModal()">
    <i class="fa-solid fa-plus"></i> Thêm sản phẩm
  </button>
</div>

<!-- Table -->
<div class="card">
  <div class="card-header">
    <div class="card-title"><i class="fa-solid fa-box-open"></i> Danh sách sản phẩm</div>
    <span style="font-size:12px;color:var(--text-muted)"><?=count($products)?> sản phẩm</span>
  </div>
  <div style="overflow-x:auto">
    <table id="productTable">
      <thead>
        <tr>
          <th>Sản phẩm</th><th>Danh mục</th><th>Giá vốn</th>
          <th>% LN</th><th>Giá bán</th><th>Tồn kho</th>
          <th>Trạng thái</th><th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($products as $p): ?>
        <tr data-cat="<?=$p['category']?>" data-status="<?=$p['status']?>">
          <td>
            <div class="product-name-cell">
              <div class="product-img"><i class="fa-solid fa-shirt"></i></div>
              <div>
                <div style="font-weight:600;font-size:13px"><?=htmlspecialchars($p['name'])?></div>
                <div style="font-size:11px;color:var(--text-muted)">ID #<?=$p['id']?></div>
              </div>
            </div>
          </td>
          <td><?=$p['category']?></td>
          <td style="font-family:var(--mono);font-size:12px"><?=number_format($p['cost'])?>₫</td>
          <td style="font-family:var(--mono);font-size:12px;color:var(--purple)"><?=$p['profit']?>%</td>
          <td style="font-family:var(--mono);font-size:12px;color:var(--accent);font-weight:700"><?=number_format($p['price'])?>₫</td>
          <td class="<?=$p['stock']<=5?'stock-low':'stock-ok'?>" style="font-family:var(--mono)"><?=$p['stock']?></td>
          <td><?=$p['status']?'<span class="badge-active">Đang bán</span>':'<span class="badge-inactive">Ngừng bán</span>'?></td>
          <td>
            <div class="action-btns">
              <button class="btn btn-secondary btn-sm" onclick='openModal(<?=json_encode($p)?>)' title="Sửa">
                <i class="fa-solid fa-pen"></i>
              </button>
              <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?=$p['id']?>, '<?=addslashes($p['name'])?>')" title="Xoá">
                <i class="fa-solid fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Thêm/Sửa -->
<div class="modal-overlay" id="productModal">
  <div class="modal" style="width:580px">
    <div class="modal-header">
      <div class="modal-title" id="modalTitle">Thêm sản phẩm</div>
      <button class="btn-close-modal" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="pId">
      <div class="form-group">
        <label class="form-label">Tên sản phẩm *</label>
        <input type="text" class="form-control" id="pName" placeholder="Nhập tên sản phẩm">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Danh mục</label>
          <select class="form-control" id="pCat">
            <?php foreach($categories as $c):?><option><?=$c?></option><?php endforeach;?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Trạng thái</label>
          <select class="form-control" id="pStatus">
            <option value="1">Đang bán</option>
            <option value="0">Ngừng bán</option>
          </select>
        </div>
      </div>
      <div class="form-row-3">
        <div class="form-group">
          <label class="form-label">Giá vốn (₫)</label>
          <input type="number" class="form-control" id="pCost" placeholder="0">
        </div>
        <div class="form-group">
          <label class="form-label">% Lợi nhuận</label>
          <input type="number" class="form-control" id="pProfit" placeholder="0" oninput="calcPrice()">
        </div>
        <div class="form-group">
          <label class="form-label">Giá bán (₫)</label>
          <input type="number" class="form-control" id="pPrice" readonly style="color:var(--accent)">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Tồn kho ban đầu</label>
        <input type="number" class="form-control" id="pStock" placeholder="0">
      </div>
      <div class="form-group">
        <label class="form-label">Mô tả</label>
        <textarea class="form-control" id="pDesc" rows="3" placeholder="Mô tả ngắn..." style="resize:vertical"></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal()">Huỷ</button>
      <button class="btn btn-primary" onclick="saveProduct()">
        <i class="fa-solid fa-floppy-disk"></i> Lưu
      </button>
    </div>
  </div>
</div>

<?php require_once 'includes/layout_footer.php'; ?>
