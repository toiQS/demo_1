<?php

function get_recent_orders(PDO $pdo, int $limit = 5): array {
    $sql = "
        SELECT 
            h.idHD,
            t.HOTEN,
            h.THANHTIEN,
            h.TRANGTHAI,
            h.NGAYMUA,
            pt.TENPHUONGTHUC
        FROM hoadon h
        JOIN taikhoan    t  ON t.idTK         = h.idTK
        JOIN ptthanhtoan pt ON pt.idThanhToan = h.idTHANHTOAN
        ORDER BY h.idHD DESC
        LIMIT :limit
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_badge_class(string $status): string {
    return match($status) {
        'Hoàn thành' => 'badge-green',
        'Đang xử lý' => 'badge-amber',
        'Đang giao'  => 'badge-blue',
        'Đã huỷ'     => 'badge-red',
        default      => 'badge-amber',
    };
}