<?php
// views/includes/header.php
// Requires: $pageTitle (string), $breadcrumb (string)
if (!isset($pageTitle))  $pageTitle  = 'DASHBOARD';
if (!isset($breadcrumb)) $breadcrumb = 'Tổng quan / Dashboard';

// Session guard — redirect to login if not authenticated
session_start();
if (!isset($_SESSION['idTK'])) {
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?> — CH Di Động Admin</title>
  <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>

<?php require_once 'includes/sidebar.php'; ?>

<div class="main">
  <!-- TOPBAR -->
  <div class="topbar">
    <div class="topbar-title"><?= htmlspecialchars($pageTitle) ?></div>
    <div class="topbar-sep"></div>
    <div class="topbar-breadcrumb"><?= htmlspecialchars($breadcrumb) ?></div>
    <div class="topbar-right">
      <div class="search-box">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--text3);flex-shrink:0">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" placeholder="Tìm kiếm...">
      </div>
      <div class="icon-btn" title="Thông báo">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <div class="notif-dot"></div>
      </div>
      <a href="../index.php?logout=1" class="icon-btn" title="Đăng xuất">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
      </a>
    </div>
  </div>
  <!-- CONTENT -->
  <div class="content">
