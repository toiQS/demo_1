<?php

$min_limit = 10;

include_once "controllers\connectDB.php";
 
$sql = "SELECT COUNT(*) FROM taikhoan";

try{
    $result_user_count = $conn->query($sql);

} catch (mysqli_sql_exception $e){
    $file = fopen("logs\index\get_count_users.txt","w");
    fwrite($file,$e->getMessage());
    fclose($file);
}
$row = $result_user_count->fetch_assoc();

echo $row['total'];