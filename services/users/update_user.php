<?php
// services/users/update_user.php
// Hàm updateUser(PDO, int, array) → ['ok'=>bool, 'msg'=>string]

function updateUser(PDO $pdo, int $id, array $d): array
{
    // ── Validate ───────────────────────────────────────────
    foreach (['hoten','email','sdt','address','phanloai'] as $f) {
        if (empty(trim($d[$f] ?? ''))) {
            return ['ok' => false, 'msg' => 'Vui lòng điền đầy đủ thông tin.'];
        }
    }

    if (!filter_var(trim($d['email']), FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'msg' => 'Email không hợp lệ.'];
    }

    if (!preg_match('/^\d{10}$/', trim($d['sdt']))) {
        return ['ok' => false, 'msg' => 'Số điện thoại phải đúng 10 chữ số.'];
    }

    // ── Kiểm tra email trùng người khác ───────────────────
    $chk = $pdo->prepare(
        'SELECT COUNT(*) FROM taikhoan WHERE EMAIL = :e AND idTK != :id'
    );
    $chk->execute([':e' => trim($d['email']), ':id' => $id]);
    if ($chk->fetchColumn() > 0) {
        return ['ok' => false, 'msg' => 'Email đã được dùng bởi tài khoản khác.'];
    }

    // ── Xây dựng SET động ─────────────────────────────────
    $sets   = [
        'HOTEN    = :ht',
        'EMAIL    = :em',
        'SDT      = :sdt',
        'ADDRESS  = :addr',
        'PHANLOAI = :pl',
    ];
    $params = [
        ':ht'   => trim($d['hoten']),
        ':em'   => trim($d['email']),
        ':sdt'  => trim($d['sdt']),
        ':addr' => trim($d['address']),
        ':pl'   => (int)$d['phanloai'],
        ':id'   => $id,
    ];

    // Đổi mật khẩu chỉ khi có nhập
    $newPw = trim($d['password'] ?? '');
    if ($newPw !== '') {
        if (strlen($newPw) < 6) {
            return ['ok' => false, 'msg' => 'Mật khẩu mới phải ít nhất 6 ký tự.'];
        }
        $sets[]      = 'PASSWORD = :pw';
        $params[':pw'] = password_hash($newPw, PASSWORD_DEFAULT);
    }

    $pdo->prepare(
        'UPDATE taikhoan SET ' . implode(', ', $sets) . ' WHERE idTK = :id'
    )->execute($params);

    return ['ok' => true, 'msg' => 'Cập nhật tài khoản thành công!'];
}
