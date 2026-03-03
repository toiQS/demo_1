<?php
// views/dashboard.php

$currentPage = 'dashboard';
$pageTitle   = 'DASHBOARD';
$breadcrumb  = 'Tổng quan / Dashboard';
require_once 'includes/header.php';

include_once __DIR__ . '/../services/dashboard/load_starts.php';
include_once __DIR__ . '/../services/dashboard/get_recent_order.php';
include_once __DIR__ . '/../services/dashboard/get_top_products.php';
include_once __DIR__ . '/../services/dashboard/get_revenue.php';

$revenue_7days = calc_bar_heights(get_revenue_7days($pdo));
$revenue_stats = get_revenue_stats($revenue_7days);s
$recent_orders = get_recent_orders($pdo, 5);
$top_products  = get_top_products($pdo, 5);
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
    <div class="stat-value"><?= number_format($starts["revenue_current_monthly"]) ?></div>
    <?php
      $pct   = $starts['percent_revenue_last_monthly'];
      $arrow = $pct >= 0 ? '▲' : '▼';
      $cls   = $pct >= 0 ? 'up' : 'down';
    ?>
    <div class="stat-change <?= $cls ?>">
      <?= $arrow ?> <?= ($pct >= 0 ? '+' : '') . number_format($pct, 1) ?>% so tháng trước
    </div>
  </div>

  <div class="stat-card" style="--card-color:var(--blue)">
    <div class="stat-top">
      <div class="stat-label">Đơn hàng</div>
      <div class="stat-icon">🛒</div>
    </div>
    <div class="stat-value"><?= number_format($starts['total_order_count']) ?></div>
    <div class="stat-change up">▲ <?= number_format($starts['new_order_count']) ?> đơn hôm nay</div>
  </div>

  <div class="stat-card" style="--card-color:var(--green)">
    <div class="stat-top">
      <div class="stat-label">Khách hàng</div>
      <div class="stat-icon">👤</div>
    </div>
    <div class="stat-value"><?= number_format($starts['total_customer_count']) ?></div>
    <div class="stat-change up">▲ <?= number_format($starts['new_customer_current_week']) ?> tuần này</div>
  </div>

  <div class="stat-card" style="--card-color:var(--red)">
    <div class="stat-top">
      <div class="stat-label">Hàng sắp hết</div>
      <div class="stat-icon">⚠️</div>
    </div>
    <div class="stat-value"><?= number_format($starts['stock_is_running_out']) ?></div>
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
          <?php if (empty($recent_orders)): ?>
            <tr>
              <td colspan="5" style="text-align:center;color:var(--text3);padding:20px">
                Chưa có đơn hàng nào
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($recent_orders as $order): ?>
              <tr>
                <td class="td-main td-mono">
                  #<?= str_pad($order['idHD'], 4, '0', STR_PAD_LEFT) ?>
                </td>
                <td><?= htmlspecialchars($order['HOTEN']) ?></td>
                <td class="td-accent">
                  <?= number_format($order['THANHTIEN']) ?>đ
                </td>
                <td><?= htmlspecialchars($order['TENPHUONGTHUC']) ?></td>
                <td>
                  <span class="badge <?= get_badge_class($order['TRANGTHAI']) ?>">
                    <?= htmlspecialchars($order['TRANGTHAI']) ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="panel">
    <div class="panel-header">
      <div class="panel-title">SẢN PHẨM NỔI BẬT</div>
      <span class="panel-action">Tổng hợp</span>
    </div>
    <div class="panel-body">
      <?php if (empty($top_products)): ?>
        <div style="text-align:center;color:var(--text3);padding:20px">
          Chưa có dữ liệu bán hàng
        </div>
      <?php else: ?>
        <?php foreach ($top_products as $i => $product): ?>
          <?php $rank = $i + 1; ?>
          <div class="product-row">
            <div class="product-rank <?= $rank <= 2 ? 'top' : '' ?>">
              <?= $rank ?>
            </div>
            <div class="product-img">
              <?= get_product_icon($product['LOAISP']) ?>
            </div>
            <div class="product-info">
              <div class="product-name">
                <?= htmlspecialchars($product['TENSP']) ?>
              </div>
              <div class="product-cat">
                <?= htmlspecialchars($product['LOAISP']) ?> · <?= htmlspecialchars($product['TENHANG']) ?>
              </div>
            </div>
            <div class="product-stat">
              <div class="product-price">
                <?= number_format($product['GIABAN']) ?>đ
              </div>
              <div class="product-sold">
                Đã bán: <?= number_format($product['tong_ban']) ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
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
      <div class="bar-wrap"><div class="bar" style="height:55%"></div><div class="bar-label">T2</div></div>
      <div class="bar-wrap"><div class="bar" style="height:72%"></div><div class="bar-label">T3</div></div>
      <div class="bar-wrap"><div class="bar" style="height:48%"></div><div class="bar-label">T4</div></div>
      <div class="bar-wrap"><div class="bar" style="height:90%"></div><div class="bar-label">T5</div></div>
      <div class="bar-wrap"><div class="bar" style="height:65%"></div><div class="bar-label">T6</div></div>
      <div class="bar-wrap"><div class="bar" style="height:100%"></div><div class="bar-label">T7</div></div>
      <div class="bar-wrap"><div class="bar" style="height:40%;opacity:.4"></div><div class="bar-label">CN</div></div>
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
      <div class="cat-bar-bg"><div class="cat-bar-fill" style="width:42%"></div></div>
    </div>
    <div class="cat-row">
      <div class="cat-top"><span class="cat-name">💻 Tablet</span><span class="cat-pct">28%</span></div>
      <div class="cat-bar-bg"><div class="cat-bar-fill" style="width:28%"></div></div>
    </div>
    <div class="cat-row">
      <div class="cat-top"><span class="cat-name">🎧 Tai nghe</span><span class="cat-pct">14%</span></div>
      <div class="cat-bar-bg"><div class="cat-bar-fill" style="width:14%"></div></div>
    </div>
    <div class="cat-row">
      <div class="cat-top"><span class="cat-name">🔌 Củ sạc</span><span class="cat-pct">10%</span></div>
      <div class="cat-bar-bg"><div class="cat-bar-fill" style="width:10%"></div></div>
    </div>
    <div class="cat-row">
      <div class="cat-top"><span class="cat-name">🔗 Dây sạc</span><span class="cat-pct">6%</span></div>
      <div class="cat-bar-bg"><div class="cat-bar-fill" style="width:6%"></div></div>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>