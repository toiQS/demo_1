<?php
// services/categories/update_category.php
// Hàm updateCategory(PDO, int, array) → ['ok'=>bool, 'msg'=>string]

function updateCategory(PDO $pdo, int $id, array $d): array
{
    $loaisp = trim($d['loaisp'] ?? '');

    if ($loaisp === '') {
        return ['ok' => false, 'msg' => 'Vui lòng nhập tên danh mục.'];
    }

    if (mb_strlen($loaisp) > 20) {
        return ['ok' => false, 'msg' => 'Tên danh mục tối đa 20 ký tự.'];
    }

    // Kiểm tra trùng tên với danh mục khác
    $chk = $pdo->prepare(
        'SELECT COUNT(*) FROM danhmuc WHERE LOAISP = :name AND idDM != :id'
    );
    $chk->execute([':name' => $loaisp, ':id' => $id]);
    if ($chk->fetchColumn() > 0) {
        return ['ok' => false, 'msg' => "Tên danh mục «{$loaisp}» đã được dùng."];
    }

    $stmt = $pdo->prepare('UPDATE danhmuc SET LOAISP = :name WHERE idDM = :id');
    $stmt->execute([':name' => $loaisp, ':id' => $id]);

    return $stmt->rowCount()
        ? ['ok' => true,  'msg' => 'Cập nhật danh mục thành công!']
        : ['ok' => false, 'msg' => 'Không tìm thấy danh mục.'];
}