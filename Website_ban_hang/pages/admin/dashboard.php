<?php
require_once '../../classes/Database.php';
require_once '../../classes/Product.php';
require_once '../../classes/Category.php';
require_once '../../config/db_config.php';

session_start();
$db = new Database($host, $username, $password, $dbname);

// Kiểm tra quyền admin
if (!isset($_SESSION['id_nguoidung']) || $_SESSION['vaitro'] != 'admin') {
    header("Location: ../user/home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LE.GICARFT | Quản trị</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-dark text-white py-2 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="logo">
                <h3>LE.GICARFT Admin</h3>
            </div>
            <div class="user-cart">
                <span class="text-white">Xin chào, <?php echo htmlspecialchars($_SESSION['hoten']); ?>!</span>
                <a href="../user/logout.php" class="btn btn-outline-light ms-2">Đăng xuất</a>
            </div>
        </div>
    </header>

    <div class="container mt-4">
        <div class="row">
            <main class="col-md-12 text-center">
                <section class="admin-dashboard">
                    <h2 class="mb-4">Bảng điều khiển</h2>
                    <p class="lead">Đây là giao diện admin. Vui lòng chờ các thành viên khác phát triển chức năng và giao diện chi tiết.</p>
                </section>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>