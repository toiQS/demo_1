<?php

/**
 * Lấy doanh thu theo danh mục, tính % so với tổng
 */
function get_categories_revenue(PDO $pdo): array {
    $sql = "
        SELECT
            d.LOAISP,
            COALESCE(SUM(hd.THANHTIEN), 0) AS doanhthu
        FROM danhmuc d
        LEFT JOIN sanpham     s  ON s.idDM   = d.idDM
        LEFT JOIN chitiethoadon ct ON ct.idSP = s.idSP
        LEFT JOIN hoadon       hd ON hd.idHD  = ct.idHD
                                  AND hd.TRANGTHAI = 'Hoàn thành'
        GROUP BY d.idDM, d.LOAISP
        ORDER BY doanhthu DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    // Tính tổng để ra %
    $total = array_sum(array_column($rows, 'doanhthu'));
    if ($total == 0) $total = 1; // tránh chia 0

    return array_map(function ($row) use ($total) {
        $pct = round($row['doanhthu'] / $total * 100, 1);
        return [
            'loaisp'   => $row['LOAISP'],
            'doanhthu' => (float) $row['doanhthu'],
            'pct'      => $pct,
            'icon'     => get_category_icon($row['LOAISP']),
        ];
    }, $rows);
}

function get_category_icon(string $loaisp): string {
    return match(true) {
        str_contains($loaisp, 'Điện thoại')              => '📱',
        str_contains($loaisp, 'Tablet')
            || str_contains($loaisp, 'iPad')             => '💻',
        str_contains($loaisp, 'Tai')                     => '🎧',
        str_contains($loaisp, 'Củ sạc')
            || str_contains($loaisp, 'Adapter')          => '🔌',
        str_contains($loaisp, 'Dây')                     => '🔗',
        str_contains($loaisp, 'Đồng hồ')                 => '⌚',
        str_contains($loaisp, 'Ốp')                      => '🛡',
        default                                          => '📦',
    };
}