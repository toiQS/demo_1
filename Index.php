<?php

$page_title    = 'Dashboard';
$page_subtitle = 'Xin chào! Đây là tổng quan hệ thống hôm nay.';
$active_nav    = 'home';

include_once 'assets/layout.php';

require_once 'controllers\index_actions\get_count_users.php';
require_once 'controllers\index_actions\get_count_orders.php';
require_once 'controllers\index_actions\get_count_orders_pending_comfirmed.php';
require_once 'controllers\index_actions\get_monthly_revenue.php';
require_once 'controllers\index_actions\get_count_products.php';

$stats = [
  'total_orders'    => $result_order_count,
  'pending_orders'  => $result_order_pending_comfirmed_count,
  'total_products'  => $result_product_count,
  'low_stock'       => 5,
  'total_users'     => $result_user_count,
  'revenue_month'   => $result_monthly_revenue,
  'import_receipts' => 42,
];


?>

<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon si-amber"><i class="fa-solid fa-receipt"></i></div>
    <div class="stat-info">
      <div class="stat-label">Tổng đơn hàng</div>
      <div class="stat-value"><?= number_format($stats['total_orders']) ?></div>
      <!-- <div class="stat-sub"><span class="up">▲ 12%</span> so với tháng trước</div> -->
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon si-orange"><i class="fa-solid fa-clock-rotate-left"></i></div>
    <div class="stat-info">
      <div class="stat-label">Chờ xác nhận</div>
      <div class="stat-value" style="color:var(--orange)"><?= $stats['pending_orders'] ?></div>
      <div class="stat-sub">Cần xử lý ngay hôm nay</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon si-green"><i class="fa-solid fa-sack-dollar"></i></div>
    <div class="stat-info">
      <div class="stat-label">Doanh thu tháng</div>
      <div class="stat-value" style="font-size:18px;color:var(--green)"><?= number_format($stats['revenue_month']) ?>₫</div>
      <!-- <div class="stat-sub"><span class="up">▲ 8.4%</span> so với tháng trước</div> -->
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon si-blue"><i class="fa-solid fa-box-open"></i></div>
    <div class="stat-info">
      <div class="stat-label">Tổng sản phẩm</div>
      <div class="stat-value"><?= number_format($stats['total_products']) ?></div>
      <div class="stat-sub"><?= $stats['low_stock'] ?> sản phẩm sắp hết hàng</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon si-purple"><i class="fa-solid fa-users"></i></div>
    <div class="stat-info">
      <div class="stat-label">Người dùng</div>
      <div class="stat-value"><?= number_format($stats['total_users']) ?></div>
      <!-- <div class="stat-sub"><span class="up">▲ 5.2%</span> tháng này</div> -->
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon si-red"><i class="fa-solid fa-truck-ramp-box"></i></div>
    <div class="stat-info">
      <div class="stat-label">Phiếu nhập</div>
      <div class="stat-value"><?= $stats['import_receipts'] ?></div>
      <div class="stat-sub">Tháng <?= date('m/Y') ?></div>
    </div>
  </div>
</div>

<!-- ===== MODULES ===== -->
<div class="section-header">
  <div class="section-title">Chức năng quản lý</div>
</div>

