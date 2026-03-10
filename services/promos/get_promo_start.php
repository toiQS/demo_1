<?php
// services/promos/get_promo_start.php
// Hàm getPromoStats(PDO) → array thống kê khuyến mãi

function getPromoStats(PDO $pdo): array
{
    $stats = [
        'total'    => 0,  // tổng số mã
        'running'  => 0,  // TRANGTHAI = 0 (đang chạy)
        'paused'   => 0,  // TRANGTHAI = 1 (tạm dừng)
        'expired'  => 0,  // HANSUDUNG < TODAY (đã hết hạn)
    ];

    try {
        $today = date('Y-m-d');
        $row   = $pdo->prepare(
            'SELECT
               COUNT(*)                                            AS total,
               SUM(TRANGTHAI = 0 AND HANSUDUNG >= :d1)            AS running,
               SUM(TRANGTHAI = 1)                                  AS paused,
               SUM(HANSUDUNG < :d2)                               AS expired
             FROM khuyenmai'
        );
        $row->execute([':d1' => $today, ':d2' => $today]);
        $data = $row->fetch();

        $stats['total']   = (int)($data['total']   ?? 0);
        $stats['running'] = (int)($data['running'] ?? 0);
        $stats['paused']  = (int)($data['paused']  ?? 0);
        $stats['expired'] = (int)($data['expired'] ?? 0);
    } catch (Exception $e) {
        error_log('getPromoStats: ' . $e->getMessage());
    }

    return $stats;
}