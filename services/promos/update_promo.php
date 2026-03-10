<?php
// services/promos/update_promo.php
// Hàm updatePromo(PDO, int, array) → ['ok'=>bool, 'msg'=>string]

function updatePromo(PDO $pdo, int $id, array $d): array
{
    // ── Validate ────────────────────────────────────────────
    foreach (['giatri', 'soluong', 'ngayapdung', 'hansudung'] as $f) {
        if (empty(trim($d[$f] ?? ''))) {
            return ['ok' => false, 'msg' => 'Vui lòng điền đầy đủ thông tin.'];
        }
    }

    $giatri = (float)$d['giatri'];
    if ($giatri <= 0 || $giatri > 1) {
        return ['ok' => false, 'msg' => 'Giá trị giảm phải từ 0.01 đến 1.00.'];
    }

    $soluong = (int)$d['soluong'];
    if ($soluong < 1) {
        return ['ok' => false, 'msg' => 'Số lượt dùng phải ít nhất là 1.'];
    }

    if ($d['hansudung'] < $d['ngayapdung']) {
        return ['ok' => false, 'msg' => 'Hạn sử dụng phải sau hoặc bằng ngày áp dụng.'];
    }

    // ── Update ─────────────────────────────────────────────
    $pdo->prepare(
        'UPDATE khuyenmai
         SET GIATRI     = :giatri,
             SOLUONG    = :sl,
             NGAYAPDUNG = :ngay,
             HANSUDUNG  = :han,
             TRANGTHAI  = :tt
         WHERE MAKHUYENMAI = :id'
    )->execute([
        ':giatri' => $giatri,
        ':sl'     => $soluong,
        ':ngay'   => $d['ngayapdung'],
        ':han'    => $d['hansudung'],
        ':tt'     => isset($d['trangthai']) ? 1 : 0,
        ':id'     => $id,
    ]);

    return ['ok' => true, 'msg' => 'Cập nhật mã khuyến mãi thành công!'];
}