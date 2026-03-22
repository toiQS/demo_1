<?php
// services/categories/add_category.php
// Hàm addCategory(PDO, array) → ['ok'=>bool, 'msg'=>string]

function addCategory(PDO $pdo, array $d): array
{
    $loaisp = trim($d['loaisp'] ?? '');

    if ($loaisp === '') {
        return ['ok' => false, 'msg' => 'Vui lòng nhập tên danh mục.'];
    }

    if (mb_strlen($loaisp) > 20) {
        return ['ok' => false, 'msg' => 'Tên danh mục tối đa 20 ký tự.'];
    }

    // Kiểm tra trùng tên
    $chk = $pdo->prepare('SELECT COUNT(*) FROM danhmuc WHERE LOAISP = :name');
    $chk->execute([':name' => $loaisp]);
    if ($chk->fetchColumn() > 0) {
        return ['ok' => false, 'msg' => "Danh mục «{$loaisp}» đã tồn tại."];
    }

    $pdo->prepare('INSERT INTO danhmuc (LOAISP) VALUES (:name)')
        ->execute([':name' => $loaisp]);

    return ['ok' => true, 'msg' => "Đã thêm danh mục «{$loaisp}» thành công!"];
}