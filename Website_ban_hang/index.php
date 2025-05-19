<?php
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'config/db_config.php';

session_start();
$db = new Database($host, $username, $password, $dbname);

// Nếu là admin, chuyển hướng ngay lập tức đến dashboard
if (isset($_SESSION['id_nguoidung']) && $_SESSION['vaitro'] == 'admin') {
    header("Location: pages/admin/dashboard.php");
    exit();
}

// Nếu đã đăng nhập, chuyển hướng đến home.php
if (isset($_SESSION['id_nguoidung'])) {
    header("Location: pages/user/my-order.php");
    exit();
} else {
    // Nếu chưa đăng nhập, chuyển hướng đến login.php
    header("Location: pages/user/home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LE.GICARFT | Trang chủ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>
    <!-- Nội dung này sẽ không hiển thị vì đã chuyển hướng -->
    <p>Đang chuyển hướng...</p>
</body>
</html>