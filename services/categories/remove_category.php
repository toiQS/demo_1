<?php
// services/categories/remove_category.php
// Hàm deleteCategory(PDO, int) → ['ok'=>bool, 'msg'=>string]

function deleteCategory(PDO $pdo, int $id): array
{
    try {
        // Kiểm tra còn sản phẩm thuộc danh mục này không
        $chk = $pdo->prepare('SELECT COUNT(*) FROM sanpham WHERE idDM = :id');
        $chk->execute([':id' => $id]);
        $count = (int)$chk->fetchColumn();

        if ($count > 0) {
            return [
                'ok'  => false,
                'msg' => "Không thể xoá: danh mục đang chứa {$count} sản phẩm. "
                       . "Hãy chuyển hoặc xoá các sản phẩm trước.",
            ];
        }

        $stmt = $pdo->prepare('DELETE FROM danhmuc WHERE idDM = :id');
        $stmt->execute([':id' => $id]);

        return $stmt->rowCount()
            ? ['ok' => true,  'msg' => 'Đã xoá danh mục thành công.']
            : ['ok' => false, 'msg' => 'Không tìm thấy danh mục.'];

    } catch (PDOException $e) {
        // FK từ bảng khác
        if ($e->getCode() === '23000') {
            return ['ok' => false, 'msg' => 'Không thể xoá: danh mục đang được tham chiếu bởi dữ liệu khác.'];
        }
        return ['ok' => false, 'msg' => 'Lỗi: ' . $e->getMessage()];
    }
}