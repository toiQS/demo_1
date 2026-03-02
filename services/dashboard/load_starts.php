<?php

include_once(__DIR__ . "/../../database/db.php");

$starts = [
    'revenue_current_monthly'      => 0,
    'percent_revenue_last_monthly' => 0,
    'total_order_count'            => 0,
    'new_order_count'              => 0,
    'total_customer_count'         => 0,
    'new_customer_current_week'    => 0,
    'stock_is_running_out'         => 0,
];

try {
    $month = date("m");
    $year  = date("Y");
    $day = date("d");

    $starts['revenue_current_monthly'] = get_revenue_current_month($pdo, $month, $year);
    $revenue_last_monthly              = get_revenue_last_month($pdo, $month, $year);

    // ← dùng / (chia) thay vì % (modulo), thêm kiểm tra chia cho 0
    if ($revenue_last_monthly > 0) {
        $starts['percent_revenue_last_monthly'] =
            round(($starts['revenue_current_monthly'] - $revenue_last_monthly) / $revenue_last_monthly * 100, 1);
    }
    $starts['total_order_count'] = get_total_order_count($pdo);
    $starts['new_order_count'] = get_new_order_count($pdo,$day,$month,$year);
    $starts['total_customer_count'] = get_total_customer_count($pdo);
    $starts['new_customer_current_week'] = get_new_customer_current_week($pdo);
    $starts['stock_is_running_out'] = get_stock_is_running_out($pdo);

} catch (Exception $e) {
    echo $e->getMessage();
}

function get_revenue_current_month(PDO $pdo, $month, $year)
{
    $sql = "SELECT COALESCE(SUM(THANHTIEN), 0) AS doanhthu 
            FROM hoadon 
            WHERE TRANGTHAI = 'Hoàn thành'
            AND MONTH(NGAYMUA) = :month
            AND YEAR(NGAYMUA)  = :year";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':month' => $month, ':year' => $year]);
    return $stmt->fetch()['doanhthu'] ?? 0;
}

function get_revenue_last_month(PDO $pdo, $month, $year)
{
    // ← xử lý đúng khi tháng 1 → lùi về tháng 12 năm trước
    $prevMonth = $month == 1 ? 12 : $month - 1;
    $prevYear  = $month == 1 ? $year - 1 : $year;

    $sql = "SELECT COALESCE(SUM(THANHTIEN), 0) AS doanhthu 
            FROM hoadon 
            WHERE TRANGTHAI = 'Hoàn thành'
            AND MONTH(NGAYMUA) = :month
            AND YEAR(NGAYMUA)  = :year";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':month' => $prevMonth, ':year' => $prevYear]);
    return $stmt->fetch()['doanhthu'] ?? 0;
}

function get_total_order_count(PDO $pdo)
{
    $sql = "SELECT COUNT(*) as count FROM hoadon WHERE TRANGTHAI = 'Hoàn thành'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetch()['count'] ?? 0;
}

function get_new_order_count(PDO $pdo, $day,$month, $year)
{
    $sql = "SELECT COUNT(*) as count
            FROM hoadon 
            WHERE TRANGTHAI = 'Hoàn thành'
            and Day(NGAYMUA) = :day
            AND MONTH(NGAYMUA) = :month
            AND YEAR(NGAYMUA)  = :year";
    $stmt = $pdo->prepare($sql);
     $stmt->execute([':day' => $day, ':month' => $month, ':year' => $year]);
    return $stmt->fetch()['count'] ?? 0;
}
function get_total_customer_count(PDO $pdo)
{
    $sql = "SELECT COUNT(*) as count FROM taikhoan WHERE TRANGTHAI = 1 and PHANLOAI = 1;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetch()['count'] ?? 0;
}

function get_new_customer_current_week(PDO $pdo) {
    $sql = "SELECT COUNT(*) AS count 
            FROM taikhoan 
            WHERE PHANLOAI = 1
            AND TRANGTHAI = 1
            AND NGAYTAO >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetch()['count'] ?? 0;
}

function get_stock_is_running_out(PDO $pdo) {
    $sql = "SELECT COUNT(DISTINCT s.idSP) AS count
            FROM sanpham s
            JOIN cauhinh_canhbao c ON c.idSP = s.idSP
            WHERE s.TRANGTHAI = 1
            AND s.SOLUONG <= c.NGUONG_DAT";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetch()['count'] ?? 0;
}