<div class="modules-grid">
  <a href="products.php" class="module-card">
    <div class="module-icon si-blue"><i class="fa-solid fa-box-open"></i></div>
    <div class="module-content">
      <h3>Quản lý Sản phẩm</h3>
      <p>Thêm, sửa, xóa sản phẩm &amp; danh mục</p>
    </div>
    <div class="module-arrow"><i class="fa-solid fa-chevron-right"></i></div>
  </a>

  <a href="import.php" class="module-card">
    <div class="module-icon si-green"><i class="fa-solid fa-truck-ramp-box"></i></div>
    <div class="module-content">
      <h3>Quản lý Nhập hàng</h3>
      <p>Tạo &amp; sửa phiếu nhập kho</p>
    </div>
    <div class="module-arrow"><i class="fa-solid fa-chevron-right"></i></div>
  </a>

  <a href="pricing.php" class="module-card">
    <div class="module-icon si-amber"><i class="fa-solid fa-tag"></i></div>
    <div class="module-content">
      <h3>Quản lý Giá bán</h3>
      <p>Cài % lợi nhuận, tra cứu giá vốn &amp; giá bán</p>
    </div>
    <div class="module-arrow"><i class="fa-solid fa-chevron-right"></i></div>
  </a>

  <a href="orders.php" class="module-card">
    <div class="module-icon si-purple"><i class="fa-solid fa-receipt"></i></div>
    <div class="module-content">
      <h3>Quản lý Đơn hàng</h3>
      <p>Xem, lọc và cập nhật trạng thái đơn</p>
    </div>
    <div class="module-arrow"><i class="fa-solid fa-chevron-right"></i></div>
  </a>

  <a href="inventory.php" class="module-card">
    <div class="module-icon si-orange"><i class="fa-solid fa-warehouse"></i></div>
    <div class="module-content">
      <h3>Tồn kho &amp; Thống kê</h3>
      <p>Tra cứu tồn kho, báo cáo nhập-xuất, cảnh báo</p>
    </div>
    <div class="module-arrow"><i class="fa-solid fa-chevron-right"></i></div>
  </a>

  <a href="users.php" class="module-card">
    <div class="module-icon si-red"><i class="fa-solid fa-users-gear"></i></div>
    <div class="module-content">
      <h3>Quản lý Người dùng</h3>
      <p>Thêm tài khoản, đặt lại mật khẩu, khoá TK</p>
    </div>
    <div class="module-arrow"><i class="fa-solid fa-chevron-right"></i></div>
  </a>
</div>

<!-- ===== BOTTOM GRID ===== -->
<div class="bottom-grid">

  <!-- Recent Orders -->
  <div class="card">
    <div class="card-header">
      <div class="card-title"><i class="fa-solid fa-clock-rotate-left"></i> Đơn hàng gần đây</div>
      <a href="orders.php" class="btn-view-all">Xem tất cả</a>
    </div>
    <table>
      <thead>
        <tr>
          <th>Mã đơn</th>
          <th>Khách hàng</th>
          <th>Tổng tiền</th>
          <th>Trạng thái</th>
          <th>Ngày đặt</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($recent_orders as $o): ?>
        <tr>
          <td><span class="order-id"><?= htmlspecialchars($o['id']) ?></span></td>
          <td><span class="customer-name"><?= htmlspecialchars($o['customer']) ?></span></td>
          <td><span class="order-total"><?= htmlspecialchars($o['total']) ?></span></td>
          <td>
            <?php $s = $status_labels[$o['status']]; ?>
            <span class="badge <?= $s['class'] ?>"><?= $s['label'] ?></span>
          </td>
          <td><span class="order-date"><?= htmlspecialchars($o['date']) ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Low Stock Alerts -->
  <div class="card">
    <div class="card-header">
      <div class="card-title" style="color:var(--red)">
        <i class="fa-solid fa-triangle-exclamation"></i> Sản phẩm sắp hết hàng
      </div>
      <a href="inventory.php" class="btn-view-all">Xem kho</a>
    </div>
    <div class="low-stock-list">
      <?php foreach ($low_stock_items as $item): ?>
      <div class="low-stock-item">
        <div class="stock-icon"><i class="fa-solid fa-box-open"></i></div>
        <div class="stock-info">
          <div class="stock-name"><?= htmlspecialchars($item['name']) ?></div>
          <div class="stock-meta">Ngưỡng cảnh báo: <?= $item['threshold'] ?> sản phẩm</div>
          <div class="stock-progress">
            <div class="stock-bar" style="width:<?= min(100, round($item['qty'] / $item['threshold'] * 100)) ?>%"></div>
          </div>
        </div>
        <div class="stock-qty"><?= $item['qty'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

</div><!-- /bottom-grid -->

<?php require_once 'assets/layout_footer.php'; ?>