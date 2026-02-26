<?php
/**
 * controllers/categories/update.php
 * Cập nhật tên danh mục
 *
 * Schema: danhmuc(idDM, LOAISP)
 * Input : $id (int), $name (string)
 * Output: $result = ['success' => bool, 'message' => string]
 */
require_once 'services/connectDB.php'; 
require_once 'services/object_status.php';

$result = ['success' => false, 'message' => ''];

$id   = (int) ($id   ?? 0);
$name = trim($name ?? '');

if ($id <= 0) {
    $result['message'] = 'ID danh mục không hợp lệ.';
    return;
}
if ($name === '') {
    $result['message'] = 'Tên danh mục không được để trống.';
    return;
}

try {
    // Kiểm tra danh mục tồn tại
    $stmt = $conn->prepare("SELECT idDM FROM danhmuc WHERE idDM = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $result['message'] = 'Danh mục không tồn tại.';
        $stmt->close();
        return;
    }
    $stmt->close();

    // Kiểm tra trùng tên với danh mục KHÁC
    $stmt = $conn->prepare(
        "SELECT idDM FROM danhmuc WHERE LOWER(LOAISP) = LOWER(?) AND idDM != ?"
    );
    $stmt->bind_param('si', $name, $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $result['message'] = "Tên \"$name\" đã được dùng bởi danh mục khác.";
        $stmt->close();
        return;
    }
    $stmt->close();

    // Cập nhật — chỉ cập nhật LOAISP
    $stmt = $conn->prepare(
        "UPDATE danhmuc SET LOAISP = ? WHERE idDM = ?"
    );
    $stmt->bind_param('si', $name, $id);

    if ($stmt->execute()) {
        $result['success'] = true;
        $result['message'] = "Đã cập nhật danh mục \"$name\" thành công.";
    } else {
        $result['message'] = 'Lỗi khi cập nhật. Vui lòng thử lại.';
    }
    $stmt->close();

} catch (mysqli_sql_exception $e) {
    $result['message'] = 'Lỗi hệ thống: ' . $e->getMessage();
    file_put_contents('logs/index/dashboard.text',
        date('[Y-m-d H:i:s]') . ' [CATEGORIES/update] ' . $e->getMessage() . "\n",
        FILE_APPEND
    );
}