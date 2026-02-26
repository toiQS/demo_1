<?php
/**
 * controllers/categories/add.php
 * Thêm danh mục mới
 *
 * Schema: danhmuc(idDM, LOAISP)
 * Input : $name (string) — tên danh mục = LOAISP
 * Output: $result = ['success' => bool, 'message' => string, 'id' => int|null]
 */
require_once 'services/connectDB.php'; 
require_once 'services/object_status.php'; 

$result = ['success' => false, 'message' => '', 'id' => null];

$name = trim($name ?? '');

if ($name === '') {
    $result['message'] = 'Tên danh mục không được để trống.';
    return;
}

try {
    // Kiểm tra trùng tên (không phân biệt hoa/thường)
    $stmt = $conn->prepare(
        "SELECT idDM FROM danhmuc WHERE LOWER(LOAISP) = LOWER(?)"
    );
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $result['message'] = "Danh mục \"$name\" đã tồn tại.";
        $stmt->close();
        return;
    }
    $stmt->close();

    // Thêm mới — chỉ có cột LOAISP
    $stmt = $conn->prepare(
        "INSERT INTO danhmuc (LOAISP) VALUES (?)"
    );
    $stmt->bind_param('s', $name);

    if ($stmt->execute()) {
        $result['success'] = true;
        $result['id']      = $conn->insert_id;
        $result['message'] = "Đã thêm danh mục \"$name\" thành công.";
    } else {
        $result['message'] = 'Lỗi khi thêm danh mục. Vui lòng thử lại.';
    }
    $stmt->close();

} catch (mysqli_sql_exception $e) {
    $result['message'] = 'Lỗi hệ thống: ' . $e->getMessage();
    file_put_contents('logs/index/dashboard.text',
        date('[Y-m-d H:i:s]') . ' [CATEGORIES/add] ' . $e->getMessage() . "\n",
        FILE_APPEND
    );
}
