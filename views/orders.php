<?php
// views/orders.php
$currentPage = 'orders';
$pageTitle   = 'ĐƠN HÀNG';
$breadcrumb  = 'Quản lý / Đơn hàng';
require_once 'includes/header.php';
?>

<div class="section-head">
  <div class="section-title">QUẢN LÝ ĐƠN HÀNG</div>
  <div class="filter-bar">
    <select class="filter-select">
      <option>Tất cả trạng thái</option>
      <option>Đang xử lý</option><option>Đang giao</option>
      <option>Hoàn thành</option><option>Đã huỷ</option>
    </select>
    <select class="filter-select">
      <option>Thanh toán</option>
      <option>Tiền mặt</option><option>Chuyển khoản</option><option>Trực tuyến</option>
    </select>
    <button class="btn-outline">📅 Lọc ngày</button>
  </div>
</div>

<!-- STATUS OVERVIEW -->
<div class="stat-grid">
  <div class="stat-card" style="--card-color:var(--accent)">
    <div class="stat-top"><div class="stat-label">Đang xử lý</div><div class="stat-icon">⏳</div></div>
    <div class="stat-value">7</div>
  </div>
  <div class="stat-card" style="--card-color:var(--blue)">
    <div class="stat-top"><div class="stat-label">Đang giao</div><div class="stat-icon">🚚</div></div>
    <div class="stat-value">12</div>
  </div>
  <div class="stat-card" style="--card-color:var(--green)">
    <div class="stat-top"><div class="stat-label">Hoàn thành</div><div class="stat-icon">✅</div></div>
    <div class="stat-value">228</div>
  </div>
  <div class="stat-card" style="--card-color:var(--red)">
    <div class="stat-top"><div class="stat-label">Đã huỷ</div><div class="stat-icon">❌</div></div>
    <div class="stat-value">15</div>
  </div>
</div>

<div class="panel">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#HD</th><th>Khách hàng</th><th>Ngày mua</th><th>Sản phẩm</th>
          <th>Tổng tiền</th><th>Khuyến mãi</th><th>Thanh toán</th><th>Trạng thái</th><th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <!-- TODO: Thay bằng vòng lặp PHP lấy từ DB (bảng hoadon JOIN taikhoan JOIN ptthanhtoan) -->
        <tr>
          <td class="td-main td-mono">#0007</td>
          <td>Nguyễn Văn A</td>
          <td class="td-muted">28/02/2026</td>
          <td>iPhone 16 × 1</td>
          <td class="td-accent">24.000.000đ</td>
          <td class="td-muted">—</td>
          <td>Tiền mặt</td>
          <td><span class="badge badge-green">Hoàn thành</span></td>
          <td><a href="?action=detail&id=7" class="action-link">Chi tiết</a></td>
        </tr>
        <tr>
          <td class="td-main td-mono">#0006</td>
          <td>Trần Thị B</td>
          <td class="td-muted">27/02/2026</td>
          <td>iPhone 16 Plus × 1</td>
          <td class="td-accent">35.000.000đ</td>
          <td style="color:var(--green);font-family:'JetBrains Mono',monospace;font-size:12px">SALE10</td>
          <td>Chuyển khoản</td>
          <td><span class="badge badge-amber">Đang xử lý</span></td>
          <td><a href="?action=detail&id=6" class="action-link">Chi tiết</a></td>
        </tr>
        <tr>
          <td class="td-main td-mono">#0005</td>
          <td>Lê Minh C</td>
          <td class="td-muted">26/02/2026</td>
          <td>Airpods Pro 2 × 1</td>
          <td class="td-accent">6.190.000đ</td>
          <td class="td-muted">—</td>
          <td>Trực tuyến</td>
          <td><span class="badge badge-green">Hoàn thành</span></td>
          <td><a href="?action=detail&id=5" class="action-link">Chi tiết</a></td>
        </tr>
        <tr>
          <td class="td-main td-mono">#0004</td>
          <td>Phạm Thị D</td>
          <td class="td-muted">25/02/2026</td>
          <td>iPhone 13 × 1</td>
          <td class="td-accent">19.000.000đ</td>
          <td class="td-muted">—</td>
          <td>Chuyển khoản</td>
          <td><span class="badge badge-red">Đã huỷ</span></td>
          <td><a href="?action=detail&id=4" class="action-link">Chi tiết</a></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
