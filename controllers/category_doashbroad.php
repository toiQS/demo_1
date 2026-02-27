<?php
include_once "connectDB.php";
include_once "object_status.php";

// ===== STATS =====
$stats = [
    'total_orders' => 1,
    'pending_orders' => 0,
    'revenue_month' => 0,
    'total_products' => 0,
    'low_stock' => 0,
    'total_users' => 0,
    'import_receipts' => 0,
];
// echo json_encode($stats);

try {
    $month = date('m');
    $year  = date('Y');

    // Tổng đơn hàng
    $stats['total_orders'] = $conn
        ->query("SELECT COUNT(*) AS total FROM hoadon")
        ->fetch_assoc()['total'];

    // Đơn chờ xác nhận
    $pending = trang_thai_hoa_don::PENDING->value;
    $stats['pending_orders'] = $conn
        ->query("SELECT COUNT(*) AS total FROM hoadon WHERE TRANGTHAI = '$pending'")
        ->fetch_assoc()['total'];

    // Doanh thu tháng (chỉ tính đơn hoàn thành)
    $completed = trang_thai_hoa_don::COMPLETED->value;
    $stats['revenue_month'] = $conn
        ->query("SELECT COALESCE(SUM(THANHTIEN), 0) AS total
                 FROM hoadon
                 WHERE TRANGTHAI = '$completed'
                   AND MONTH(NGAYMUA) = $month
                   AND YEAR(NGAYMUA)  = $year")
        ->fetch_assoc()['total'];

    // Tổng sản phẩm đang bán
    $sp_active = trang_thai_san_pham::ACTIVE->value;
    $stats['total_products'] = $conn
        ->query("SELECT COUNT(*) AS total FROM sanpham WHERE TRANGTHAI = $sp_active")
        ->fetch_assoc()['total'];

    // Sản phẩm sắp hết hàng (đang bán, tồn < 10)
    $stats['low_stock'] = $conn
        ->query("SELECT COUNT(*) AS total FROM sanpham WHERE SOLUONG < 10 AND TRANGTHAI = $sp_active")
        ->fetch_assoc()['total'];

    // Tổng tài khoản
    $stats['total_users'] = $conn
        ->query("SELECT COUNT(*) AS total FROM taikhoan")
        ->fetch_assoc()['total'];

    // Phiếu nhập hoàn tất trong tháng
    $pn_hoan_tat = trang_thai_phieu_nhap::HOAN_TAT->value;
    $stats['import_receipts'] = $conn
        ->query("SELECT COUNT(*) AS total FROM phieunhap
                 WHERE TRANGTHAI = $pn_hoan_tat
                   AND MONTH(NGAYNHAP) = $month
                   AND YEAR(NGAYNHAP)  = $year")
        ->fetch_assoc()['total'];

} catch (mysqli_sql_exception $e) {
    file_put_contents("logs/index/dashboard.text",
        date('[Y-m-d H:i:s]') . " [STATS] " . $e->getMessage() . "\n", FILE_APPEND);
}

// ===== RECENT ORDERS =====
$recent_orders = [];
try {
    // Lấy tất cả trạng thái trừ đơn đã huỷ
    $cancelled = trang_thai_hoa_don::CANCELLED->value;
    $res = $conn->query(
        "SELECT hd.idHD, tk.HOTEN, hd.THANHTIEN, hd.TRANGTHAI, hd.NGAYMUA
         FROM hoadon hd
         JOIN taikhoan tk ON hd.idTK = tk.idTK
         WHERE hd.TRANGTHAI != '$cancelled'
         ORDER BY hd.NGAYMUA DESC
         LIMIT 10"
    );
    while ($row = $res->fetch_assoc()) {
        $recent_orders[] = [
            'id'       => '#HD' . str_pad($row['idHD'], 4, '0', STR_PAD_LEFT),
            'customer' => $row['HOTEN'],
            'total'    => number_format($row['THANHTIEN']) . '₫',
            'status'   => $row['TRANGTHAI'],
            'date'     => date('d/m/Y', strtotime($row['NGAYMUA'])),
        ];
    }
} catch (mysqli_sql_exception $e) {
    file_put_contents("logs/index/dashboard.text",
        date('[Y-m-d H:i:s]') . " [ORDERS] " . $e->getMessage() . "\n", FILE_APPEND);
}

// ===== LOW STOCK ITEMS =====
$low_stock_items = [];
try {
    $sp_active = trang_thai_san_pham::ACTIVE->value;
    $res = $conn->query(
        "SELECT TENSP, SOLUONG FROM sanpham
         WHERE SOLUONG < 10 AND TRANGTHAI = $sp_active
         ORDER BY SOLUONG ASC
         LIMIT 5"
    );
    while ($row = $res->fetch_assoc()) {
        $low_stock_items[] = [
            'name'      => $row['TENSP'],
            'qty'       => $row['SOLUONG'],
            'threshold' => 10,
        ];
    }
} catch (mysqli_sql_exception $e) {
    file_put_contents("logs/index/dashboard.text",
        date('[Y-m-d H:i:s]') . " [LOWSTOCK] " . $e->getMessage() . "\n", FILE_APPEND);
}

// ===== STATUS LABELS =====
// Map từ enum value → class CSS + nhãn hiển thị
$status_labels = [
    trang_thai_hoa_don::PENDING->value    => ['class' => 'badge-pending',    'label' => 'Chờ xác nhận'],
    trang_thai_hoa_don::PROCESSING->value => ['class' => 'badge-processing', 'label' => 'Đang xử lý'],
    trang_thai_hoa_don::SHIPPED->value    => ['class' => 'badge-shipped',    'label' => 'Đang giao'],
    trang_thai_hoa_don::COMPLETED->value  => ['class' => 'badge-completed',  'label' => 'Hoàn thành'],
    trang_thai_hoa_don::CANCELLED->value  => ['class' => 'badge-cancelled',  'label' => 'Đã huỷ'],
];

$pending_orders  = $stats['pending_orders'];
$low_stock_count = $stats['low_stock'];
$conn->close();
?>