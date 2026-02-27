<?php
$page_title    = 'Tồn kho & Thống kê';
$page_subtitle = 'Tra cứu tồn kho thiết bị điện tử, báo cáo nhập-xuất và cảnh báo sắp hết hàng';
$active_nav    = 'inventory';
$pending_orders = 27; $low_stock_count = 5;

$products = [
  ['id'=>1,'name'=>'iPhone 15 Pro Max 256GB (Titan Đen)',  'category'=>'Điện thoại',       'imported'=>30, 'sold'=>28,'stock'=>2, 'threshold'=>5, 'cost'=>27000000,'price'=>31500000],
  ['id'=>2,'name'=>'Samsung Galaxy S24 Ultra 512GB',       'category'=>'Điện thoại',       'imported'=>25, 'sold'=>24,'stock'=>1, 'threshold'=>5, 'cost'=>22000000,'price'=>26000000],
  ['id'=>3,'name'=>'Laptop ASUS Vivobook 15 i5-1335U',     'category'=>'Laptop & Máy tính','imported'=>20, 'sold'=>14,'stock'=>6, 'threshold'=>5, 'cost'=>12500000,'price'=>15500000],
  ['id'=>4,'name'=>'MacBook Air M3 13" 16GB/512GB',        'category'=>'Laptop & Máy tính','imported'=>15, 'sold'=>12,'stock'=>3, 'threshold'=>5, 'cost'=>28000000,'price'=>32500000],
  ['id'=>5,'name'=>'iPad Pro 12.9" M4 Wi-Fi 256GB',        'category'=>'Máy tính bảng',    'imported'=>20, 'sold'=>13,'stock'=>7, 'threshold'=>5, 'cost'=>22000000,'price'=>26500000],
  ['id'=>6,'name'=>'AirPods Pro Gen 2 (USB-C)',             'category'=>'Âm thanh',         'imported'=>50, 'sold'=>46,'stock'=>4, 'threshold'=>10,'cost'=>4800000, 'price'=>6000000],
  ['id'=>7,'name'=>'Tai nghe Sony WH-1000XM5',             'category'=>'Âm thanh',         'imported'=>30, 'sold'=>21,'stock'=>9, 'threshold'=>8, 'cost'=>6500000, 'price'=>8000000],
  ['id'=>8,'name'=>'Cáp USB-C 240W Anker (2m)',             'category'=>'Phụ kiện',         'imported'=>200,'sold'=>134,'stock'=>66,'threshold'=>30,'cost'=>180000,  'price'=>280000],
];

$low_stock = array_filter($products, fn($p) => $p['stock'] <= $p['threshold']);
require_once 'includes/layout.php';
?>
<link rel="stylesheet" href="../css/inventory.css">

<!-- Stats -->
<div class="stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px">
  <div class="stat-card">
    <div class="stat-icon si-blue"><i class="fa-solid fa-boxes-stacked"></i></div>
    <div class="stat-info">
      <div class="stat-label">Tổng tồn kho</div>
      <div class="stat-value"><?=array_sum(array_column($products,'stock'))?></div>
      <div class="stat-sub">Tất cả sản phẩm</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon si-red"><i class="fa-solid fa-triangle-exclamation"></i></div>
    <div class="stat-info">
      <div class="stat-label">Sắp hết hàng</div>
      <div class="stat-value" style="color:var(--red)"><?=count($low_stock)?></div>
      <div class="stat-sub">Dưới ngưỡng cảnh báo</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon si-green"><i class="fa-solid fa-arrow-trend-up"></i></div>
    <div class="stat-info">
      <div class="stat-label">Tổng đã bán</div>
      <div class="stat-value"><?=number_format(array_sum(array_column($products,'sold')))?></div>
      <div class="stat-sub">Từ trước đến nay</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon si-amber"><i class="fa-solid fa-truck-ramp-box"></i></div>
    <div class="stat-info">
      <div class="stat-label">Tổng đã nhập</div>
      <div class="stat-value"><?=number_format(array_sum(array_column($products,'imported')))?></div>
      <div class="stat-sub">Từ trước đến nay</div>
    </div>
  </div>
</div>

<!-- Cảnh báo sắp hết hàng -->
<?php if(count($low_stock)): ?>
<div class="alert-card">
  <div class="alert-card-header"><i class="fa-solid fa-bell"></i> Cảnh báo: <?=count($low_stock)?> sản phẩm sắp hết hàng</div>
  <?php foreach($low_stock as $p): ?>
  <div class="mini-stock-item">
    <div class="mini-name">
      <?=htmlspecialchars($p['name'])?>
      <div style="font-size:11px;color:var(--text-muted)">Ngưỡng cảnh báo: <?=$p['threshold']?> sản phẩm</div>
    </div>
    <div style="flex:1;padding:0 16px">
      <div class="mini-progress"><div class="mini-bar" style="width:<?=min(100,round($p['stock']/$p['threshold']*100))?>%"></div></div>
    </div>
    <div class="mini-qty"><?=$p['stock']?></div>
    <button class="btn btn-secondary" style="padding:5px 12px;font-size:12px;margin-left:12px" onclick="showToast('Chức năng tạo phiếu nhập nhanh','info')">
      <i class="fa-solid fa-plus"></i> Nhập thêm
    </button>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Tabs -->
