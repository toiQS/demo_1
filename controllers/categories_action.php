<?php
/**
 * categories_action.php
 * JSON API cho trang quản lý danh mục
 *
 * POST body (JSON): { action, id?, name?, desc?, status? }
 * Response        : { success, message, ... }
 *
 * Lưu ý schema thực tế: danhmuc(idDM, LOAISP)
 *   — desc  : không có cột MOTA  → bị bỏ qua
 *   — status: không có cột TRANGTHAI → toggle không khả dụng
 */

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$raw  = file_get_contents('php://input');
$body = json_decode($raw, true);

if (!is_array($body)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Request body không hợp lệ.']);
    exit;
}

$action = strtolower(trim($body['action'] ?? ''));

// Chỉ extract những gì DB thực sự dùng được
$id   = isset($body['id'])   ? (int) $body['id']   : 0;
$name = isset($body['name']) ? trim($body['name'])  : '';

// desc & status được nhận nhưng DB chưa có cột tương ứng — bỏ qua
// (giữ lại để frontend không cần thay đổi)

$result = ['success' => false, 'message' => 'Action không hợp lệ.'];

switch ($action) {

    case 'add':
        require 'services\category\add.php';
        break;

    case 'edit':
        require 'services\category\edit.php';
        break;

    case 'toggle':
        require 'services\category\toggle.php';
        break;

    case 'delete':
        require 'services\category\remove.php';
        break;
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
exit;