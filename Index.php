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

// ============================================================
//  HÀM KIỂM TRA DỮ LIỆU ĐẦU VÀO (Server-side)
// ============================================================
function validateUsername(string $username): string
{
    if ($username === '') {
        return 'Tên đăng nhập không được để trống.';
    }
    if (mb_strlen($username) < 3) {
        return 'Tên đăng nhập phải có ít nhất 3 ký tự.';
    }
    if (mb_strlen($username) > 30) {
        return 'Tên đăng nhập không được vượt quá 30 ký tự.';
    }
    // Chỉ cho phép chữ cái (a-z, A-Z), số (0-9) và dấu gạch dưới (_)
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        return 'Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới (_).';
    }
    return '';
}

function validatePassword(string $password): string
{
    if ($password === '') {
        return 'Mật khẩu không được để trống.';
    }
    if (mb_strlen($password) < 6) {
        return 'Mật khẩu phải có ít nhất 6 ký tự.';
    }
    if (mb_strlen($password) > 255) {
        return 'Mật khẩu không được vượt quá 255 ký tự.';
    }
    return '';
}

// ============================================================
//  XỬ LÝ ĐĂNG NHẬP
// ============================================================
$errors      = [];   // lỗi từng trường
$globalError = '';   // lỗi chung (sai mật khẩu, không quyền…)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'database/db.php';

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // ===== DEBUG: Kiểm tra dữ liệu đầu vào =====
    echo '<pre style="
        background:#0d0d0d;
        color:#00ff99;
        border:1px solid #00ff99;
        padding:14px 18px;
        border-radius:8px;
        font-size:13px;
        font-family:monospace;
        margin:16px auto;
        max-width:480px;
    ">';
    echo "📥 [DEBUG] Dữ liệu POST nhận được:\n";
    echo "──────────────────────────────────\n";
    echo "  USERNAME  : " . htmlspecialchars($username) . "\n";
    echo "  PASSWORD  : " . htmlspecialchars($password) . "\n";
    echo "  Độ dài UN : " . mb_strlen($username) . " ký tự\n";
    echo "  Độ dài PW : " . mb_strlen($password) . " ký tự\n";
    echo "──────────────────────────────────\n";
    echo "  \$_POST raw:\n";
    foreach ($_POST as $key => $val) {
        echo "    [$key] => " . htmlspecialchars($val) . "\n";
    }
    echo '</pre>';
    // ===== END DEBUG =====

    // --- Kiểm tra từng trường ---
    $errUser = validateUsername($username);
    $errPass = validatePassword($password);

    if ($errUser) $errors['username'] = $errUser;
    if ($errPass) $errors['password'] = $errPass;

    // --- Chỉ truy vấn DB khi không có lỗi validation ---
    if (empty($errors)) {
        $stmt = $pdo->prepare("
            SELECT * FROM taikhoan
            WHERE USERNAME = ?
            AND TRANGTHAI = 1
        ");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Hỗ trợ cả mật khẩu plain-text lẫn bcrypt
        $passwordOk = false;
        if ($user) {
            $algoInfo = password_get_info($user['PASSWORD']);
            $isBcrypt = !empty($algoInfo['algo']); // null hoặc 0 = plain text

            // ===== DEBUG: Kiểm tra quá trình so sánh mật khẩu =====
            echo '<pre style="background:#0d0d0d;color:#ffcc00;border:1px solid #ffcc00;padding:14px 18px;border-radius:8px;font-size:13px;font-family:monospace;margin:16px auto;max-width:480px;">';
            echo "🔐 [DEBUG] Kiểm tra mật khẩu:\n";
            echo "──────────────────────────────────\n";
            echo "  User tìm thấy  : " . ($user ? 'CÓ' : 'KHÔNG') . "\n";
            echo "  DB PASSWORD    : " . htmlspecialchars($user['PASSWORD']) . "\n";
            echo "  INPUT password : " . htmlspecialchars($password) . "\n";
            echo "  algo (PHP)     : " . var_export($algoInfo['algo'], true) . "\n";
            echo "  Là bcrypt?     : " . ($isBcrypt ? 'CÓ' : 'KHÔNG — dùng so sánh thẳng') . "\n";
            echo "  So sánh bằng   : " . ($password === $user['PASSWORD'] ? '✅ KHỚP' : '❌ KHÔNG KHỚP') . "\n";
            echo "  password_verify: " . (password_verify($password, $user['PASSWORD']) ? '✅ ĐÚNG' : '❌ SAI') . "\n";
            echo '</pre>';
            // ===== END DEBUG =====

            if ($isBcrypt) {
                $passwordOk = password_verify($password, $user['PASSWORD']);
            } else {
                $passwordOk = ($password === $user['PASSWORD']);
            }
        }

        if ($user && $passwordOk) {
            if ($user['PHANLOAI'] != 2) {
                $globalError = 'Tài khoản không có quyền truy cập hệ thống quản lý.';
            } else {
                $_SESSION['idTK']     = $user['idTK'];
                $_SESSION['username'] = $user['USERNAME'];
                $_SESSION['hoten']    = $user['HOTEN'];
                $_SESSION['role']     = $user['PHANLOAI'];
                header('Location: views/dashboard.php');
                exit();
            }
        } else {
            $globalError = 'Tên đăng nhập hoặc mật khẩu không đúng.';
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
    <style>
        .field-error {
            color: #ef4444;
            font-size: 0.78rem;
            margin-top: 4px;
            display: block;
            min-height: 1.1em;
        }
        .form-input.is-invalid { border-color: #ef4444 !important; }
        .form-input.is-valid   { border-color: #22c55e !important; }
    </style>
</head>

<body class="login-page">

    <div class="login-box">
        <div class="login-title">CH DI ĐỘNG</div>
        <div class="login-sub">ADMIN CONSOLE · ĐĂNG NHẬP</div>

        <?php if ($globalError): ?>
            <div class="login-error show"><?= htmlspecialchars($globalError) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php" id="loginForm" novalidate>

            <!-- USERNAME -->
            <div class="form-group">
                <label class="form-label" for="username">Tên đăng nhập</label>
                <input
                    class="form-input <?= isset($errors['username']) ? 'is-invalid' : (isset($_POST['username']) && $_POST['username'] !== '' && !isset($errors['username']) ? 'is-valid' : '') ?>"
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Nhập username..."
                    autocomplete="username"
                    maxlength="30"
                    value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                <span class="field-error" id="username-error">
                    <?= isset($errors['username']) ? htmlspecialchars($errors['username']) : '' ?>
                </span>
            </div>

            <!-- PASSWORD -->
            <div class="form-group">
                <label class="form-label" for="password">Mật khẩu</label>
                <input
                    class="form-input <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                    type="password"
                    id="password"
                    name="password"
                    placeholder="••••••••"
                    autocomplete="current-password"
                    maxlength="255">
                <span class="field-error" id="password-error">
                    <?= isset($errors['password']) ? htmlspecialchars($errors['password']) : '' ?>
                </span>
            </div>

            <button type="submit" class="btn-primary" id="submitBtn">ĐĂNG NHẬP</button>
        </form>
    </div>

    <script src="views/assets/js/admin.js"></script>
    <script>
    // ============================================================
    //  VALIDATION PHÍA CLIENT (JavaScript) — phải khớp với PHP
    // ============================================================
    const form       = document.getElementById('loginForm');
    const usernameEl = document.getElementById('username');
    const passwordEl = document.getElementById('password');
    const userErrEl  = document.getElementById('username-error');
    const passErrEl  = document.getElementById('password-error');
    const submitBtn  = document.getElementById('submitBtn');

    function validateUsernameJS(v) {
        v = v.trim();
        if (v === '')              return 'Tên đăng nhập không được để trống.';
        if (v.length < 3)         return 'Tên đăng nhập phải có ít nhất 3 ký tự.';
        if (v.length > 30)        return 'Tên đăng nhập không được vượt quá 30 ký tự.';
        if (!/^[a-zA-Z0-9_]+$/.test(v))
                                   return 'Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới (_).';
        return '';
    }

    function validatePasswordJS(v) {
        if (v === '')       return 'Mật khẩu không được để trống.';
        if (v.length < 6)  return 'Mật khẩu phải có ít nhất 6 ký tự.';
        if (v.length > 255) return 'Mật khẩu không được vượt quá 255 ký tự.';
        return '';
    }

    function applyFieldState(inputEl, errorEl, message) {
        errorEl.textContent = message;
        if (message) {
            inputEl.classList.add('is-invalid');
            inputEl.classList.remove('is-valid');
        } else {
            inputEl.classList.remove('is-invalid');
            inputEl.classList.add('is-valid');
        }
    }

    // Validate khi rời khỏi field
    usernameEl.addEventListener('blur', () =>
        applyFieldState(usernameEl, userErrEl, validateUsernameJS(usernameEl.value)));

    passwordEl.addEventListener('blur', () =>
        applyFieldState(passwordEl, passErrEl, validatePasswordJS(passwordEl.value)));

    // Validate realtime khi đang gõ
    usernameEl.addEventListener('input', () => {
        if (usernameEl.value !== '')
            applyFieldState(usernameEl, userErrEl, validateUsernameJS(usernameEl.value));
    });

    passwordEl.addEventListener('input', () => {
        if (passwordEl.value !== '')
            applyFieldState(passwordEl, passErrEl, validatePasswordJS(passwordEl.value));
    });

    // Chặn submit nếu còn lỗi
    form.addEventListener('submit', function (e) {
        const errU = validateUsernameJS(usernameEl.value);
        const errP = validatePasswordJS(passwordEl.value);

        applyFieldState(usernameEl, userErrEl, errU);
        applyFieldState(passwordEl, passErrEl, errP);

        if (errU || errP) {
            e.preventDefault();
            return;
        }

        // Hiển thị trạng thái loading
        submitBtn.disabled    = true;
        submitBtn.textContent = 'ĐANG XỬ LÝ...';
    });
    </script>
</body>

</html>