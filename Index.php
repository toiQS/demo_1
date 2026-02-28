<?php
// index.php  (root — login page)
session_start();

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}

// Redirect if already logged in
if (isset($_SESSION['idTK'])) {
    header('Location: views/dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'database/db.php'; // your DB connection file

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Vui lòng nhập đầy đủ thông tin.';
    } else {
        $stmt = $pdo->prepare("
            SELECT tk.*, ut.Ten AS tenLoai
            FROM taikhoan tk
            JOIN usertype ut ON tk.PHANLOAI = ut.idType
            WHERE tk.USERNAME = ? AND tk.TRANGTHAI = 1
        ");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['PASSWORD'])) {
            if ($user['PHANLOAI'] != 2) {
                $error = 'Tài khoản không có quyền truy cập.';
            } else {
                $_SESSION['idTK']    = $user['idTK'];
                $_SESSION['username'] = $user['USERNAME'];
                $_SESSION['hoten']   = $user['HOTEN'];
                $_SESSION['role']    = $user['PHANLOAI'];
                header('Location: views/dashboard.php');
                exit();
            }
        } else {
            $error = 'Tên đăng nhập hoặc mật khẩu không đúng.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng nhập — CH Di Động Admin</title>
  <link rel="stylesheet" href="views/assets/css/admin.css">
</head>
<body class="login-page">

  <div class="login-box">
    <div class="login-title">CH DI ĐỘNG</div>
    <div class="login-sub">ADMIN CONSOLE · ĐĂNG NHẬP</div>

    <?php if ($error): ?>
      <div class="login-error show"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php">
      <div class="form-group">
        <label class="form-label" for="username">Tên đăng nhập</label>
        <input class="form-input" type="text" id="username" name="username"
               placeholder="Nhập username..." autocomplete="username"
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="password">Mật khẩu</label>
        <input class="form-input" type="password" id="password" name="password"
               placeholder="••••••••" autocomplete="current-password">
      </div>
      <button type="submit" class="btn-primary">ĐĂNG NHẬP</button>
    </form>
  </div>

  <script src="views/assets/js/admin.js"></script>
</body>
</html>
