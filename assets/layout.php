<?php
/**
 * includes/layout.php
 * Include file dùng chung — Header HTML, Sidebar, Topbar
 *
 * Cách dùng ở bất kỳ trang nào:
 * -----------------------------------------------
 * <?php
 *   $page_title    = 'Quản lý Sản phẩm';   // Tiêu đề trang
 *   $page_subtitle = 'Danh sách sản phẩm'; // Mô tả nhỏ bên dưới
 *   $active_nav    = 'products';             // Key của menu đang active
 *   require_once 'includes/layout.php';
 * ?>
 *   ... nội dung trang ...
 * <?php require_once 'includes/layout_footer.php'; ?>
 * -----------------------------------------------
 */

// Giá trị mặc định nếu trang chưa khai báo
$page_title    = $page_title    ?? 'Admin Panel';
$page_subtitle = $page_subtitle ?? '';
$active_nav    = $active_nav    ?? '';

// Thông tin admin từ session
session_start();
// if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
$admin_name = $_SESSION['admin']['name'] ?? 'Quản Trị Viên';
$admin_role = $_SESSION['admin']['role'] ?? 'Super Admin';

// Badge counts (thay bằng query DB thực tế)
$pending_orders = $pending_orders ?? 34;
$low_stock_count = $low_stock_count ?? 8;

// Cấu hình menu sidebar
$nav_items = [
  'overview' => [
    'title' => 'Tổng quan',
    'items' => [
      'home' => ['label' => 'Dashboard', 'icon' => 'fa-gauge-high', 'href' => 'admin_home.php'],
    ]
  ],
  'manage' => [
    'title' => 'Quản lý',
    'items' => [
      'products'  => ['label' => 'Sản phẩm',    'icon' => 'fa-box-open',        'href' => 'products.php'],
      'categories'=> ['label' => 'Danh mục',    'icon' => 'fa-tags',            'href' => 'categories.php'],
      'orders'    => ['label' => 'Đơn hàng',    'icon' => 'fa-receipt',         'href' => 'orders.php',    'badge' => $pending_orders,  'badge_class' => ''],
      'import'    => ['label' => 'Nhập hàng',   'icon' => 'fa-truck-ramp-box',  'href' => 'import.php'],
      'pricing'   => ['label' => 'Giá bán',     'icon' => 'fa-tag',             'href' => 'pricing.php'],
      'inventory' => ['label' => 'Tồn kho',     'icon' => 'fa-warehouse',       'href' => 'inventory.php', 'badge' => $low_stock_count, 'badge_class' => 'amber'],
      'users'     => ['label' => 'Người dùng',  'icon' => 'fa-users',           'href' => 'users.php'],
    ]
  ],
  'reports' => [
    'title' => 'Báo cáo',
    'items' => [
      'report_revenue' => ['label' => 'Doanh thu',   'icon' => 'fa-chart-line',   'href' => 'report_revenue.php'],
      'report_import'  => ['label' => 'Nhập - Xuất', 'icon' => 'fa-file-invoice', 'href' => 'report_import.php'],
    ]
  ],
  'system' => [
    'title' => 'Hệ thống',
    'items' => [
      'settings' => ['label' => 'Cài đặt', 'icon' => 'fa-gear', 'href' => 'settings.php'],
    ]
  ],
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?> — ShopAdmin</title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  
  <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Shared style CSS -->
  <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

<!-- ===================== SIDEBAR ===================== -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-icon"><i class="fa-solid fa-store"></i></div>
    <div class="logo-text">
      Shop<span>Admin</span><br>
      <small style="font-size:10px;font-weight:400;color:var(--text-muted)">Bảng điều khiển</small>
    </div>
  </div>

  <nav class="sidebar-nav">
    <?php foreach ($nav_items as $section_key => $section): ?>
    <div class="nav-section">
      <div class="nav-section-title"><?= $section['title'] ?></div>
      <?php foreach ($section['items'] as $key => $item): ?>
      <a href="<?= $item['href'] ?>" class="nav-item <?= ($active_nav === $key) ? 'active' : '' ?>">
        <i class="fa-solid <?= $item['icon'] ?>"></i>
        <?= $item['label'] ?>
        <?php if (!empty($item['badge']) && $item['badge'] > 0): ?>
          <span class="nav-badge <?= $item['badge_class'] ?? '' ?>"><?= $item['badge'] ?></span>
        <?php endif; ?>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
  </nav>

  <div class="sidebar-footer">
    <div class="admin-avatar"><?= strtoupper(mb_substr($admin_name, 0, 1, 'UTF-8')) ?></div>
    <div class="admin-info">
      <div class="name"><?= htmlspecialchars($admin_name) ?></div>
      <div class="role"><?= htmlspecialchars($admin_role) ?></div>
    </div>
    <a href="logout.php" class="btn-logout" title="Đăng xuất">
      <i class="fa-solid fa-arrow-right-from-bracket"></i>
    </a>
  </div>
</aside>

<!-- ===================== MAIN WRAP ===================== -->
<div class="main-wrap">

  <!-- Topbar -->
  <header class="topbar">
    <div class="topbar-title">
      <h1><?= htmlspecialchars($page_title) ?></h1>
      <?php if ($page_subtitle): ?>
        <p><?= htmlspecialchars($page_subtitle) ?></p>
      <?php endif; ?>
    </div>
    <div class="topbar-actions">
      <div class="topbar-date" id="clock">--:-- | --/--/----</div>
      <button class="btn-icon" title="Thông báo">
        <i class="fa-solid fa-bell"></i>
        <span class="notif-dot"></span>
      </button>
      <button class="btn-icon" title="Làm mới" onclick="location.reload()">
        <i class="fa-solid fa-rotate-right"></i>
      </button>
    </div>
  </header>

  <!-- Content bắt đầu từ đây -->
  <main class="content">
