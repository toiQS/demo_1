<?php
// services/users/get_user_start.php
// Cung cấp hàm getUSerStats() trả về mảng thống kê người dùng

function getUserStats(PDO $pdo): array
{
    $stats = [
        'total_users'    => 0,
        'activity_users' => 0,
        'activity_lock'  => 0,
        'admin_counts'   => 0,
    ];

    try {
        $rows = $pdo->query(
            'SELECT
               COUNT(*)                                        AS total_users,
               SUM(TRANGTHAI = 1)                             AS activity_users,
               SUM(TRANGTHAI = 0)                             AS activity_lock,
               SUM(PHANLOAI  = 2 AND TRANGTHAI = 1)          AS admin_counts
             FROM taikhoan'
        )->fetch();

        $stats['total_users']    = (int)($rows['total_users']    ?? 0);
        $stats['activity_users'] = (int)($rows['activity_users'] ?? 0);
        $stats['activity_lock']  = (int)($rows['activity_lock']  ?? 0);
        $stats['admin_counts']   = (int)($rows['admin_counts']   ?? 0);
    } catch (Exception $e) {
        error_log('getUserStats error: ' . $e->getMessage());
    }

    return $stats;
}
