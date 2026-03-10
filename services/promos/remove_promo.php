<?php
// services/promos/remove_promo.php

// ── Toggle bật / tắt ──────────────────────────────────────
function togglePromo(PDO $pdo, int $id): array
{
    try {
        $stmt = $pdo->prepare(
            'UPDATE khuyenmai
             SET TRANGTHAI = CASE WHEN TRANGTHAI = 0 THEN 1 ELSE 0 END
             WHERE MAKHUYENMAI = :id'
        );
        $stmt->execute([':id' => $id]);

        if (!$stmt->rowCount()) {
            return ['ok' => false, 'msg' => 'Không tìm thấy mã khuyến mãi.'];
        }

        $new = $pdo->prepare('SELECT TRANGTHAI FROM khuyenmai WHERE MAKHUYENMAI = :id');
        $new->execute([':id' => $id]);
        $tt = (int)$new->fetchColumn();

        return [
            'ok'  => true,
            'msg' => $tt === 0 ? 'Đã bật mã khuyến mãi.' : 'Đã tạm dừng mã khuyến mãi.',
        ];
    } catch (PDOException $e) {
        return ['ok' => false, 'msg' => 'Lỗi: ' . $e->getMessage()];
    }
}

// ── Xoá vĩnh viễn ─────────────────────────────────────────
function deletePromo(PDO $pdo, int $id): array
{
    try {
        $pdo->beginTransaction();

        // Lấy tên mã để hiển thị thông báo
        $chk = $pdo->prepare('SELECT CODE FROM khuyenmai WHERE MAKHUYENMAI = :id');
        $chk->execute([':id' => $id]);
        $promo = $chk->fetch();

        if (!$promo) {
            $pdo->rollBack();
            return ['ok' => false, 'msg' => 'Không tìm thấy mã khuyến mãi.'];
        }

        // Xoá chi tiết khuyến mãi của user trước
        $pdo->prepare('DELETE FROM chitietkhuyenmai WHERE idKM = :id')
            ->execute([':id' => $id]);

        // Xoá mã
        $stmt = $pdo->prepare('DELETE FROM khuyenmai WHERE MAKHUYENMAI = :id');
        $stmt->execute([':id' => $id]);

        $pdo->commit();
        return ['ok' => true, 'msg' => 'Đã xoá mã «' . $promo['CODE'] . '» thành công.'];

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();

        // Lỗi FK: có hóa đơn đang dùng mã này
        if ($e->getCode() === '23000') {
            return [
                'ok'  => false,
                'msg' => 'Không thể xoá: mã đang được dùng trong hóa đơn. Hãy dùng Tạm dừng thay vì Xoá.',
            ];
        }

        return ['ok' => false, 'msg' => 'Lỗi: ' . $e->getMessage()];
    }
}
