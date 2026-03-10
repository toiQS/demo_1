<?php
// services/promos/get_promo_list.php
// Hàm: getPromoList(), getPromoById()

function getPromoList(PDO $pdo, array $filters = []): array
{
    $where  = ['1=1'];
    $params = [];

    // Lọc theo trạng thái
    if (isset($filters['trangthai']) && $filters['trangthai'] !== '') {
        $where[]              = 'TRANGTHAI = :trangthai';
        $params[':trangthai'] = (int)$filters['trangthai'];
    }

    // Lọc đã hết hạn
    if (!empty($filters['expired'])) {
        $where[]   = 'HANSUDUNG < :today';
        $params[':today'] = date('Y-m-d');
    }

    // Tìm theo mã code
    if (!empty($filters['keyword'])) {
        $where[]     = 'CODE LIKE :kw';
        $params[':kw'] = '%' . trim($filters['keyword']) . '%';
    }

    $sql = 'SELECT * FROM khuyenmai
            WHERE  ' . implode(' AND ', $where) . '
            ORDER BY MAKHUYENMAI DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getPromoById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare(
        'SELECT * FROM khuyenmai WHERE MAKHUYENMAI = :id LIMIT 1'
    );
    $stmt->execute([':id' => $id]);
    return $stmt->fetch() ?: null;
}