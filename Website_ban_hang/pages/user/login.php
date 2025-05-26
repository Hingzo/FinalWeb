<?php
require_once '../../classes/Database.php';
require_once '../../classes/User.php';
require_once '../../config/db_config.php'; // Đảm bảo tải file cấu hình

session_start();
$user = new User($host, $username, $password, $dbname); // Truyền các biến từ db_config.php

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) {
        if ($user->login($email, $password)) {
            $_SESSION['id_nguoidung'] = $user->getId();
            $_SESSION['hoten'] = $user->getHoten();
            $_SESSION['vaitro'] = $user->getVaitro();
            header("Location: ../../index.php");
            exit();
        } else {
            $error = "Email hoặc mật khẩu không đúng!";
        }
    } else {
        $error = "Vui lòng nhập đầy đủ email và mật khẩu!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LE.GICARFT | Đăng nhập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="topbar">
        <div class="logo">
            <img src="../../assets/images/logo.png" alt="Le Gicart Logo">
        </div>
        </div>

        <div class="content">
            <div class="login-modal">
                <h3>Đăng nhập tài khoản</h3>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="POST" action="login.php">
                    <label>Chưa có tài khoản?</label>
                    <a href="register.php" style="color: #4f92a5; margin-bottom: 15px; display: block;">Đăng ký</a>
                    <label>Email</label>
                    <input type="email" name="email" id="email" placeholder="Nhập địa chỉ Email" required>
                    <label>Mật khẩu</label>
                    <input type="password" name="password" id="password" placeholder="Nhập mật khẩu" required>
                    <button type="submit">Đăng nhập</button>
                </form>
            </div>
        </div>
    </div>
<div class="container">
    <p class="mt-3 text-center"><a href="home.php" class="btn btn-secondary">Quay lại trang chủ</a></p>
  </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>