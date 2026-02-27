<?php
$page_title    = 'Quản lý Giá bán';
$page_subtitle = 'Cài đặt % lợi nhuận và tra cứu giá bán thiết bị điện tử';
$active_nav    = 'pricing';
$pending_orders = 27; $low_stock_count = 5;

$products = [
  ['id'=>1,'name'=>'iPhone 15 Pro Max 256GB (Titan Đen)', 'category'=>'Điện thoại',       'cost'=>27000000,'profit'=>17,'price'=>31590000,'updated'=>'15/02/2026'],
  ['id'=>2,'name'=>'Samsung Galaxy S24 Ultra 512GB',      'category'=>'Điện thoại',       'cost'=>22000000,'profit'=>18,'price'=>25960000,'updated'=>'15/02/2026'],
  ['id'=>3,'name'=>'Laptop ASUS Vivobook 15 i5-1335U',    'category'=>'Laptop & Máy tính','cost'=>12500000,'profit'=>24,'price'=>15500000,'updated'=>'10/02/2026'],
  ['id'=>4,'name'=>'MacBook Air M3 13" 16GB/512GB',       'category'=>'Laptop & Máy tính','cost'=>28000000,'profit'=>16,'price'=>32480000,'updated'=>'10/02/2026'],
  ['id'=>5,'name'=>'iPad Pro 12.9" M4 Wi-Fi 256GB',       'category'=>'Máy tính bảng',    'cost'=>22000000,'profit'=>20,'price'=>26400000,'updated'=>'01/02/2026'],
  ['id'=>6,'name'=>'AirPods Pro Gen 2 (USB-C)',            'category'=>'Âm thanh',         'cost'=>4800000, 'profit'=>25,'price'=>6000000, 'updated'=>'01/02/2026'],
  ['id'=>7,'name'=>'Tai nghe Sony WH-1000XM5',            'category'=>'Âm thanh',         'cost'=>6500000, 'profit'=>23,'price'=>7995000, 'updated'=>'20/01/2026'],
  ['id'=>8,'name'=>'Cáp USB-C 240W Anker (2m)',            'category'=>'Phụ kiện',         'cost'=>180000,  'profit'=>56,'price'=>280800, 'updated'=>'20/01/2026'],
];
require_once 'includes/layout.php';
?>
<link rel="stylesheet" href="../css/pricing.css">

<!-- Bulk update bar -->
<div class="bulk-bar">
  <h4><i class="fa-solid fa-wand-magic-sparkles" style="color:var(--accent);margin-right:6px"></i>Cập nhật % LN hàng loạt</h4>
  <select class="form-control" id="bulkCat" style="width:160px">
    <option value="">Tất cả danh mục</option>
    <option>Áo</option><option>Quần</option><option>Giày</option><option>Túi xách</option><option>Phụ kiện</option>
  </select>
  <input type="number" class="form-control" id="bulkProfit" placeholder="% LN mới" style="width:120px">
  <button class="btn btn-primary" onclick="applyBulk()"><i class="fa-solid fa-rotate"></i> Áp dụng</button>
</div>

<div class="page-actions">
  <div class="search-box">
    <i class="fa-solid fa-magnifying-glass"></i>
    <input type="text" class="form-control" id="searchInput" placeholder="Tìm sản phẩm...">
  </div>
  <span style="font-size:12px;color:var(--text-muted)"><?=count($products)?> sản phẩm</span>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title"><i class="fa-solid fa-tag"></i> Bảng giá sản phẩm</div>
    <span style="font-size:12px;color:var(--text-muted)">Click vào % LN để sửa trực tiếp</span>
  </div>
  <div style="overflow-x:auto">
    <table id="pricingTable">
      <thead>
        <tr>
          <th>Sản phẩm</th>
          <th>Danh mục</th>
          <th>Giá vốn</th>
          <th>% Lợi nhuận</th>
          <th>Giá bán</th>
          <th>LN/SP</th>
          <th>Cập nhật</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($products as $p): ?>
        <tr data-id="<?=$p['id']?>" data-cost="<?=$p['cost']?>">
          <td style="font-weight:600;font-size:13px"><?=htmlspecialchars($p['name'])?></td>
          <td style="color:var(--text-muted);font-size:12px"><?=$p['category']?></td>
          <td style="font-family:var(--mono);font-size:12px"><?=number_format($p['cost'])?>₫</td>
          <td>
            <div style="display:flex;align-items:center;gap:8px">
              <input type="number" class="editable-profit" value="<?=$p['profit']?>" min="0" max="500"
                oninput="updateRow(this)"
                onblur="saveProfit(this)">
              <span style="color:var(--text-muted);font-size:11px">%</span>
            </div>
            <div class="profit-bar"><div class="profit-fill" style="width:<?=min(100,$p['profit']/2)?>%" id="bar_<?=$p['id']?>"></div></div>
          </td>
          <td style="font-family:var(--mono);font-size:13px;font-weight:700;color:var(--accent)" id="price_<?=$p['id']?>"><?=number_format($p['price'])?>₫</td>
          <td style="font-family:var(--mono);font-size:12px;color:var(--green)" id="margin_<?=$p['id']?>"><?=number_format($p['price']-$p['cost'])?>₫</td>
          <td style="font-size:11px;color:var(--text-muted)"><?=$p['updated']?></td>
          <td>
            <button class="btn btn-secondary btn-sm" onclick='openModal(<?=json_encode($p)?>)' title="Sửa chi tiết"><i class="fa-solid fa-pen"></i> Sửa</button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal chi tiết giá -->
<div class="modal-overlay" id="priceModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="modalTitle">Cập nhật giá</div>
      <button class="btn-close-modal" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Sản phẩm</label>
        <input type="text" class="form-control" id="mProduct" readonly style="color:var(--text-muted)">
      </div>
      <div class="form-group">
        <label class="form-label">Giá vốn (₫)</label>
        <input type="number" class="form-control" id="mCost" oninput="modalCalc()">
      </div>
      <div class="form-group">
        <label class="form-label">% Lợi nhuận mong muốn</label>
        <input type="number" class="form-control" id="mProfit" oninput="modalCalc()">
      </div>
      <div class="price-preview">
        <div class="price-preview-row"><span>Giá vốn</span><span id="prevCost" style="font-family:var(--mono)">0₫</span></div>
        <div class="price-preview-row"><span>Lợi nhuận</span><span id="prevMargin" style="font-family:var(--mono);color:var(--green)">+0₫</span></div>
        <div class="price-preview-row total"><span>Giá bán</span><span id="prevPrice">0₫</span></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal()">Huỷ</button>
      <button class="btn btn-primary" onclick="saveModal()"><i class="fa-solid fa-floppy-disk"></i> Lưu giá</button>
    </div>
  </div>
</div>

<script src="../js/pricing.js"></script>
<?php require_once 'includes/layout_footer.php'; ?>
