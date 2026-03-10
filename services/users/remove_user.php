<?php
// services/users/remove_user.php
// 3 hàm: lockUser(), unlockUser(), deleteUser()
// Tất cả trả về ['ok'=>bool, 'msg'=>string]

function lockUser(PDO $pdo, int $id): array
{
    $stmt = $pdo->prepare('UPDATE taikhoan SET TRANGTHAI = 0 WHERE idTK = :id');
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount()
        ? ['ok' => true,  'msg' => 'Đã khoá tài khoản.']
        : ['ok' => false, 'msg' => 'Không tìm thấy tài khoản.'];
}

function unlockUser(PDO $pdo, int $id): array
{
    $stmt = $pdo->prepare('UPDATE taikhoan SET TRANGTHAI = 1 WHERE idTK = :id');
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount()
        ? ['ok' => true,  'msg' => 'Đã mở khoá tài khoản.']
        : ['ok' => false, 'msg' => 'Không tìm thấy tài khoản.'];
}

function deleteUser(PDO $pdo, int $id): array
{
    // Không xoá nếu còn hóa đơn
    $chk = $pdo->prepare('SELECT COUNT(*) FROM hoadon WHERE idTK = :id');
    $chk->execute([':id' => $id]);
    if ($chk->fetchColumn() > 0) {
        return [
            'ok'  => false,
            'msg' => 'Không thể xoá: tài khoản còn hóa đơn liên quan. Hãy dùng Khoá thay vì Xoá.',
        ];
    }

    // Xoá dữ liệu phụ trước
    foreach ([
        'DELETE FROM chitietgiohang   WHERE idTK = :id',
        'DELETE FROM chitietkhuyenmai WHERE idTK = :id',
        'DELETE FROM cauhinh_canhbao  WHERE idND = :id',
    ] as $sql) {
        $pdo->prepare($sql)->execute([':id' => $id]);
    }

    $stmt = $pdo->prepare('DELETE FROM taikhoan WHERE idTK = :id');
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount()
        ? ['ok' => true,  'msg' => 'Đã xoá tài khoản thành công.']
        : ['ok' => false, 'msg' => 'Không tìm thấy tài khoản.'];
}
