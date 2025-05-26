<?php
require_once '../../classes/Database.php';
require_once '../../classes/Category.php';
require_once '../../config/db_config.php';

session_start();
$db = new Database($host, $username, $password, 'legicaft');


// Kiểm tra quyền admin
if (!isset($_SESSION['id_nguoidung']) || $_SESSION['vaitro'] != 'admin') {
    header("Location: ../user/home.php");
    exit();
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description'] ?? '');
    $quantity = 0; // Giá trị mặc định, có thể cập nhật sau

    if (!empty($name)) {
        $category = new Category(null, $name, $quantity, $description);
        if ($category->save($db)) {
            header("Location: manage_category.php?success=added");
            exit();
        } else {
            $message = "Lỗi khi thêm danh mục: " . $db->getConnection()->error;
        }
    } else {
        $message = "Tên danh mục không được để trống!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LE.GICARFT | Thêm danh mục</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .header-bg { background: linear-gradient(135deg, #d4a574, #b8956a); }
        .sidebar { background-color: #5a9bb8; min-height: 100vh; }
        .sidebar .nav-link { color: white; padding: 12px 20px; border-radius: 8px; margin: 5px 0; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(255,255,255,0.2); color: white; }
        .content { background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 20px; }
    </style>
</head>
<body class="bg-light">
    <!-- Header -->
    <header class="header-bg text-white py-3 shadow-sm">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <img src="logo.png" alt="LE.GICARFT Logo" class="logo-img">
                    </div>
                </div>
                <div class="col-md-6"></div>
                <div class="col-md-3 text-end">
                    <div class="d-flex align-items-center justify-content-end">
                        <div class="me-3">
                            <small>Tài khoản</small>
                            <div><strong><?php echo htmlspecialchars($_SESSION['hoten']); ?></strong></div>
                        </div>
                        <div class="bg-white rounded-circle p-2">
                            <i class="fas fa-user text-muted"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="p-3">
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle d-flex align-items-center w-100" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bars me-2"></i> Chức năng quản lý
                        </button>
                        <ul class="dropdown-menu w-100">
                            <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
                               <li><a class="dropdown-item" href="manage_order.php">Quản lý đơn hàng</a></li>
                            <li><a class="dropdown-item" href="manage_product.php">Quản lý sản phẩm</a></li>
                            <li><a class="dropdown-item" href="manage_revenue.php">Thống kê doanh thu</a></li>
                            <li><a class="dropdown-item active" href="manage_category.php">Quản lý danh mục</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="content">
                    <h2>Thêm danh mục mới</h2>
                    <?php if ($message): ?>
                        <div class="alert alert-danger"><?php echo $message; ?></div>
                    <?php endif; ?>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên danh mục</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="description" name="description"></textarea>
                        </div>
                        <button type="submit" name="add_category" class="btn btn-success">Thêm danh mục</button>
                        <a href="manage_category.php" class="btn btn-secondary">Quay lại</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .logo-img { height: 40px; width: auto; object-fit: contain; }
    </style>
</body>
</html>