<?php
// services/users/get_user_list.php
// Cung cấp: getUserList(), getUserById(), getUserTypes()

function getUserList(PDO $pdo, array $filters = []): array
{
    $where  = ['1=1'];
    $params = [];

    if (!empty($filters['phanloai'])) {
        $where[]             = 'tk.PHANLOAI = :phanloai';
        $params[':phanloai'] = (int)$filters['phanloai'];
    }

    if (isset($filters['trangthai']) && $filters['trangthai'] !== '') {
        $where[]              = 'tk.TRANGTHAI = :trangthai';
        $params[':trangthai'] = (int)$filters['trangthai'];
    }

    if (!empty($filters['keyword'])) {
        $where[]            = '(tk.HOTEN LIKE :kw OR tk.USERNAME LIKE :kw OR tk.EMAIL LIKE :kw)';
        $params[':kw']      = '%' . trim($filters['keyword']) . '%';
    }

    $sql = 'SELECT tk.idTK, tk.USERNAME, tk.HOTEN, tk.EMAIL,
                   tk.SDT, tk.ADDRESS, tk.PHANLOAI, tk.TRANGTHAI,
                   ut.Ten AS TEN_LOAI
            FROM   taikhoan tk
            LEFT JOIN usertype ut ON tk.PHANLOAI = ut.idType
            WHERE  ' . implode(' AND ', $where) . '
            ORDER BY tk.idTK DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getUserById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare(
        'SELECT tk.*, ut.Ten AS TEN_LOAI
         FROM taikhoan tk
         LEFT JOIN usertype ut ON tk.PHANLOAI = ut.idType
         WHERE tk.idTK = :id LIMIT 1'
    );
    $stmt->execute([':id' => $id]);
    return $stmt->fetch() ?: null;
}

function getUserTypes(PDO $pdo): array
{
    return $pdo->query('SELECT * FROM usertype ORDER BY idType')->fetchAll();
}
