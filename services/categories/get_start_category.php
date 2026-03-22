<?php
// services/categories/get_start_category.php
// Hàm getCategoryStats(PDO) → array thống kê danh mục

function getCategoryStats(PDO $pdo): array
{
    $stats = [
        'total'        => 0,  // Tổng số danh mục
        'total_san_pham' => 0,  // Tổng sản phẩm đang hoạt động
        'co_hang'      => 0,  // Danh mục có ít nhất 1 SP còn hàng
        'het_hang'     => 0,  // Danh mục toàn bộ SP hết hàng
    ];

    try {
        $row = $pdo->query(
            'SELECT COUNT(*) AS total FROM danhmuc'
        )->fetch();
        $stats['total'] = (int)($row['total'] ?? 0);

        $row2 = $pdo->query(
            'SELECT COUNT(*) AS total_sp FROM sanpham WHERE TRANGTHAI = 1'
        )->fetch();
        $stats['total_san_pham'] = (int)($row2['total_sp'] ?? 0);

        // Danh mục có ít nhất 1 SP còn hàng (SOLUONG > 0)
        $row3 = $pdo->query(
            'SELECT COUNT(DISTINCT idDM) AS co_hang
             FROM sanpham
             WHERE TRANGTHAI = 1 AND SOLUONG > 0'
        )->fetch();
        $stats['co_hang'] = (int)($row3['co_hang'] ?? 0);

        $stats['het_hang'] = $stats['total'] - $stats['co_hang'];

    } catch (Exception $e) {
        error_log('getCategoryStats: ' . $e->getMessage());
    }

    return $stats;
}