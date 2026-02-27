<?php

include_once __DIR__. '/connectDB.php';

header("Content-Type: application/json");


$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) 
{
    default:
    
}


?>