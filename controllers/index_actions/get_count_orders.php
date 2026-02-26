<?php
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    $log = date('[Y-m-d H:i:s]') . " [$errno] $errstr in $errfile on line $errline\n";
    file_put_contents("logs\index\get_count_orders.txt", $log, FILE_APPEND);
});

include_once "controllers\connectDB.php";

$sql = "SELECT COUNT(*) AS total FROM hoadon ";

try {
    $stmt = $conn->query($sql);
    $row  = $stmt->fetch_assoc();
    $result_order_count = $row['total'];

} catch (mysqli_sql_exception $e) {
    $result_order_count = 0; 
}