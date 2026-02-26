<?php
/**
 * controllers/categories/toggle.php
 *
 * Bảng danhmuc (theo chdidong.sql) KHÔNG có cột TRANGTHAI.
 * Chức năng toggle trạng thái không khả dụng với schema hiện tại.
 *
 * Trả về thông báo rõ ràng để frontend xử lý đúng.
 */

$result = [
    'success' => false,
    'message' => 'Chức năng ẩn/hiện danh mục chưa được hỗ trợ. '
               . 'Bảng danhmuc chưa có cột TRANGTHAI.',
];
