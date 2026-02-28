<?php
// views/users.php
$currentPage = 'users';
$pageTitle   = 'NGƯỜI DÙNG';
$breadcrumb  = 'Quản lý / Người dùng';
require_once 'includes/header.php';
?>

<div class="section-head">
  <div class="section-title">QUẢN LÝ NGƯỜI DÙNG</div>
  <div style="display:flex;gap:8px;align-items:center">
    <div class="filter-bar">
      <select class="filter-select">
        <option>Tất cả loại</option>
        <option>Khách hàng</option>
        <option>Quản lý</option>
      </select>
      <select class="filter-select">
        <option>Trạng thái</option>
        <option>Hoạt động</option>
        <option>Đã khoá</option>
      </select>
    </div>
    <button class="btn-sm">+ Thêm người dùng</button>
  </div>
</div>

<div class="panel">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th><th>Họ tên</th><th>Username</th><th>Email</th>
          <th>SĐT</th><th>Loại</th><th>Trạng thái</th><th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <!-- TODO: Thay bằng vòng lặp PHP lấy từ DB -->
        <tr>
          <td class="td-mono">001</td>
          <td class="td-main">Nguyễn Văn An</td>
          <td class="td-mono" style="color:var(--text2)">nguyenvanan</td>
          <td>an.nguyen@email.com</td>
          <td class="td-mono">0912345678</td>
          <td><span class="badge badge-blue">Khách hàng</span></td>
          <td><span class="badge badge-green">Hoạt động</span></td>
          <td>
            <a href="?action=edit&id=1" class="action-link">Sửa</a>
            <a href="?action=lock&id=1" class="action-link del"
               onclick="return confirm('Khoá tài khoản này?')">Khoá</a>
          </td>
        </tr>
        <tr>
          <td class="td-mono">002</td>
          <td class="td-main">Trần Thị Bình</td>
          <td class="td-mono" style="color:var(--text2)">tranthibinh</td>
          <td>binh.tran@email.com</td>
          <td class="td-mono">0987654321</td>
          <td><span class="badge badge-amber">Quản lý</span></td>
          <td><span class="badge badge-green">Hoạt động</span></td>
          <td>
            <a href="?action=edit&id=2" class="action-link">Sửa</a>
            <a href="?action=lock&id=2" class="action-link del"
               onclick="return confirm('Khoá tài khoản này?')">Khoá</a>
          </td>
        </tr>
        <tr>
          <td class="td-mono">003</td>
          <td class="td-main">Lê Minh Châu</td>
          <td class="td-mono" style="color:var(--text2)">leminhchau</td>
          <td>chau.le@email.com</td>
          <td class="td-mono">0901234567</td>
          <td><span class="badge badge-blue">Khách hàng</span></td>
          <td><span class="badge badge-red">Đã khoá</span></td>
          <td>
            <a href="?action=edit&id=3" class="action-link">Sửa</a>
            <a href="?action=unlock&id=3" class="action-link success">Mở khoá</a>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
