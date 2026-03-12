<?php
// services/inventories/get_detail_list.php
// Hàm getInventoryDetail(PDO, array) → array chi tiết tồn kho từng SKU

function getInventoryDetail(PDO $pdo, array $filters = []): array
{
    $where  = ['sp.TRANGTHAI = 1'];
    $params = [];

    // Lọc theo danh mục
    if (!empty($filters['idDM'])) {
        $where[]      = 'sp.idDM = :idDM';
        $params[':idDM'] = (int)$filters['idDM'];
    }

    // Lọc theo tình trạng
    // 'out'  → hết hàng (SOLUONG = 0)
    // 'low'  → sắp hết  (0 < SOLUONG <= NGUONG_DAT)
    // 'ok'   → còn hàng (SOLUONG > NGUONG_DAT)
    if (!empty($filters['status'])) {
        switch ($filters['status']) {
            case 'out':
                $where[] = 'sp.SOLUONG = 0';
                break;
            case 'low':
                $where[] = 'sp.SOLUONG > 0 AND sp.SOLUONG <= COALESCE(cb.NGUONG_DAT, 5)';
                break;
            case 'ok':
                $where[] = 'sp.SOLUONG > COALESCE(cb.NGUONG_DAT, 5)';
                break;
        }
    }

    // Tìm theo tên
    if (!empty($filters['keyword'])) {
        $where[]      = 'sp.TENSP LIKE :kw';
        $params[':kw'] = '%' . trim($filters['keyword']) . '%';
    }

    $sql = 'SELECT
              sp.idSP,
              sp.TENSP,
              sp.SOLUONG,
              sp.GIABAN,
              dm.LOAISP                              AS DANHMUC,
              COALESCE(cb.NGUONG_DAT, 5)            AS NGUONG_DAT,
              CASE
                WHEN sp.SOLUONG = 0
                  THEN "out"
                WHEN sp.SOLUONG <= COALESCE(cb.NGUONG_DAT, 5)
                  THEN "low"
                ELSE "ok"
              END                                    AS STATUS
            FROM   sanpham sp
            LEFT JOIN danhmuc         dm ON dm.idDM   = sp.idDM
            LEFT JOIN cauhinh_canhbao cb ON cb.idSP   = sp.idSP
            WHERE  ' . implode(' AND ', $where) . '
            ORDER BY
              FIELD(STATUS, "out", "low", "ok"),   -- ưu tiên hết hàng lên đầu
              sp.TENSP ASC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getDanhMucList(PDO $pdo): array
{
    return $pdo->query('SELECT * FROM danhmuc ORDER BY idDM')->fetchAll();
}