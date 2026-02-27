<?php
$page_title    = 'Quản lý Người dùng';
$page_subtitle = 'Thêm tài khoản, khởi tạo mật khẩu, khoá tài khoản - Hệ thống cửa hàng điện tử';
$active_nav    = 'users';
$pending_orders = 27; $low_stock_count = 5;

$users = [
  ['id'=>1,'name'=>'Nguyễn Văn An',    'email'=>'an.nguyen@email.com',   'phone'=>'0901234567','role'=>'admin',   'status'=>'active','joined'=>'01/01/2025','orders'=>0, 'avatar'=>'A'],
  ['id'=>2,'name'=>'Trần Thị Bình',    'email'=>'binh.tran@email.com',   'phone'=>'0912345678','role'=>'staff',   'status'=>'active','joined'=>'15/01/2025','orders'=>0, 'avatar'=>'B'],
  ['id'=>3,'name'=>'Lê Minh Cường',    'email'=>'cuong.le@email.com',    'phone'=>'0923456789','role'=>'customer','status'=>'active','joined'=>'20/01/2025','orders'=>7, 'avatar'=>'C'],
  ['id'=>4,'name'=>'Phạm Thị Diệu',   'email'=>'dieu.pham@email.com',   'phone'=>'0934567890','role'=>'customer','status'=>'active','joined'=>'01/02/2025','orders'=>3, 'avatar'=>'D'],
  ['id'=>5,'name'=>'Hoàng Quốc Dũng', 'email'=>'dung.hoang@email.com',  'phone'=>'0945678901','role'=>'customer','status'=>'locked','joined'=>'10/02/2025','orders'=>1, 'avatar'=>'E'],
  ['id'=>6,'name'=>'Vũ Thị Hương',    'email'=>'huong.vu@email.com',    'phone'=>'0956789012','role'=>'customer','status'=>'active','joined'=>'15/02/2025','orders'=>5, 'avatar'=>'F'],
  ['id'=>7,'name'=>'Đặng Thanh Tuấn', 'email'=>'tuan.dang@email.com',   'phone'=>'0967890123','role'=>'staff',   'status'=>'active','joined'=>'20/02/2025','orders'=>0, 'avatar'=>'G'],
];

$role_map = [
  'admin'    => ['label'=>'Quản trị viên','color'=>'var(--accent)','bg'=>'rgba(240,165,0,.15)'],
  'staff'    => ['label'=>'Nhân viên','color'=>'var(--blue)','bg'=>'rgba(88,166,255,.15)'],
  'customer' => ['label'=>'Khách hàng','color'=>'var(--green)','bg'=>'rgba(63,185,80,.15)'],
];
require_once 'includes/layout.php';
?>
<link rel="stylesheet" href="../css/users.css">

<!-- Stats -->
<div class="stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px">
  <div class="stat-card">
    <div class="stat-icon si-purple"><i class="fa-solid fa-users"></i></div>
    <div class="stat-info">
      <div class="stat-label">Tổng người dùng</div>
      <div class="stat-value"><?=count($users)?></div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon si-amber"><i class="fa-solid fa-user-shield"></i></div>
    <div class="stat-info">
      <div class="stat-label">Admin & Nhân viên</div>
      <div class="stat-value"><?=count(array_filter($users,fn($u)=>in_array($u['role'],['admin','staff'])))?></div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon si-green"><i class="fa-solid fa-user-check"></i></div>
    <div class="stat-info">
      <div class="stat-label">Đang hoạt động</div>
      <div class="stat-value" style="color:var(--green)"><?=count(array_filter($users,fn($u)=>$u['status']==='active'))?></div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon si-red"><i class="fa-solid fa-user-lock"></i></div>
    <div class="stat-info">
      <div class="stat-label">Đã khoá</div>
      <div class="stat-value" style="color:var(--red)"><?=count(array_filter($users,fn($u)=>$u['status']==='locked'))?></div>
    </div>
  </div>
</div>

