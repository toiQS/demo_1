<?php
/**
 * controllers/categories/gets.php
 * Lấy danh sách danhmuc + đếm số sản phẩm mỗi danh mục
 *
 * Schema thực tế: danhmuc(idDM, LOAISP)
 * — Không có MOTA, không có TRANGTHAI
 *
 * Output: $categories[]  mỗi phần tử:
 *   id     => idDM
 *   name   => LOAISP
 *   desc   => '' (cột không tồn tại, trả về rỗng)
 *   status => 1  (cột không tồn tại, mặc định hoạt động)
 *   count  => số sản phẩm đang bán thuộc danh mục
 */
require_once 'services/connectDB.php';
require_once 'services/object_status.php';

$categories = [];

try {
    $sp_active = trang_thai_san_pham::ACTIVE->value;

    $res = $conn->query(
        "SELECT
             dm.idDM              AS id,
             dm.LOAISP            AS name,
             ''                   AS `desc`,
             1                    AS status,
             COUNT(sp.idSP)       AS `count`
         FROM danhmuc dm
         LEFT JOIN sanpham sp
               ON sp.idDM      = dm.idDM
              AND sp.TRANGTHAI = $sp_active
         GROUP BY dm.idDM, dm.LOAISP
         ORDER BY dm.idDM ASC"
    );

    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $categories[] = $row;
        }
    }

} catch (mysqli_sql_exception $e) {
    file_put_contents('logs/index/dashboard.text',
        date('[Y-m-d H:i:s]') . ' [CATEGORIES/gets] ' . $e->getMessage() . "\n",
        FILE_APPEND
    );
}

// ── Badge counts cho sidebar (layout.php cần 2 biến này) ──────
$pending_orders  = 0;
$low_stock_count = 0;

try {
    $pending = trang_thai_hoa_don::PENDING->value;
    $pending_orders = (int) $conn
        ->query("SELECT COUNT(*) AS c FROM hoadon WHERE TRANGTHAI = '$pending'")
        ->fetch_assoc()['c'];

    $sp_active = trang_thai_san_pham::ACTIVE->value;
    $low_stock_count = (int) $conn
        ->query("SELECT COUNT(*) AS c
                 FROM sanpham
                 WHERE SOLUONG < 10
                   AND TRANGTHAI = $sp_active")
        ->fetch_assoc()['c'];

} catch (mysqli_sql_exception $e) {
    file_put_contents('logs/index/dashboard.text',
        date('[Y-m-d H:i:s]') . ' [CATEGORIES/badge] ' . $e->getMessage() . "\n",
        FILE_APPEND
    );
}
