<?php
// services/categories/get_category_list.php
// Hàm: getCategoryList(), getCategoryById()

function getCategoryList(PDO $pdo, array $filters = []): array
{
    $where  = ['1=1'];
    $params = [];

    if (!empty($filters['keyword'])) {
        $where[]     = 'dm.LOAISP LIKE :kw';
        $params[':kw'] = '%' . trim($filters['keyword']) . '%';
    }

    $sql = 'SELECT
              dm.idDM,
              dm.LOAISP,
              COUNT(sp.idSP)                          AS tong_sp,
              SUM(sp.TRANGTHAI = 1)                   AS sp_active,
              COALESCE(SUM(sp.SOLUONG), 0)            AS tong_ton_kho
            FROM danhmuc dm
            LEFT JOIN sanpham sp ON sp.idDM = dm.idDM
            WHERE ' . implode(' AND ', $where) . '
            GROUP BY dm.idDM, dm.LOAISP
            ORDER BY dm.idDM ASC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getCategoryById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare(
        'SELECT * FROM danhmuc WHERE idDM = :id LIMIT 1'
    );
    $stmt->execute([':id' => $id]);
    return $stmt->fetch() ?: null;
}