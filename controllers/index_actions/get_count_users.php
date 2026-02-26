<?php

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    $log = date('[Y-m-d H:i:s]') . " [$errno] $errstr in $errfile on line $errline\n";
    file_put_contents("logs\index\get_count_users.txt", $log, FILE_APPEND);
});

$min_limit = 10;

include_once "controllers\connectDB.php";

// ✅ Thêm AS total để fetch_assoc() lấy đúng key
$sql = "SELECT COUNT(*) AS total FROM taikhoan ";

try {
    $stmt = $conn->query($sql);
    $row  = $stmt->fetch_assoc();

    // ✅ Gán giá trị số vào biến, không echo ở đây
    $result_user_count = $row['total'];

} catch (mysqli_sql_exception $e) {
    $result_user_count = 0; // fallback tránh lỗi undefined
}