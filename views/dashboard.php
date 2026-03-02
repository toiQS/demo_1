<?php
// views/dashboard.php

$currentPage = 'dashboard';
$pageTitle   = 'DASHBOARD';
$breadcrumb  = 'Tổng quan / Dashboard';
require_once 'includes/header.php';

// TODO: Kết nối DB và lấy số liệu thực
// require_once '../database/db.php';
// $doanhThu = ...
// $donHang  = ...
include_once __DIR__ . '/../services/dashboard/load_starts.php';
include_once __DIR__ . '/../services/dashboard/get_recent_order.php';
?>

<!-- QUICK ACTIONS -->
<div class="quick-grid">
  <a href="products.php?action=add" class="quick-btn">
    <div class="quick-icon" style="background:rgba(245,166,35,0.12)">➕</div>
    <div class="quick-label">Thêm sản phẩm</div>
  </a>
  <a href="import.php?action=add" class="quick-btn">
    <div class="quick-icon" style="background:rgba(59,130,246,0.12)">📦</div>
    <div class="quick-label">Tạo phiếu nhập</div>
  </a>
  <a href="promo.php?action=add" class="quick-btn">
    <div class="quick-icon" style="background:rgba(34,197,94,0.12)">🎟</div>
    <div class="quick-label">Tạo khuyến mãi</div>
  </a>
  <a href="inventory.php" class="quick-btn">
    <div class="quick-icon" style="background:rgba(168,85,247,0.12)">📊</div>
    <div class="quick-label">Tồn kho & Thống kê</div>
  </a>
</div>

<!-- STAT CARDS -->
<div class="stat-grid">
  <div class="stat-card" style="--card-color:var(--accent)">
    <div class="stat-top">
      <div class="stat-label">Doanh thu tháng</div>
      <div class="stat-icon">💰</div>
    </div>
    <!-- <div class="stat-value">842.5M</div> -->
    <div class="stat-value"><?= number_format($starts["revenue_current_monthly"]) ?></div>
    <!-- <div class="stat-change up">▲ +12.4% so tháng trước</div> -->
    <?php
    $pct = $starts['percent_revenue_last_monthly'];
    $arrow = $pct >= 0 ? '▲' : '▼';
    $cls   = $pct >= 0 ? 'up' : 'down';
    ?>
    <div class="stat-change <?= $cls ?>">
      <?= $arrow ?> <?= ($pct >= 0 ? '+' : '') . number_format($pct, 1) ?>% so tháng trước
    </div>
    <!-- <div class="stat-change up"> <?= number_format($starts['percent_revenue_last_monthly']) ?> so tháng trước</div> -->
  </div>
  <div class="stat-card" style="--card-color:var(--blue)">
    <div class="stat-top">
      <div class="stat-label">Đơn hàng</div>
      <div class="stat-icon">🛒</div>
    </div>
    <!-- <div class="stat-value">247</div> -->
    <div class="stat-value"><?= number_format($starts['total_order_count']) ?></div>
    <div class="stat-change up">▲ <?= number_format($starts['new_order_count']) ?> đơn hôm nay</div>
  </div>
  <div class="stat-card" style="--card-color:var(--green)">
    <div class="stat-top">
      <div class="stat-label">Khách hàng</div>
      <div class="stat-icon">👤</div>
    </div>
    <div class="stat-value"><?=  number_format( $starts['total_customer_count']) ?></div>
    <div class="stat-change up">▲ <?=  number_format($starts['new_customer_current_week']) ?> tuần này</div>
  </div>
  <div class="stat-card" style="--card-color:var(--red)">
    <div class="stat-top">
      <div class="stat-label">Hàng sắp hết</div>
      <div class="stat-icon">⚠️</div>
    </div>
    <div class="stat-value"><?= number_format(    $starts['stock_is_running_out']) ?></div>
    <div class="stat-change down">▼ Cần nhập bổ sung</div>
  </div>
</div>

