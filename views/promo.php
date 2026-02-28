<?php
// views/promo.php
$currentPage = 'promo';
$pageTitle   = 'KHUYẾN MÃI';
$breadcrumb  = 'Tiện ích / Khuyến mãi';
require_once 'includes/header.php';
?>

<div class="section-head">
  <div class="section-title">QUẢN LÝ KHUYẾN MÃI</div>
  <a href="?action=add" class="btn-sm">+ Tạo mã mới</a>
</div>

<div class="promo-grid">
  <!-- TODO: Vòng lặp PHP từ bảng khuyenmai -->

  <!-- TRANGTHAI = 0: Đang chạy -->
  <div class="promo-card">
    <div>
      <div class="promo-code">SALE10</div>
      <div class="promo-discount">Giảm 10% · HSD: 31/12/2026</div>
    </div>
    <div class="promo-meta">
      <div class="promo-qty">100 lượt</div>
      <div class="promo-exp">01/01/2026 – 31/12/2026</div>
    </div>
    <span class="badge badge-green" style="margin-left:8px">Đang chạy</span>
  </div>

  <div class="promo-card">
    <div>
      <div class="promo-code">SALE20</div>
      <div class="promo-discount">Giảm 20% · HSD: 30/06/2026</div>
    </div>
    <div class="promo-meta">
      <div class="promo-qty">50 lượt</div>
      <div class="promo-exp">01/02/2026 – 30/06/2026</div>
    </div>
    <span class="badge badge-green" style="margin-left:8px">Đang chạy</span>
  </div>

  <!-- TRANGTHAI = 1: Tạm dừng -->
  <div class="promo-card promo-disabled">
    <div>
      <div class="promo-code">DISCOUNT15</div>
      <div class="promo-discount">Giảm 15% · HSD: 31/07/2026</div>
    </div>
    <div class="promo-meta">
      <div class="promo-qty">30 lượt</div>
      <div class="promo-exp">01/03/2026 – 31/07/2026</div>
    </div>
    <span class="badge badge-gray" style="margin-left:8px">Tạm dừng</span>
  </div>

  <div class="promo-card promo-disabled">
    <div>
      <div class="promo-code">NEWYEAR25</div>
      <div class="promo-discount">Giảm 25% · HSD: 31/05/2026</div>
    </div>
    <div class="promo-meta">
      <div class="promo-qty">20 lượt</div>
      <div class="promo-exp">01/04/2026 – 31/05/2026</div>
    </div>
    <span class="badge badge-gray" style="margin-left:8px">Tạm dừng</span>
  </div>

  <div class="promo-card promo-disabled">
    <div>
      <div class="promo-code">SUMMER30</div>
      <div class="promo-discount">Giảm 30% · HSD: 30/09/2026</div>
    </div>
    <div class="promo-meta">
      <div class="promo-qty">10 lượt</div>
      <div class="promo-exp">01/05/2026 – 30/09/2026</div>
    </div>
    <span class="badge badge-gray" style="margin-left:8px">Tạm dừng</span>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
