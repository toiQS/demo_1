<?php
// views/inventory.php
$currentPage = 'inventory';
$pageTitle   = 'TỒN KHO & THỐNG KÊ';
$breadcrumb  = 'Tiện ích / Tồn kho & Thống kê';
require_once 'includes/header.php';
?>

<div class="section-head">
  <div class="section-title">TỒN KHO & THỐNG KÊ</div>
  <button class="btn-outline">📥 Xuất Excel</button>
</div>

<div class="stat-grid">
  <div class="stat-card" style="--card-color:var(--accent)">
    <div class="stat-top"><div class="stat-label">Tổng SKU</div><div class="stat-icon">🗂</div></div>
    <div class="stat-value">26</div>
    <div class="stat-change up">▲ Đang hoạt động</div>
  </div>
  <div class="stat-card" style="--card-color:var(--red)">
    <div class="stat-top"><div class="stat-label">Hết hàng</div><div class="stat-icon">❗</div></div>
    <div class="stat-value">24</div>
    <div class="stat-change down">▼ Cần nhập ngay</div>
  </div>
  <div class="stat-card" style="--card-color:var(--accent)">
    <div class="stat-top"><div class="stat-label">Sắp hết</div><div class="stat-icon">⚠️</div></div>
    <div class="stat-value">2</div>
    <div class="stat-change">Dưới ngưỡng cảnh báo</div>
  </div>
  <div class="stat-card" style="--card-color:var(--green)">
    <div class="stat-top"><div class="stat-label">Còn hàng</div><div class="stat-icon">✅</div></div>
    <div class="stat-value">2</div>
    <div class="stat-change up">▲ Đủ cung ứng</div>
  </div>
</div>

<div class="panel-row">
  <div class="panel">
    <div class="panel-header">
      <div class="panel-title">CHI TIẾT TỒN KHO</div>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Sản phẩm</th><th>Danh mục</th><th>Tồn kho</th>
            <th>Ngưỡng cảnh báo</th><th>Tình trạng</th><th>Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <!-- TODO: Vòng lặp PHP từ bảng sanpham JOIN cauhinh_canhbao JOIN danhmuc -->
          <tr>
            <td class="td-main">Airpods Pro 2</td>
            <td>Tai nghe</td>
            <td style="color:var(--green);font-family:'JetBrains Mono',monospace">1</td>
            <td style="color:var(--text3);font-family:'JetBrains Mono',monospace">5</td>
            <td><span class="badge badge-amber">Sắp hết</span></td>
            <td><a href="import.php?action=add&sp=5" class="action-link">Nhập thêm</a></td>
          </tr>
          <tr>
            <td class="td-main">iPhone 13</td>
            <td>Điện thoại</td>
            <td style="color:var(--green);font-family:'JetBrains Mono',monospace">1</td>
            <td style="color:var(--text3);font-family:'JetBrains Mono',monospace">5</td>
            <td><span class="badge badge-amber">Sắp hết</span></td>
            <td><a href="import.php?action=add&sp=7" class="action-link">Nhập thêm</a></td>
          </tr>
          <tr>
            <td class="td-main">iPhone 16</td>
            <td>Điện thoại</td>
            <td style="color:var(--red);font-family:'JetBrains Mono',monospace">0</td>
            <td style="color:var(--text3);font-family:'JetBrains Mono',monospace">5</td>
            <td><span class="badge badge-red">Hết hàng</span></td>
            <td><a href="import.php?action=add&sp=1" class="action-link">Nhập thêm</a></td>
          </tr>
          <tr>
            <td class="td-main">Samsung Galaxy S21</td>
            <td>Điện thoại</td>
            <td style="color:var(--red);font-family:'JetBrains Mono',monospace">0</td>
            <td style="color:var(--text3);font-family:'JetBrains Mono',monospace">5</td>
            <td><span class="badge badge-red">Hết hàng</span></td>
            <td><a href="import.php?action=add&sp=6" class="action-link">Nhập thêm</a></td>
          </tr>
          <tr>
            <td class="td-main">Củ sạc Xiaomi</td>
            <td>Củ sạc</td>
            <td style="color:var(--red);font-family:'JetBrains Mono',monospace">0</td>
            <td style="color:var(--text3);font-family:'JetBrains Mono',monospace">10</td>
            <td><span class="badge badge-red">Hết hàng</span></td>
            <td><a href="import.php?action=add&sp=9" class="action-link">Nhập thêm</a></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="panel">
    <div class="panel-header"><div class="panel-title">CẢNH BÁO TỒN KHO</div></div>
    <div class="panel-body">
      <!-- TODO: Vòng lặp PHP — sanpham WHERE SOLUONG <= NGUONG_DAT -->
      <div class="alert-item">
        <div class="alert-dot" style="background:var(--red)"></div>
        <div class="alert-name">iPhone 16</div>
        <div class="alert-stock" style="color:var(--red)">0 / 5</div>
      </div>
      <div class="alert-item">
        <div class="alert-dot" style="background:var(--red)"></div>
        <div class="alert-name">iPhone 16 Plus</div>
        <div class="alert-stock" style="color:var(--red)">0 / 5</div>
      </div>
      <div class="alert-item">
        <div class="alert-dot" style="background:var(--red)"></div>
        <div class="alert-name">Samsung Galaxy S21</div>
        <div class="alert-stock" style="color:var(--red)">0 / 5</div>
      </div>
      <div class="alert-item">
        <div class="alert-dot" style="background:var(--accent)"></div>
        <div class="alert-name">Airpods Pro 2</div>
        <div class="alert-stock" style="color:var(--accent)">1 / 5</div>
      </div>
      <div class="alert-item">
        <div class="alert-dot" style="background:var(--accent)"></div>
        <div class="alert-name">iPhone 13</div>
        <div class="alert-stock" style="color:var(--accent)">1 / 5</div>
      </div>
      <div class="alert-item">
        <div class="alert-dot" style="background:var(--red)"></div>
        <div class="alert-name">Sony 1000XM4</div>
        <div class="alert-stock" style="color:var(--red)">0 / 5</div>
      </div>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
