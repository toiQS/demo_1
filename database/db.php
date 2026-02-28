<?php
// database/db.php

define('DB_HOST', 'localhost');
define('DB_NAME', 'chdidong');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHAR', 'utf8mb4');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHAR,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('<div style="font-family:monospace;background:#1a0000;color:#ef4444;padding:20px;border-radius:8px;margin:20px">
        <b>Lỗi kết nối database:</b><br>' . htmlspecialchars($e->getMessage()) . '
    </div>');
}