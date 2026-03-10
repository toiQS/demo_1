<?php
// services/promos/add_promo.php
// Hàm addPromo(PDO, array) → ['ok'=>bool, 'msg'=>string]

function addPromo(PDO $pdo, array $d): array
{
    // ── Validate ────────────────────────────────────────────
    foreach (['code', 'giatri', 'soluong', 'ngayapdung', 'hansudung'] as $f) {
        if (empty(trim($d[$f] ?? ''))) {
            return ['ok' => false, 'msg' => 'Vui lòng điền đầy đủ thông tin.'];
        }
    }

    $code = strtoupper(trim($d['code']));
    if (!preg_match('/^[A-Z0-9]{3,10}$/', $code)) {
        return ['ok' => false, 'msg' => 'Mã code chỉ gồm chữ hoa và số, 3–10 ký tự.'];
    }

    $giatri = (float)$d['giatri'];
    if ($giatri <= 0 || $giatri > 1) {
        return ['ok' => false, 'msg' => 'Giá trị giảm phải từ 0.01 đến 1.00 (tương đương 1%–100%).'];
    }

    $soluong = (int)$d['soluong'];
    if ($soluong < 1) {
        return ['ok' => false, 'msg' => 'Số lượt dùng phải ít nhất là 1.'];
    }

    if ($d['hansudung'] < $d['ngayapdung']) {
        return ['ok' => false, 'msg' => 'Hạn sử dụng phải sau hoặc bằng ngày áp dụng.'];
    }

    // ── Kiểm tra trùng CODE ────────────────────────────────
    $chk = $pdo->prepare('SELECT COUNT(*) FROM khuyenmai WHERE CODE = :c');
    $chk->execute([':c' => $code]);
    if ($chk->fetchColumn() > 0) {
        return ['ok' => false, 'msg' => "Mã code «{$code}» đã tồn tại."];
    }

    // ── Insert ─────────────────────────────────────────────
    $pdo->prepare(
        'INSERT INTO khuyenmai (CODE, GIATRI, SOLUONG, NGAYAPDUNG, HANSUDUNG, TRANGTHAI)
         VALUES (:code, :giatri, :sl, :ngay, :han, :tt)'
    )->execute([
        ':code'  => $code,
        ':giatri'=> $giatri,
        ':sl'    => $soluong,
        ':ngay'  => $d['ngayapdung'],
        ':han'   => $d['hansudung'],
        ':tt'    => isset($d['trangthai']) ? 1 : 0,  // 0=chạy, 1=dừng
    ]);

    return ['ok' => true, 'msg' => "Đã tạo mã khuyến mãi «{$code}» thành công!"];
}