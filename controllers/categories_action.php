<?php
/**
 * controllers/categories_action.php
 * JSON API cho trang quản lý danh mục
 *
 * FIX: Tất cả require dùng __DIR__ để path tuyệt đối,
 *      không phụ thuộc vào CWD khi gọi qua AJAX.
 */

ini_set('display_errors', 0);
error_reporting(E_ALL);

// Bắt mọi output lỡ xảy ra trước header
ob_start();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$raw  = file_get_contents('php://input');
$body = json_decode($raw, true);

if (!is_array($body)) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Request body không hợp lệ.']);
    exit;
}

$action = strtolower(trim($body['action'] ?? ''));
$id     = isset($body['id'])   ? (int)  $body['id']   : 0;
$name   = isset($body['name']) ? trim($body['name'])   : '';

$result = ['success' => false, 'message' => 'Action không hợp lệ.'];

// FIX: dùng __DIR__ . '/../services/category/...' thay vì path tương đối sai
switch ($action) {
    case 'add':
        require __DIR__ . '/../services/category/add.php';
        break;

    case 'edit':
        // FIX: file edit.php đã được tạo (trước đây không tồn tại)
        require __DIR__ . '/../services/category/edit.php';
        break;

    case 'toggle':
        require __DIR__ . '/../services/category/toggle.php';
        break;

    case 'delete':
        require __DIR__ . '/../services/category/remove.php';
        break;

    default:
        // Ghi log action không xác định
        @file_put_contents(__DIR__ . '/../logs/index/dashboard.text',
            date('[Y-m-d H:i:s]') . " [CATEGORIES/action] Unknown action: $action\n",
            FILE_APPEND
        );
        break;
}

// Dọn sạch mọi output lỡ (warning, notice...) rồi mới echo JSON
ob_end_clean();
echo json_encode($result, JSON_UNESCAPED_UNICODE);
exit;
