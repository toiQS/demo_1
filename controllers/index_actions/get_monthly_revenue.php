<?php
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    $log = date('[Y-m-d H:i:s]') . " [$errno] $errstr in $errfile on line $errline\n";
    file_put_contents("logs\index\get_monthly_revenue.txt", $log, FILE_APPEND);
});

include_once "controllers\connectDB.php";

$sql = "SELECT SUM(THANHTIEN) AS doanhthu FROM hoadon WHERE TRANGTHAI = 3 AND NGAYMUA >= DATE_FORMAT(NOW(), '%Y-%m-01') AND NGAYMUA < DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 1 MONTH), '%Y-%m-01');";

try {
    $stmt = $conn->query($sql);
    $row  = $stmt->fetch_assoc();
    $result_monthly_revenue = $row['total'];

} catch (mysqli_sql_exception $e) {
    $result_monthly_revenue = 0; 
}