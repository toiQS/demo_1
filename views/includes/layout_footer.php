<?php
/**
 * includes/layout_footer.php
 * Đóng tag </main>, </div> và include JS
 * Luôn dùng cùng với includes/layout.php
 *
 * Biến tuỳ chọn khai báo trước require layout.php:
 *   $extra_css = 'assets/css/products.css';  → CSS riêng của trang
 *   $extra_js  = 'assets/js/products.js';    → JS file riêng của trang
 *   $inline_js = '<script>...</script>';      → JS inline (dữ liệu PHP inject)
 */
?>
  </main>
  <!-- /content -->

</div>
<!-- /main-wrap -->

<!-- Shared Admin JS -->
<script src="assets\js\index.js"></script>

<!-- JS inline (PHP data → JS variables, phải đặt TRƯỚC page JS) -->
<?php if (!empty($inline_js)): ?>
  <?= $inline_js ?>
<?php endif; ?>

<!-- Page-specific JS -->
<?php if (!empty($extra_js)): ?>
  <script src="<?= htmlspecialchars($extra_js) ?>"></script>
<?php endif; ?>

</body>
</html>
