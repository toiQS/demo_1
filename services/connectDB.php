<?php

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    $log = date('[Y-m-d H:i:s]') . " [$errno] $errstr in $errfile on line $errline\n";
    file_put_contents("logs\dbconnect.txt", $log, FILE_APPEND);
});

$host = "localhost";
$user = "root";
$pass = "";
$db   = "chdidong";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error)
{
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
