<?php
// services/users/add_user.php
// Hàm addUser(PDO, array) → ['ok'=>bool, 'msg'=>string]

function addUser(PDO $pdo, array $d): array
{
    // ── Validate bắt buộc ──────────────────────────────────
    foreach (['username','password','hoten','email','sdt','address','phanloai'] as $f) {
        if (empty(trim($d[$f] ?? ''))) {
            return ['ok' => false, 'msg' => "Vui lòng điền đầy đủ thông tin."];
        }
    }

    if (strlen(trim($d['password'])) < 6) {
        return ['ok' => false, 'msg' => 'Mật khẩu phải ít nhất 6 ký tự.'];
    }

    if (!filter_var(trim($d['email']), FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'msg' => 'Email không hợp lệ.'];
    }

    if (!preg_match('/^\d{10}$/', trim($d['sdt']))) {
        return ['ok' => false, 'msg' => 'Số điện thoại phải đúng 10 chữ số.'];
    }

    // ── Kiểm tra trùng username / email ───────────────────
    $chk = $pdo->prepare(
        'SELECT COUNT(*) FROM taikhoan WHERE USERNAME = :u OR EMAIL = :e'
    );
    $chk->execute([':u' => trim($d['username']), ':e' => trim($d['email'])]);
    if ($chk->fetchColumn() > 0) {
        return ['ok' => false, 'msg' => 'Username hoặc Email đã tồn tại.'];
    }

    // ── Insert ─────────────────────────────────────────────
    $pdo->prepare(
        'INSERT INTO taikhoan (USERNAME, PASSWORD, HOTEN, EMAIL, SDT, ADDRESS, PHANLOAI, TRANGTHAI)
         VALUES (:u, :pw, :ht, :em, :sdt, :addr, :pl, 1)'
    )->execute([
        ':u'    => trim($d['username']),
        ':pw'   => password_hash(trim($d['password']), PASSWORD_DEFAULT),
        ':ht'   => trim($d['hoten']),
        ':em'   => trim($d['email']),
        ':sdt'  => trim($d['sdt']),
        ':addr' => trim($d['address']),
        ':pl'   => (int)$d['phanloai'],
    ]);

    return ['ok' => true, 'msg' => 'Thêm người dùng thành công!'];
}
