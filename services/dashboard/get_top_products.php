<?php

function get_top_products(PDO $pdo, int $limit = 5): array {
    $sql = "
        SELECT 
            s.idSP,
            s.TENSP,
            s.GIABAN,
            s.IMG,
            h.TENHANG,
            d.LOAISP,
            SUM(ct.SOLUONG) AS tong_ban
        FROM chitiethoadon ct
        JOIN hoadon  hd ON hd.idHD  = ct.idHD  AND hd.TRANGTHAI = 'Hoàn thành'
        JOIN sanpham  s ON s.idSP   = ct.idSP
        JOIN hang     h ON h.idHANG = s.HANG
        JOIN danhmuc  d ON d.idDM   = s.idDM
        GROUP BY s.idSP, s.TENSP, s.GIABAN, s.IMG, h.TENHANG, d.LOAISP
        ORDER BY tong_ban DESC
        LIMIT :limit
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Map danh mục → emoji icon
function get_product_icon(string $loaisp): string {
    return match(true) {
        str_contains($loaisp, 'Điện thoại') => '📱',
        str_contains($loaisp, 'Tablet')
            || str_contains($loaisp, 'iPad')  => '💻',
        str_contains($loaisp, 'Tai')          => '🎧',
        str_contains($loaisp, 'Củ sạc')
            || str_contains($loaisp, 'Adapter')=> '🔌',
        str_contains($loaisp, 'Dây')          => '🔗',
        str_contains($loaisp, 'Đồng hồ')      => '⌚',
        str_contains($loaisp, 'Ốp')           => '📦',
        default                               => '📦',
    };
}