<?php
/**
 * services/category/add.php
 * Thêm danh mục mới
 *
 * Schema: danhmuc(idDM, LOAISP)
 * Input : $name (string)
 * Output: $result = ['success' => bool, 'message' => string, 'id' => int|null]
 */
require_once __DIR__ . '/../connectDB.php';
require_once __DIR__ . '/../object_status.php';

$result = ['success' => false, 'message' => '', 'id' => null];

$name = trim($name ?? '');

if ($name === '') {
    $result['message'] = 'Tên danh mục không được để trống.';
    return;
}

try {
    // Kiểm tra trùng tên
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

    // Thêm mới — schema chỉ có idDM AUTO_INCREMENT + LOAISP
    $stmt = $conn->prepare("INSERT INTO danhmuc (LOAISP) VALUES (?)");
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

    // FIX: thêm DIRECTORY_SEPARATOR để path đúng
    $logPath = __DIR__ . '/../../logs/category/add.txt';
    @file_put_contents($logPath,
        date('[Y-m-d H:i:s]') . ' [CATEGORIES/add] ' . $e->getMessage() . "\n",
        FILE_APPEND
    );
}
