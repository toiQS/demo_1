<?php
// services/inventories/get_warning_list.php
// Hàm getWarningList(PDO) → sản phẩm có SOLUONG <= NGUONG_DAT
// Dùng cho panel "Cảnh báo tồn kho" bên phải

function getWarningList(PDO $pdo): array
{
    try {
        $stmt = $pdo->query(
            'SELECT
               sp.idSP,
               sp.TENSP,
               sp.SOLUONG,
               COALESCE(cb.NGUONG_DAT, 5) AS NGUONG_DAT,
               CASE
                 WHEN sp.SOLUONG = 0 THEN "out"
                 ELSE "low"
               END AS STATUS
             FROM   sanpham sp
             LEFT JOIN cauhinh_canhbao cb ON cb.idSP = sp.idSP
             WHERE  sp.TRANGTHAI = 1
               AND  sp.SOLUONG <= COALESCE(cb.NGUONG_DAT, 5)
             ORDER BY sp.SOLUONG ASC, sp.TENSP ASC
             LIMIT 50'
        );
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log('getWarningList: ' . $e->getMessage());
        return [];
    }
}