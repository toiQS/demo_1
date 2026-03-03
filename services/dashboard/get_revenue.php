<?php

/**
 * Lấy doanh thu 7 ngày gần nhất
 * Trả về mảng 7 phần tử, mỗi phần tử gồm: ngay, thu, nhan (T2-CN)
 */
function get_revenue_7days(PDO $pdo): array {
    $sql = "
        SELECT 
            DATE(NGAYMUA)          AS ngay,
            COALESCE(SUM(THANHTIEN), 0) AS doanhthu
        FROM hoadon
        WHERE TRANGTHAI = 'Hoàn thành'
          AND NGAYMUA >= CURDATE() - INTERVAL 6 DAY
          AND NGAYMUA <= CURDATE()
        GROUP BY DATE(NGAYMUA)
        ORDER BY ngay ASC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    // Index kết quả theo ngày để tra nhanh
    $map = [];
    foreach ($rows as $row) {
        $map[$row['ngay']] = (float) $row['doanhthu'];
    }

    // Tạo đủ 7 ngày, ngày nào không có doanh thu thì = 0
    $days   = [];
    $labels = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];

    for ($i = 6; $i >= 0; $i--) {
        $date    = date('Y-m-d', strtotime("-$i day"));
        $dow     = (int) date('w', strtotime($date)); // 0=CN, 1=T2...6=T7
        $label   = $labels[$dow === 0 ? 6 : $dow - 1];
        $revenue = $map[$date] ?? 0;

        $days[] = [
            'date'    => $date,
            'label'   => $label,
            'revenue' => $revenue,
        ];
    }

    return $days;
}

/**
 * Tính % height cho bar chart (so với ngày cao nhất = 100%)
 */
function calc_bar_heights(array $days): array {
    $max = max(array_column($days, 'revenue'));
    if ($max == 0) $max = 1; // tránh chia 0

    return array_map(function ($d) use ($max) {
        return [
            ...$d,
            'height' => round($d['revenue'] / $max * 100),
        ];
    }, $days);
}

/**
 * Tính trung bình và đỉnh trong 7 ngày
 */
function get_revenue_stats(array $days): array {
    $revenues = array_column($days, 'revenue');
    $max      = max($revenues);
    $avg      = array_sum($revenues) / count($revenues);

    return [
        'avg' => $avg,
        'max' => $max,
    ];
}