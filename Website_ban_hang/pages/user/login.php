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
</head>
<body>
    <header class="bg-light py-2 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="logo">
                <img src="../../assets/images/logo.png" alt="Logo LE.GICARFT" height="50">
            </div>
            <div class="user-cart">
                <a href="../../index.php" class="btn btn-secondary">Quay lại trang chủ</a>
            </div>
        </div>
    </header>

    <section class="login py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white text-center">
                            <h3 class="mb-0">Đăng nhập</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            <form method="POST" action="login.php">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" id="email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mật khẩu</label>
                                    <input type="password" name="password" id="password" class="form-control" required>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Đăng nhập</button>
                                </div>
                            </form>
                            <p class="mt-3 text-center">Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>