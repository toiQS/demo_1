<?php
$page_title    = 'Quản lý Đơn hàng';
$page_subtitle = 'Xem, lọc và cập nhật trạng thái đơn hàng thiết bị điện tử';
$active_nav    = 'orders';
$pending_orders = 27; $low_stock_count = 5;

$orders = [
  ['id'=>'DH0984','customer'=>'Nguyễn Văn An',   'phone'=>'0901234567','address'=>'12 Lê Lợi, Q.1, TP.HCM','total'=>31500000,'status'=>'pending',   'date'=>'20/02/2026','items'=>[['name'=>'iPhone 15 Pro Max 256GB (Titan Đen)','qty'=>1,'price'=>31500000]],'note'=>'Giao buổi sáng, gọi trước 30 phút'],
  ['id'=>'DH0983','customer'=>'Trần Thị Bích',   'phone'=>'0912345678','address'=>'45 Trần Hưng Đạo, Q.5, TP.HCM','total'=>14500000,'status'=>'processing','date'=>'20/02/2026','items'=>[['name'=>'Laptop ASUS Vivobook 15 i5','qty'=>1,'price'=>15500000],['name'=>'Cáp USB-C 240W Anker','qty'=>1,'price'=>280000]],'note'=>''],
  ['id'=>'DH0982','customer'=>'Lê Minh Cường',   'phone'=>'0923456789','address'=>'78 Nguyễn Huệ, Q.1, TP.HCM','total'=>52000000,'status'=>'shipped',   'date'=>'19/02/2026','items'=>[['name'=>'MacBook Air M3 13" 16GB/512GB','qty'=>1,'price'=>32500000],['name'=>'iPad Pro 12.9" M4','qty'=>1,'price'=>26500000]],'note'=>'Đóng gói kỹ, hàng dễ vỡ'],
  ['id'=>'DH0981','customer'=>'Phạm Thị Diệu',   'phone'=>'0934567890','address'=>'90 Đinh Tiên Hoàng, Bình Thạnh, TP.HCM','total'=>6000000,'status'=>'completed','date'=>'19/02/2026','items'=>[['name'=>'AirPods Pro Gen 2 (USB-C)','qty'=>1,'price'=>6000000]],'note'=>''],
  ['id'=>'DH0980','customer'=>'Hoàng Quốc Dũng', 'phone'=>'0945678901','address'=>'55 Cách Mạng Tháng 8, Q.3, TP.HCM','total'=>9800000,'status'=>'cancelled','date'=>'18/02/2026','items'=>[['name'=>'Samsung Galaxy S24 Ultra 512GB','qty'=>1,'price'=>26000000]],'note'=>'Khách huỷ - đổi ý mua hãng khác'],
  ['id'=>'DH0979','customer'=>'Vũ Thị Hương',    'phone'=>'0956789012','address'=>'33 Võ Văn Tần, Q.3, TP.HCM','total'=>8000000,'status'=>'completed','date'=>'17/02/2026','items'=>[['name'=>'Tai nghe Sony WH-1000XM5','qty'=>1,'price'=>8000000]],'note'=>''],
  ['id'=>'DH0978','customer'=>'Đặng Thanh Tuấn',  'phone'=>'0967890123','address'=>'20 Phan Xích Long, Phú Nhuận, TP.HCM','total'=>26500000,'status'=>'pending','date'=>'17/02/2026','items'=>[['name'=>'iPad Pro 12.9" M4 Wi-Fi 256GB','qty'=>1,'price'=>26500000]],'note'=>''],
];

$status_map = [
  'pending'    => ['label'=>'Chờ xác nhận','class'=>'badge-pending','next'=>'processing','next_label'=>'Xác nhận'],
  'processing' => ['label'=>'Đang xử lý',  'class'=>'badge-processing','next'=>'shipped','next_label'=>'Giao hàng'],
  'shipped'    => ['label'=>'Đang giao',   'class'=>'badge-shipped','next'=>'completed','next_label'=>'Hoàn thành'],
  'completed'  => ['label'=>'Hoàn thành', 'class'=>'badge-completed','next'=>null,'next_label'=>null],
  'cancelled'  => ['label'=>'Đã huỷ',     'class'=>'badge-cancelled','next'=>null,'next_label'=>null],
];
require_once 'includes/layout.php';
?>
<link rel="stylesheet" href="../css/orders.css">

