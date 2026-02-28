<?php
// views/import.php
$currentPage = 'import';
$pageTitle   = 'NHẬP HÀNG';
$breadcrumb  = 'Quản lý / Nhập hàng';
require_once 'includes/header.php';
?>

<div class="section-head">
  <div class="section-title">QUẢN LÝ NHẬP HÀNG</div>
  <a href="?action=add" class="btn-sm">+ Tạo phiếu nhập</a>
</div>

<div class="stat-grid-3">
  <div class="stat-card" style="--card-color:var(--accent)">
    <div class="stat-top"><div class="stat-label">Tổng phiếu nhập</div><div class="stat-icon">📦</div></div>
    <div class="stat-value">38</div>
  </div>
  <div class="stat-card" style="--card-color:var(--blue)">
    <div class="stat-top"><div class="stat-label">Nhà cung cấp</div><div class="stat-icon">🏭</div></div>
    <div class="stat-value">12</div>
  </div>
  <div class="stat-card" style="--card-color:var(--green)">
    <div class="stat-top"><div class="stat-label">Chi phí tháng này</div><div class="stat-icon">💳</div></div>
    <div class="stat-value">620M</div>
  </div>
</div>

<div class="panel-row">
  <div class="panel">
    <div class="panel-header">
      <div class="panel-title">PHIẾU NHẬP GẦN ĐÂY</div>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>#PN</th><th>Nhà cung cấp</th><th>Ngày nhập</th>
            <th>Lần nhập</th><th>Thành tiền</th><th>Trạng thái</th><th>Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <!-- TODO: Vòng lặp PHP từ bảng phieunhap JOIN nhacungcap -->
          <tr>
            <td class="td-main td-mono">#038</td>
            <td>Apple VN</td>
            <td class="td-muted">28/02/2026</td>
            <td class="td-mono">3</td>
            <td class="td-accent">192.000.000đ</td>
            <td><span class="badge badge-green">Đã nhập</span></td>
            <td><a href="?action=detail&id=38" class="action-link">Chi tiết</a></td>
          </tr>
          <tr>
            <td class="td-main td-mono">#037</td>
            <td>Samsung VN</td>
            <td class="td-muted">25/02/2026</td>
            <td class="td-mono">1</td>
            <td class="td-accent">95.600.000đ</td>
            <td><span class="badge badge-green">Đã nhập</span></td>
            <td><a href="?action=detail&id=37" class="action-link">Chi tiết</a></td>
          </tr>
          <tr>
            <td class="td-main td-mono">#036</td>
            <td>Xiaomi Dist.</td>
            <td class="td-muted">20/02/2026</td>
            <td class="td-mono">2</td>
            <td class="td-accent">4.760.000đ</td>
            <td><span class="badge badge-amber">Chờ duyệt</span></td>
            <td><a href="?action=detail&id=36" class="action-link">Chi tiết</a></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="panel">
    <div class="panel-header">
      <div class="panel-title">NHÀ CUNG CẤP</div>
      <a href="?action=add-supplier" class="btn-sm">+ Thêm</a>
    </div>
    <div class="panel-body">
      <!-- TODO: Vòng lặp PHP từ bảng nhacungcap -->
      <div class="alert-item">
        <div class="alert-dot" style="background:var(--green)"></div>
        <div style="flex:1">
          <div class="alert-name">Apple Vietnam</div>
          <div style="font-size:11px;color:var(--text3);font-family:'JetBrains Mono',monospace">0912000001 · Q.1, TP.HCM</div>
        </div>
        <a href="?action=supplier&id=1" class="action-link">Xem</a>
      </div>
      <div class="alert-item">
        <div class="alert-dot" style="background:var(--green)"></div>
        <div style="flex:1">
          <div class="alert-name">Samsung VN</div>
          <div style="font-size:11px;color:var(--text3);font-family:'JetBrains Mono',monospace">0912000002 · Q.3, TP.HCM</div>
        </div>
        <a href="?action=supplier&id=2" class="action-link">Xem</a>
      </div>
      <div class="alert-item">
        <div class="alert-dot" style="background:var(--accent)"></div>
        <div style="flex:1">
          <div class="alert-name">Xiaomi Distributor</div>
          <div style="font-size:11px;color:var(--text3);font-family:'JetBrains Mono',monospace">0912000003 · Q.7, TP.HCM</div>
        </div>
        <a href="?action=supplier&id=3" class="action-link">Xem</a>
      </div>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