<!-- ORDERS + TOP PRODUCTS -->
<div class="panel-row">
  <div class="panel">
    <div class="panel-header">
      <div class="panel-title">ĐƠN HÀNG GẦN ĐÂY</div>
      <a href="orders.php" class="panel-action">Xem tất cả →</a>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>#HD</th>
            <th>Khách hàng</th>
            <th>Tổng tiền</th>
            <th>Thanh toán</th>
            <th>Trạng thái</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="td-main td-mono">#0007</td>
            <td>Nguyễn Văn A</td>
            <td class="td-accent">24.000.000đ</td>
            <td>Tiền mặt</td>
            <td><span class="badge badge-green">Hoàn thành</span></td>
          </tr>
          <tr>
            <td class="td-main td-mono">#0006</td>
            <td>Trần Thị B</td>
            <td class="td-accent">35.000.000đ</td>
            <td>Chuyển khoản</td>
            <td><span class="badge badge-amber">Đang xử lý</span></td>
          </tr>
          <tr>
            <td class="td-main td-mono">#0005</td>
            <td>Lê Minh C</td>
            <td class="td-accent">6.190.000đ</td>
            <td>Trực tuyến</td>
            <td><span class="badge badge-green">Hoàn thành</span></td>
          </tr>
          <tr>
            <td class="td-main td-mono">#0004</td>
            <td>Phạm Thị D</td>
            <td class="td-accent">19.000.000đ</td>
            <td>Chuyển khoản</td>
            <td><span class="badge badge-red">Đã huỷ</span></td>
          </tr>
          <tr>
            <td class="td-main td-mono">#0003</td>
            <td>Hoàng Văn E</td>
            <td class="td-accent">3.000.000đ</td>
            <td>Tiền mặt</td>
            <td><span class="badge badge-blue">Đang giao</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="panel">
    <div class="panel-header">
      <div class="panel-title">SẢN PHẨM NỔI BẬT</div>
      <span class="panel-action">Tuần này</span>
    </div>
    <div class="panel-body">
      <div class="product-row">
        <div class="product-rank top">1</div>
        <div class="product-img">📱</div>
        <div class="product-info">
          <div class="product-name">iPhone 16</div>
          <div class="product-cat">Điện thoại · Apple</div>
        </div>
        <div class="product-stat">
          <div class="product-price">24.000.000đ</div>
          <div class="product-sold">Đã bán: 42</div>
        </div>
      </div>
      <div class="product-row">
        <div class="product-rank top">2</div>
        <div class="product-img">📱</div>
        <div class="product-info">
          <div class="product-name">iPhone 16 Plus</div>
          <div class="product-cat">Điện thoại · Apple</div>
        </div>
        <div class="product-stat">
          <div class="product-price">35.000.000đ</div>
          <div class="product-sold">Đã bán: 28</div>
        </div>
      </div>
      <div class="product-row">
        <div class="product-rank">3</div>
        <div class="product-img">🎧</div>
        <div class="product-info">
          <div class="product-name">Airpods Pro 2</div>
          <div class="product-cat">Tai nghe · Apple</div>
        </div>
        <div class="product-stat">
          <div class="product-price">6.190.000đ</div>
          <div class="product-sold">Đã bán: 19</div>
        </div>
      </div>
      <div class="product-row">
        <div class="product-rank">4</div>
        <div class="product-img">📱</div>
        <div class="product-info">
          <div class="product-name">Galaxy Z Flip 6</div>
          <div class="product-cat">Điện thoại · Samsung</div>
        </div>
        <div class="product-stat">
          <div class="product-price">23.900.000đ</div>
          <div class="product-sold">Đã bán: 15</div>
        </div>
      </div>
      <div class="product-row">
        <div class="product-rank">5</div>
        <div class="product-img">💻</div>
        <div class="product-info">
          <div class="product-name">Tab S10 Ultra</div>
          <div class="product-cat">Tablet · Samsung</div>
        </div>
        <div class="product-stat">
          <div class="product-price">24.290.000đ</div>
          <div class="product-sold">Đã bán: 11</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- CHART + CATEGORIES -->
<div class="panel-row">
  <div class="panel">
    <div class="panel-header">
      <div class="panel-title">DOANH THU 7 NGÀY</div>
      <span style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--text3)">VNĐ (triệu)</span>
    </div>
    <div class="chart-bars">
      <div class="bar-wrap">
        <div class="bar" style="height:55%"></div>
        <div class="bar-label">T2</div>
      </div>
      <div class="bar-wrap">
        <div class="bar" style="height:72%"></div>
        <div class="bar-label">T3</div>
      </div>
      <div class="bar-wrap">
        <div class="bar" style="height:48%"></div>
        <div class="bar-label">T4</div>
      </div>
      <div class="bar-wrap">
        <div class="bar" style="height:90%"></div>
        <div class="bar-label">T5</div>
      </div>
      <div class="bar-wrap">
        <div class="bar" style="height:65%"></div>
        <div class="bar-label">T6</div>
      </div>
      <div class="bar-wrap">
        <div class="bar" style="height:100%"></div>
        <div class="bar-label">T7</div>
      </div>
      <div class="bar-wrap">
        <div class="bar" style="height:40%;opacity:.4"></div>
        <div class="bar-label">CN</div>
      </div>
    </div>
    <div style="padding:8px 20px 16px;display:flex;gap:20px">
      <div>
        <div style="font-size:10px;color:var(--text3);font-family:'JetBrains Mono',monospace">TRUNG BÌNH/NGÀY</div>
        <div style="font-family:'Bebas Neue',sans-serif;font-size:20px;color:var(--accent)">120.3M</div>
      </div>
      <div>
        <div style="font-size:10px;color:var(--text3);font-family:'JetBrains Mono',monospace">ĐỈNH NGÀY</div>
        <div style="font-family:'Bebas Neue',sans-serif;font-size:20px;color:var(--green)">198.7M</div>
      </div>
    </div>
  </div>

  <div class="panel">
    <div class="panel-header">
      <div class="panel-title">DANH MỤC</div>
      <span class="panel-action">Theo doanh thu</span>
    </div>
    <div class="cat-row">
      <div class="cat-top"><span class="cat-name">📱 Điện thoại</span><span class="cat-pct">42%</span></div>
      <div class="cat-bar-bg">
        <div class="cat-bar-fill" style="width:42%"></div>
      </div>
    </div>
    <div class="cat-row">
      <div class="cat-top"><span class="cat-name">💻 Tablet</span><span class="cat-pct">28%</span></div>
      <div class="cat-bar-bg">
        <div class="cat-bar-fill" style="width:28%"></div>
      </div>
    </div>
    <div class="cat-row">
      <div class="cat-top"><span class="cat-name">🎧 Tai nghe</span><span class="cat-pct">14%</span></div>
      <div class="cat-bar-bg">
        <div class="cat-bar-fill" style="width:14%"></div>
      </div>
    </div>
    <div class="cat-row">
      <div class="cat-top"><span class="cat-name">🔌 Củ sạc</span><span class="cat-pct">10%</span></div>
      <div class="cat-bar-bg">
        <div class="cat-bar-fill" style="width:10%"></div>
      </div>
    </div>
    <div class="cat-row">
      <div class="cat-top"><span class="cat-name">🔗 Dây sạc</span><span class="cat-pct">6%</span></div>
      <div class="cat-bar-bg">
        <div class="cat-bar-fill" style="width:6%"></div>
      </div>
    </div>
  </div>
</div>

<?php
require_once 'includes/footer.php'; ?>