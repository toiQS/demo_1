<?php

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    $log = date('[Y-m-d H:i:s]') . " [$errno] $errstr in $errfile on line $errline\n";
    file_put_contents("logs\index\get_count_products.txt", $log, FILE_APPEND);
});

$min_limit = 10;

include_once "controllers\connectDB.php";

$sql = "SELECT COUNT(*) AS total FROM sanpham";

try {
    $stmt = $conn->query($sql);
    $row  = $stmt->fetch_assoc();
    $result_product_count = $row['total'];

} catch (mysqli_sql_exception $e) {
    $result_product_count = 0; 
}