<!-- Filter Bar -->
<div class="filter-bar">
  <div class="search-box">
    <i class="fa-solid fa-magnifying-glass"></i>
    <input type="text" class="form-control" id="searchInput" placeholder="Mã đơn, tên khách...">
  </div>
  <input type="date" class="form-control" id="filterFrom" style="width:150px" title="Từ ngày">
  <input type="date" class="form-control" id="filterTo" style="width:150px" title="Đến ngày">
  <button class="btn btn-secondary" onclick="applyFilters()"><i class="fa-solid fa-filter"></i> Lọc</button>
  <button class="btn btn-secondary" onclick="resetFilters()"><i class="fa-solid fa-rotate-left"></i></button>
</div>

<!-- Tab Status -->
<div class="tab-bar">
  <button class="tab-btn active" data-status="" onclick="filterByTab(this)">Tất cả (<?=count($orders)?>)</button>
  <button class="tab-btn" data-status="pending" onclick="filterByTab(this)">Chờ xác nhận (<?=count(array_filter($orders,fn($o)=>$o['status']==='pending'))?>)</button>
  <button class="tab-btn" data-status="processing" onclick="filterByTab(this)">Đang xử lý</button>
  <button class="tab-btn" data-status="shipped" onclick="filterByTab(this)">Đang giao</button>
  <button class="tab-btn" data-status="completed" onclick="filterByTab(this)">Hoàn thành</button>
  <button class="tab-btn" data-status="cancelled" onclick="filterByTab(this)">Đã huỷ</button>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title"><i class="fa-solid fa-receipt"></i> Danh sách đơn hàng</div>
    <a href="#" onclick="window.print()" class="btn-view-all"><i class="fa-solid fa-print"></i> In danh sách</a>
  </div>
  <div style="overflow-x:auto">
    <table id="ordersTable">
      <thead><tr><th>Mã đơn</th><th>Khách hàng</th><th>Ngày đặt</th><th>Tổng tiền</th><th>Trạng thái</th><th>Thao tác</th></tr></thead>
      <tbody>
        <?php foreach($orders as $o): $s=$status_map[$o['status']]; ?>
        <tr data-status="<?=$o['status']?>" data-id="<?=$o['id']?>">
          <td><span class="order-id">#<?=$o['id']?></span></td>
          <td>
            <div style="font-weight:600;font-size:13px"><?=htmlspecialchars($o['customer'])?></div>
            <div style="font-size:11px;color:var(--text-muted)"><?=$o['phone']?></div>
          </td>
          <td><span class="order-date"><?=$o['date']?></span></td>
          <td><span class="order-total"><?=number_format($o['total'])?>₫</span></td>
          <td><span class="badge <?=$s['class']?>"><?=$s['label']?></span></td>
          <td>
            <div class="action-btns">
              <button class="btn btn-secondary btn-sm" onclick='openDetail(<?=json_encode($o)?>)' title="Xem chi tiết"><i class="fa-solid fa-eye"></i></button>
              <?php if($s['next']): ?>
              <button class="btn btn-primary btn-sm" onclick="updateStatus('<?=$o['id']?>','<?=$o['status']?>','<?=$s['next']?>')" title="<?=$s['next_label']?>">
                <i class="fa-solid fa-circle-check"></i> <?=$s['next_label']?>
              </button>
              <?php endif; ?>
              <?php if($o['status']==='pending'): ?>
              <button class="btn btn-danger btn-sm" onclick="cancelOrder('<?=$o['id']?>')" title="Huỷ"><i class="fa-solid fa-ban"></i></button>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Order Detail Modal -->
<div class="modal-overlay" id="orderModal">
  <div class="modal-lg">
    <div class="modal-header">
      <div class="modal-title" id="orderModalTitle">Chi tiết đơn hàng</div>
      <button class="btn-close-modal" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" id="orderModalBody"></div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal()">Đóng</button>
      <button class="btn btn-primary" id="nextStepBtn" style="display:none"></button>
    </div>
  </div>
</div>

<script src="../js/orders.js"></script>
<?php require_once 'includes/layout_footer.php'; ?>
