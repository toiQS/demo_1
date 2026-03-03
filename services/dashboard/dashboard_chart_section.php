<?php
// Thêm vào đầu dashboard.php:
include_once __DIR__ . '/../services/dashboard/get_revenue.php';

$revenue_7days  = get_revenue_7days($pdo);
$revenue_7days  = calc_bar_heights($revenue_7days);
$revenue_stats  = get_revenue_stats($revenue_7days);
?>

<!-- CHART + CATEGORIES -->
<div class="panel-row">
  <div class="panel">
    <div class="panel-header">
      <div class="panel-title">DOANH THU 7 NGÀY</div>
      <span style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--text3)">VNĐ (triệu)</span>
    </div>
    <div class="chart-bars">
      <?php foreach ($revenue_7days as $i => $day): ?>
        <?php
          $isToday  = $day['date'] === date('Y-m-d');
          $opacity  = $isToday && $day['revenue'] == 0 ? 'opacity:.4;' : '';
          $height   = max($day['height'], 4); // tối thiểu 4% để bar luôn thấy
        ?>
        <div class="bar-wrap">
          <div class="bar" style="height:<?= $height ?>%;<?= $opacity ?>"
               title="<?= $day['label'] ?> <?= date('d/m', strtotime($day['date'])) ?>: <?= number_format($day['revenue']) ?>đ">
          </div>
          <div class="bar-label" style="<?= $isToday ? 'color:var(--accent);font-weight:700' : '' ?>">
            <?= $day['label'] ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div style="padding:8px 20px 16px;display:flex;gap:20px">
      <div>
        <div style="font-size:10px;color:var(--text3);font-family:'JetBrains Mono',monospace">TRUNG BÌNH/NGÀY</div>
        <div style="font-family:'Bebas Neue',sans-serif;font-size:20px;color:var(--accent)">
          <?= number_format($revenue_stats['avg'] / 1_000_000, 1) ?>M
        </div>
      </div>
      <div>
        <div style="font-size:10px;color:var(--text3);font-family:'JetBrains Mono',monospace">ĐỈNH NGÀY</div>
        <div style="font-family:'Bebas Neue',sans-serif;font-size:20px;color:var(--green)">
          <?= number_format($revenue_stats['max'] / 1_000_000, 1) ?>M
        </div>
      </div>
    </div>
  </div>

  <!-- panel DANH MỤC giữ nguyên bên dưới -->
</div>