<div class="page-actions">
  <div class="search-box">
    <i class="fa-solid fa-magnifying-glass"></i>
    <input type="text" class="form-control" id="searchInput" placeholder="Tìm tên, email, SĐT...">
  </div>
  <select class="form-control" style="width:150px" id="filterRole">
    <option value="">Tất cả vai trò</option>
    <option value="admin">Quản trị viên</option>
    <option value="staff">Nhân viên</option>
    <option value="customer">Khách hàng</option>
  </select>
  <select class="form-control" style="width:140px" id="filterStatus">
    <option value="">Tất cả trạng thái</option>
    <option value="active">Hoạt động</option>
    <option value="locked">Đã khoá</option>
  </select>
  <button class="btn btn-primary" onclick="openAddModal()"><i class="fa-solid fa-user-plus"></i> Thêm tài khoản</button>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title"><i class="fa-solid fa-users"></i> Danh sách tài khoản</div>
    <span style="font-size:12px;color:var(--text-muted)"><?=count($users)?> tài khoản</span>
  </div>
  <div style="overflow-x:auto">
    <table id="usersTable">
      <thead><tr><th>Người dùng</th><th>Liên hệ</th><th>Vai trò</th><th>Đơn hàng</th><th>Ngày tham gia</th><th>Trạng thái</th><th>Thao tác</th></tr></thead>
      <tbody>
        <?php foreach($users as $u): $r=$role_map[$u['role']]; ?>
        <tr data-role="<?=$u['role']?>" data-status="<?=$u['status']?>">
          <td>
            <div class="user-cell">
              <div class="user-avatar" style="background:<?=$u['role']==='admin'?'linear-gradient(135deg,var(--accent),#e05c00)':($u['role']==='staff'?'linear-gradient(135deg,var(--blue),#0055cc)':'linear-gradient(135deg,var(--purple),#6600cc)')?>"><?=$u['avatar']?></div>
              <div>
                <div style="font-weight:600;font-size:13px"><?=htmlspecialchars($u['name'])?></div>
                <div style="font-size:11px;color:var(--text-muted)">ID #<?=$u['id']?></div>
              </div>
            </div>
          </td>
          <td>
            <div style="font-size:13px"><?=htmlspecialchars($u['email'])?></div>
            <div style="font-size:11px;color:var(--text-muted)"><?=$u['phone']?></div>
          </td>
          <td><span class="role-badge" style="color:<?=$r['color']?>;background:<?=$r['bg']?>"><?=$r['label']?></span></td>
          <td style="text-align:center;font-family:var(--mono);color:var(--blue)"><?=$u['orders']?></td>
          <td class="order-date"><?=$u['joined']?></td>
          <td><?=$u['status']==='active'?'<span class="status-active">Hoạt động</span>':'<span class="status-locked"><i class="fa-solid fa-lock" style="margin-right:3px"></i>Đã khoá</span>'?></td>
          <td>
            <div class="action-btns">
              <button class="btn btn-secondary btn-sm" onclick='openEditModal(<?=json_encode($u)?>)' title="Sửa"><i class="fa-solid fa-pen"></i></button>
              <button class="btn btn-secondary btn-sm" onclick='openResetModal(<?=json_encode($u)?>)' title="Đặt lại mật khẩu"><i class="fa-solid fa-key"></i></button>
              <?php if($u['status']==='active'): ?>
              <button class="btn btn-danger btn-sm" onclick="lockUser(<?=$u['id']?>, '<?=addslashes($u['name'])?>')" title="Khoá tài khoản"><i class="fa-solid fa-lock"></i></button>
              <?php else: ?>
              <button class="btn btn-primary btn-sm" onclick="unlockUser(<?=$u['id']?>, '<?=addslashes($u['name'])?>')" title="Mở khoá"><i class="fa-solid fa-lock-open"></i></button>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Thêm/Sửa tài khoản -->
<div class="modal-overlay" id="userModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="userModalTitle">Thêm tài khoản</div>
      <button class="btn-close-modal" onclick="closeModal('userModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="uId">
      <div class="form-row">
        <div class="form-group"><label class="form-label">Họ và tên *</label><input type="text" class="form-control" id="uName" placeholder="Nhập họ tên"></div>
        <div class="form-group"><label class="form-label">Vai trò</label>
          <select class="form-control" id="uRole">
            <option value="customer">Khách hàng</option>
            <option value="staff">Nhân viên</option>
            <option value="admin">Quản trị viên</option>
          </select>
        </div>
      </div>
      <div class="form-group"><label class="form-label">Email *</label><input type="email" class="form-control" id="uEmail" placeholder="email@example.com"></div>
      <div class="form-group"><label class="form-label">Số điện thoại</label><input type="text" class="form-control" id="uPhone" placeholder="0900 000 000"></div>
      <div class="form-group" id="passGroup">
        <label class="form-label">Mật khẩu *</label>
        <div class="pass-box">
          <input type="password" class="form-control" id="uPass" placeholder="Tối thiểu 8 ký tự">
          <button class="pass-toggle" onclick="togglePass('uPass',this)" type="button"><i class="fa-solid fa-eye"></i></button>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('userModal')">Huỷ</button>
      <button class="btn btn-primary" onclick="saveUser()"><i class="fa-solid fa-floppy-disk"></i> Lưu</button>
    </div>
  </div>
</div>

<!-- Modal Đặt lại mật khẩu -->
<div class="modal-overlay" id="resetModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Đặt lại mật khẩu</div>
      <button class="btn-close-modal" onclick="closeModal('resetModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <div style="display:flex;align-items:center;gap:12px;padding:14px;background:var(--accent-dim);border:1px solid rgba(240,165,0,.2);border-radius:var(--radius-sm);margin-bottom:16px">
        <i class="fa-solid fa-triangle-exclamation" style="color:var(--accent);font-size:20px"></i>
        <div>
          <div style="font-size:13px;font-weight:600" id="resetUserName">Người dùng</div>
          <div style="font-size:12px;color:var(--text-muted)">Mật khẩu mới sẽ được tạo tự động</div>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Mật khẩu mới (tuỳ chọn)</label>
        <div class="pass-box">
          <input type="text" class="form-control" id="newPass" placeholder="Để trống để tạo tự động">
          <button class="pass-toggle" onclick="generatePass()" type="button" title="Tạo tự động"><i class="fa-solid fa-wand-magic-sparkles"></i></button>
        </div>
      </div>
      <div class="reset-pass-result" id="resetResult">
        <div style="font-size:12px;color:var(--text-muted);text-align:center">Mật khẩu mới được tạo:</div>
        <div class="new-pass" id="generatedPass">—</div>
        <div style="font-size:11px;color:var(--text-muted);text-align:center">Hãy ghi lại và gửi cho người dùng</div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('resetModal')">Huỷ</button>
      <button class="btn btn-primary" onclick="doReset()"><i class="fa-solid fa-key"></i> Đặt lại mật khẩu</button>
    </div>
  </div>
</div>

<script src="../js/users.js"></script>
<?php require_once 'includes/layout_footer.php'; ?>