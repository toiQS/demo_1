<?php
// services/inventories/get_inventority_start.php
// Hàm getInventoryStats(PDO) → array thống kê tồn kho
// Ngưỡng mặc định = 5 nếu sản phẩm chưa cấu hình cảnh báo

function getInventoryStats(PDO $pdo): array
{
    $stats = [
        'total_sku'  => 0,  // Tổng sản phẩm đang hoạt động
        'out_stock'  => 0,  // SOLUONG = 0
        'low_stock'  => 0,  // 0 < SOLUONG <= NGUONG_DAT
        'in_stock'   => 0,  // SOLUONG > NGUONG_DAT
    ];

    try {
        $row = $pdo->query(
            'SELECT
               COUNT(*)                                                          AS total_sku,
               SUM(sp.SOLUONG = 0)                                              AS out_stock,
               SUM(sp.SOLUONG > 0
                   AND sp.SOLUONG <= COALESCE(cb.NGUONG_DAT, 5))                AS low_stock,
               SUM(sp.SOLUONG > COALESCE(cb.NGUONG_DAT, 5))                    AS in_stock
             FROM sanpham sp
             LEFT JOIN cauhinh_canhbao cb ON cb.idSP = sp.idSP
             WHERE sp.TRANGTHAI = 1'
        )->fetch();

        $stats['total_sku'] = (int)($row['total_sku'] ?? 0);
        $stats['out_stock'] = (int)($row['out_stock'] ?? 0);
        $stats['low_stock'] = (int)($row['low_stock'] ?? 0);
        $stats['in_stock']  = (int)($row['in_stock']  ?? 0);
    } catch (Exception $e) {
        error_log('getInventoryStats: ' . $e->getMessage());
    }

    return $stats;
}