<div class="tab-bar">
  <button class="tab-btn active" data-filter="" onclick="filterTab(this)">Tất cả</button>
  <button class="tab-btn" data-filter="low" onclick="filterTab(this)">Sắp hết hàng</button>
  <button class="tab-btn" data-filter="ok"  onclick="filterTab(this)">Còn hàng đủ</button>
</div>

<!-- Search -->
<div class="page-actions">
  <div class="search-box">
    <i class="fa-solid fa-magnifying-glass"></i>
    <input type="text" class="form-control" id="searchInput" placeholder="Tìm sản phẩm...">
  </div>
  <select class="form-control" style="width:160px" id="filterCat">
    <option value="">Tất cả danh mục</option>
    <option>Điện thoại</option><option>Laptop & Máy tính</option><option>Máy tính bảng</option><option>Âm thanh</option><option>Phụ kiện</option>
  </select>
  <button class="btn btn-secondary" onclick="exportReport()"><i class="fa-solid fa-file-export"></i> Xuất báo cáo</button>
</div>

<!-- Inventory Table -->
<div class="card">
  <div class="card-header">
    <div class="card-title"><i class="fa-solid fa-warehouse"></i> Báo cáo tồn kho chi tiết</div>
  </div>
  <div style="overflow-x:auto">
    <table id="invTable">
      <thead>
        <tr>
          <th>Sản phẩm</th>
          <th>Danh mục</th>
          <th style="text-align:center">Đã nhập</th>
          <th style="text-align:center">Đã bán</th>
          <th style="text-align:center">Tồn kho</th>
          <th>Tỷ lệ bán</th>
          <th>Giá trị tồn</th>
          <th>Trạng thái</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($products as $p):
          $rate = $p['imported'] > 0 ? round($p['sold']/$p['imported']*100) : 0;
          $isLow = $p['stock'] <= $p['threshold'];
        ?>
        <tr class="<?=$isLow?'alert-row':''?>" data-low="<?=$isLow?1:0?>" data-cat="<?=$p['category']?>">
          <td style="font-weight:600;font-size:13px">
            <?=htmlspecialchars($p['name'])?>
            <?php if($isLow): ?><span style="margin-left:6px;font-size:10px;background:rgba(248,81,73,.15);color:var(--red);padding:1px 6px;border-radius:10px;font-weight:700">⚠ Sắp hết</span><?php endif; ?>
          </td>
          <td style="color:var(--text-muted);font-size:12px"><?=$p['category']?></td>
          <td style="text-align:center;font-family:var(--mono);color:var(--blue)"><?=$p['imported']?></td>
          <td style="text-align:center;font-family:var(--mono);color:var(--green)"><?=$p['sold']?></td>
          <td style="text-align:center">
            <span style="font-family:var(--mono);font-weight:800;font-size:16px;color:<?=$isLow?'var(--red)':'var(--text-primary)'?>"><?=$p['stock']?></span>
            <div style="font-size:10px;color:var(--text-muted)">ngưỡng: <?=$p['threshold']?></div>
          </td>
          <td>
            <div style="display:flex;align-items:center;gap:8px">
              <div class="stock-bar-wrap">
                <div class="stock-bar-fill" style="width:<?=$rate?>%;background:<?=$rate>70?'var(--green)':($rate>40?'var(--accent)':'var(--red)')?>"></div>
              </div>
              <span style="font-size:11px;color:var(--text-muted)"><?=$rate?>%</span>
            </div>
          </td>
          <td style="font-family:var(--mono);font-size:12px;color:var(--accent)"><?=number_format($p['stock']*$p['cost'])?>₫</td>
          <td>
            <?php if($p['stock']===0): ?>
              <span style="background:rgba(248,81,73,.15);color:var(--red);padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600">Hết hàng</span>
            <?php elseif($isLow): ?>
              <span style="background:rgba(210,153,34,.15);color:var(--orange);padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600">Sắp hết</span>
            <?php else: ?>
              <span style="background:rgba(63,185,80,.15);color:var(--green);padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600">Còn hàng</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="../js/inventory.js"></script>
<?php require_once 'includes/layout_footer.php'; ?>