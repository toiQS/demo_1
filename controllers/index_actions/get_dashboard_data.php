<?php
include_once "controllers/connectDB.php";

// ===== STATS =====
$stats = [
    'total_orders'    => 0,
    'pending_orders'  => 0,
    'revenue_month'   => 0,
    'total_products'  => 0,
    'low_stock'       => 0,
    'total_users'     => 0,
    'import_receipts' => 0,
];

try {
    $month = date('m');
    $year  = date('Y');

    $stats['total_orders'] = $conn
        ->query("SELECT COUNT(*) AS total FROM hoadon")
        ->fetch_assoc()['total'];

    $stats['pending_orders'] = $conn
        ->query("SELECT COUNT(*) AS total FROM hoadon WHERE TRANGTHAI = 'pending'")
        ->fetch_assoc()['total'];

    $stats['revenue_month'] = $conn
        ->query("SELECT COALESCE(SUM(THANHTIEN), 0) AS total
                 FROM hoadon
                 WHERE MONTH(NGAYMUA) = $month AND YEAR(NGAYMUA) = $year")
        ->fetch_assoc()['total'];

    $stats['total_products'] = $conn
        ->query("SELECT COUNT(*) AS total FROM sanpham WHERE TRANGTHAI = 1")
        ->fetch_assoc()['total'];

    $stats['low_stock'] = $conn
        ->query("SELECT COUNT(*) AS total FROM sanpham WHERE SOLUONG < 10 AND TRANGTHAI = 1")
        ->fetch_assoc()['total'];

    $stats['total_users'] = $conn
        ->query("SELECT COUNT(*) AS total FROM taikhoan")
        ->fetch_assoc()['total'];

    $stats['import_receipts'] = $conn
        ->query("SELECT COUNT(*) AS total FROM phieunhap
                 WHERE MONTH(NGAYNHAP) = $month AND YEAR(NGAYNHAP) = $year")
        ->fetch_assoc()['total'];

} catch (mysqli_sql_exception $e) {
    file_put_contents(
        "logs\index\dashboard.text",
        date('[Y-m-d H:i:s]') . " [STATS] " . $e->getMessage() . "\n",
        FILE_APPEND
    );
}

// ===== RECENT ORDERS =====
$recent_orders = [];
try {
    $res = $conn->query(
        "SELECT hd.idHD, tk.HOTEN, hd.THANHTIEN, hd.TRANGTHAI, hd.NGAYMUA
         FROM hoadon hd
         JOIN taikhoan tk ON hd.idTK = tk.idTK
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
    file_put_contents(
        "logs/dashboard.txt",
        date('[Y-m-d H:i:s]') . " [ORDERS] " . $e->getMessage() . "\n",
        FILE_APPEND
    );
}

// ===== LOW STOCK ITEMS =====
$low_stock_items = [];
try {
    $res = $conn->query(
        "SELECT TENSP, SOLUONG FROM sanpham
         WHERE SOLUONG < 10 AND TRANGTHAI = 1
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
    file_put_contents(
        "logs/dashboard.txt",
        date('[Y-m-d H:i:s]') . " [LOWSTOCK] " . $e->getMessage() . "\n",
        FILE_APPEND
    );
}

// ===== STATUS LABELS =====
$status_labels = [
    'pending'    => ['class' => 'badge-pending',    'label' => 'Chờ xác nhận'],
    'processing' => ['class' => 'badge-processing', 'label' => 'Đang xử lý'],
    'shipped'    => ['class' => 'badge-shipped',    'label' => 'Đang giao'],
    'completed'  => ['class' => 'badge-completed',  'label' => 'Hoàn thành'],
    'cancelled'  => ['class' => 'badge-cancelled',  'label' => 'Đã huỷ'],
];
