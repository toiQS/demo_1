<?php
/**
 * controllers/categories/remove.php
 * Xoá danh mục (chỉ cho phép nếu không còn sản phẩm nào thuộc DM)
 *
 * Input  : $id (int)
 * Output : $result = ['success' => bool, 'message' => string]
 */
require_once 'controllers/connectDB.php';
require_once 'controllers/object_status.php';

$result = ['success' => false, 'message' => ''];

$id = (int)($id ?? 0);

if ($id <= 0) {
    $result['message'] = 'ID danh mục không hợp lệ.';
    return;
}

try {
    // Lấy tên danh mục
    $stmt = $conn->prepare("SELECT TENDM FROM danhmuc WHERE idDM = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($tendm);

    if (!$stmt->fetch()) {
        $result['message'] = 'Danh mục không tồn tại.';
        $stmt->close();
        return;
    }
    $stmt->close();

    // Kiểm tra còn sản phẩm không (mọi trạng thái)
    $stmt = $conn->prepare(
        "SELECT COUNT(*) AS cnt FROM sanpham WHERE idDM = ?"
    );
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($cnt);
    $stmt->fetch();
    $stmt->close();

    if ($cnt > 0) {
        $result['message'] = "Không thể xoá: danh mục \"$tendm\" còn $cnt sản phẩm.";
        return;
    }

    // Xoá
    $stmt = $conn->prepare("DELETE FROM danhmuc WHERE idDM = ?");
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        $result['success'] = true;
        $result['message'] = "Đã xoá danh mục \"$tendm\" thành công.";
    } else {
        $result['message'] = 'Lỗi khi xoá danh mục. Vui lòng thử lại.';
    }
    $stmt->close();

} catch (mysqli_sql_exception $e) {
    $result['message'] = 'Lỗi hệ thống: ' . $e->getMessage();
    file_put_contents('logs/index/dashboard.text',
        date('[Y-m-d H:i:s]') . ' [CATEGORIES/remove] ' . $e->getMessage() . "\n",
        FILE_APPEND
    );
}
