<?php
session_start();
require_once '../../classes/Database.php';
require_once '../../classes/User.php';
require_once '../../config/db_config.php';

// Khởi tạo đối tượng User
$user = new User($host, $username, $password, $dbname);

// Biến thông báo lỗi và thành công
$message = '';
$success = false;

// Xử lý form khi submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hoten = trim($_POST['hoten']);
    $gioitinh = trim($_POST['gioitinh']);
    $sdt = trim($_POST['sdt']);
    $email = trim($_POST['email']);
    $matkhau = trim($_POST['matkhau']);
    $diachi = trim($_POST['diachi']);

    // Validate dữ liệu
    if (empty($hoten) || empty($email) || empty($matkhau) || empty($sdt)) {
        $message = "Vui lòng điền đầy đủ thông tin!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Email không hợp lệ!";
    } else {
        // Sử dụng phương thức register từ class User
        $result = $user->register($hoten, $gioitinh, $sdt, $email, $matkhau, $diachi);

        if ($result === true) {
            $success = true;
            $message = "Đăng ký thành công!";
            $_POST = array(); // Xóa dữ liệu form
        } else {
            $message = $result; // Hiển thị thông báo lỗi từ phương thức register
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LE.GICARFT | Đăng ký</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="topbar">
        <div class="logo">
            <img src="../../assets/images/logo.png" alt="Le Gicart Logo">
        </div>
        <div class="search">
            <input type="text" placeholder="Nhập sản phẩm cần tìm kiếm">
            <button>Tìm kiếm</button>
        </div>
        <div class="user-cart">
        </div>
    </div>

    <div class="main-container">
        <div class="sidebar">
            <h3>Danh mục sản phẩm</h3>
            <ul class="sidebar-menu">
                <li>HOTWHEELS</li>
                <li>MINI GT</li>
                <li>TARMACWORKS</li>
                <li>BABY CRY</li>
                <li>LABUBU</li>
                <li>BABY THREE</li>
            </ul>
        </div>
        <div class="register-content"> <!-- Thay đổi từ .content thành .register-content -->
            <div class="register-modal">
                <h3>Đăng ký tài khoản</h3>
                <?php if ($message): ?>
                    <div class="alert <?php echo $success ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="register.php" class="needs-validation" novalidate>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        <div class="invalid-feedback">Vui lòng nhập email hợp lệ!</div>
                    </div>
                    <div class="form-group">
                        <label for="hoten">Tên khách hàng</label>
                        <input type="text" class="form-control" id="hoten" name="hoten" value="<?php echo htmlspecialchars($_POST['hoten'] ?? ''); ?>" required>
                        <div class="invalid-feedback">Vui lòng nhập tên khách hàng!</div>
                    </div>
                    <div class="form-group">
                        <label for="matkhau">Mật khẩu</label>
                        <input type="password" class="form-control" id="matkhau" name="matkhau" required>
                        <div class="invalid-feedback">Vui lòng nhập mật khẩu!</div>
                    </div>
                    <div class="form-group">
                        <label for="sdt">Số điện thoại</label>
                        <input type="text" class="form-control" id="sdt" name="sdt" value="<?php echo htmlspecialchars($_POST['sdt'] ?? ''); ?>" required>
                        <div class="invalid-feedback">Vui lòng nhập số điện thoại!</div>
                    </div>
                    <div class="form-group">
                        <label for="diachi">Địa chỉ</label>
                        <textarea class="form-control" id="diachi" name="diachi" rows="3"><?php echo htmlspecialchars($_POST['diachi'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Giới tính</label>
                        <div>
                            <input type="radio" name="gioitinh" id="nam" value="Nam" <?php echo (isset($_POST['gioitinh']) && $_POST['gioitinh'] == 'Nam') ? 'checked' : ''; ?> required>
                            <label for="nam">Nam</label>
                            <input type="radio" name="gioitinh" id="nu" value="Nữ" <?php echo (isset($_POST['gioitinh']) && $_POST['gioitinh'] == 'Nữ') ? 'checked' : ''; ?>>
                            <label for="nu">Nữ</label>
                        </div>
                    </div>
                    <button type="submit" class="btn-register">Đăng ký</button>
                </form>
                <p class="text-center mt-3">Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Bootstrap validation
        (function () {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html>