<?php
// views/products.php
$currentPage = 'products';
$pageTitle   = 'SẢN PHẨM';
$breadcrumb  = 'Quản lý / Sản phẩm';
require_once 'includes/header.php';
?>

<div class="section-head">
  <div class="section-title">QUẢN LÝ SẢN PHẨM</div>
  <div style="display:flex;gap:8px;align-items:center">
    <select class="filter-select">
      <option>Tất cả danh mục</option>
      <option>Điện thoại</option><option>Tai nghe</option>
      <option>Tablet</option><option>Củ sạc</option><option>Dây sạc</option>
    </select>
    <select class="filter-select">
      <option>Tất cả hãng</option>
      <option>Apple</option><option>Samsung</option><option>Xiaomi</option>
    </select>
    <a href="?action=add" class="btn-sm">+ Thêm sản phẩm</a>
  </div>
</div>

<div class="panel">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th><th>Tên sản phẩm</th><th>Hãng</th><th>Danh mục</th>
          <th>Giá nhập</th><th>Giá bán</th><th>Tồn kho</th><th>Giảm giá</th><th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <!-- TODO: Thay bằng vòng lặp PHP lấy từ DB (bảng sanpham JOIN hang JOIN danhmuc) -->
        <tr>
          <td class="td-mono">001</td>
          <td class="td-main">iPhone 16</td>
          <td>Apple</td>
          <td><span class="badge badge-blue">Điện thoại</span></td>
          <td class="td-mono">19.200.000đ</td>
          <td class="td-accent">24.000.000đ</td>
          <td><span style="color:var(--red);font-family:'JetBrains Mono',monospace">0</span></td>
          <td><span class="badge badge-amber">10%</span></td>
          <td>
            <a href="?action=edit&id=1" class="action-link">Sửa</a>
            <a href="?action=hide&id=1" class="action-link del"
               onclick="return confirm('Ẩn sản phẩm này?')">Ẩn</a>
          </td>
        </tr>
        <tr>
          <td class="td-mono">005</td>
          <td class="td-main">Airpods Pro 2</td>
          <td>Apple</td>
          <td><span class="badge badge-purple">Tai nghe</span></td>
          <td class="td-mono">4.952.000đ</td>
          <td class="td-accent">6.190.000đ</td>
          <td><span style="color:var(--green);font-family:'JetBrains Mono',monospace">1</span></td>
          <td><span class="badge badge-amber">10%</span></td>
          <td>
            <a href="?action=edit&id=5" class="action-link">Sửa</a>
            <a href="?action=hide&id=5" class="action-link del"
               onclick="return confirm('Ẩn sản phẩm này?')">Ẩn</a>
          </td>
        </tr>
        <tr>
          <td class="td-mono">013</td>
          <td class="td-main">Samsung Galaxy Tab S10 Ultra</td>
          <td>Samsung</td>
          <td><span class="badge badge-green">Tablet</span></td>
          <td class="td-mono">19.432.000đ</td>
          <td class="td-accent">24.290.000đ</td>
          <td><span style="color:var(--red);font-family:'JetBrains Mono',monospace">0</span></td>
          <td><span class="badge badge-gray">0%</span></td>
          <td>
            <a href="?action=edit&id=13" class="action-link">Sửa</a>
            <a href="?action=hide&id=13" class="action-link del"
               onclick="return confirm('Ẩn sản phẩm này?')">Ẩn</a>
          </td>
        </tr>
        <tr>
          <td class="td-mono">009</td>
          <td class="td-main">Củ sạc Xiaomi 20W</td>
          <td>Xiaomi</td>
          <td><span class="badge badge-amber">Củ sạc</span></td>
          <td class="td-mono">119.200đ</td>
          <td class="td-accent">149.000đ</td>
          <td><span style="color:var(--red);font-family:'JetBrains Mono',monospace">0</span></td>
          <td><span class="badge badge-amber">10%</span></td>
          <td>
            <a href="?action=edit&id=9" class="action-link">Sửa</a>
            <a href="?action=hide&id=9" class="action-link del"
               onclick="return confirm('Ẩn sản phẩm này?')">Ẩn</a>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
