<?php
// views/includes/sidebar.php
if (!isset($currentPage)) $currentPage = '';

$nav = [
  'overview' => [
    'label' => 'Tổng quan',
    'items' => [
      ['id' => 'dashboard', 'href' => 'dashboard.php', 'label' => 'Dashboard', 'badge' => null,
       'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'],
    ]
  ],
  'manage' => [
    'label' => 'Quản lý',
    'items' => [
      ['id' => 'users',    'href' => 'users.php',    'label' => 'Người dùng', 'badge' => null,
       'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>'],
      ['id' => 'products', 'href' => 'products.php', 'label' => 'Sản phẩm',  'badge' => null,
       'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>'],
      ['id' => 'category', 'href' => 'category.php', 'label' => 'Danh mục',  'badge' => null,
       'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>'],
      ['id' => 'orders',   'href' => 'orders.php',   'label' => 'Đơn hàng',  'badge' => '7',
       'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>'],
      ['id' => 'import',   'href' => 'import.php',   'label' => 'Nhập hàng', 'badge' => null,
       'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l-4-4m4 4l4-4"/>'],
    ]
  ],
  'tools' => [
    'label' => 'Tiện ích',
    'items' => [
      ['id' => 'promo',     'href' => 'promo.php',     'label' => 'Khuyến mãi',         'badge' => null,
       'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>'],
      ['id' => 'inventory', 'href' => 'inventory.php', 'label' => 'Tồn kho & Thống kê', 'badge' => null,
       'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'],
    ]
  ],
];
?>
<aside class="sidebar">
  <div class="logo">
    <div class="logo-title">CH DI ĐỘNG</div>
    <div class="logo-sub">Admin Console v2.0</div>
  </div>

  <?php foreach ($nav as $group): ?>
  <div class="nav-section">
    <div class="nav-label"><?= $group['label'] ?></div>
    <?php foreach ($group['items'] as $item): ?>
    <a href="<?= $item['href'] ?>" class="nav-item <?= $currentPage === $item['id'] ? 'active' : '' ?>">
      <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <?= $item['icon'] ?>
      </svg>
      <?= $item['label'] ?>
      <?php if ($item['badge']): ?>
        <span class="nav-badge"><?= $item['badge'] ?></span>
      <?php endif; ?>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endforeach; ?>

  <div class="sidebar-bottom">
    <div class="user-card">
      <div class="user-avatar">A</div>
      <div>
        <div class="user-name"><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></div>
        <div class="user-role">QUẢN LÝ HỆ THỐNG</div>
      </div>
    </div>
  </div>
</aside>