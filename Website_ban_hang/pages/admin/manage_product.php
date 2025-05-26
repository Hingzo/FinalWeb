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

// Xử lý tìm kiếm
$searchKeyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$products = $searchKeyword ? Product::search($db, $searchKeyword) : Product::getAll($db);
$categories = Category::getAll($db);

// Tạo mảng danh mục để tra cứu
$categoryMap = [];
foreach ($categories as $category) {
    $categoryMap[$category->getId()] = $category->getName();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LE.GICARFT | Quản lý sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .header-bg {
            background: linear-gradient(135deg,rgb(255, 255, 255),rgb(255, 255, 255));
        }
        .sidebar {
            background-color: #5a9bb8;
            min-height: 100vh;
        }
        .sidebar .nav-link {
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px 0;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.2);
            color: white;
        }
        .product-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .btn-edit {
            background-color: #5a9bb8;
            border: none;
            color: white;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 12px;
        }
        .btn-delete {
            background-color: #dc3545;
            border: none;
            color: white;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 12px;
        }
        .search-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .tab-navigation {
            border-bottom: 2px solid #5a9bb8;
            margin-bottom: 20px;
        }
        .tab-navigation .nav-link {
            color: #666;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 15px 30px;
            font-weight: 500;
        }
        .tab-navigation .nav-link.active {
            color: #5a9bb8;
            border-bottom-color: #5a9bb8;
            background: none;
        }
        .logo-img {
            height: 70px;
            width: auto;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header-bg text-white py-3 shadow-sm">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <img src="logo.png" alt="LE.GICARFT Logo" class="logo-img">
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Search bar in header if needed -->
                </div>
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
                            <i class="fas fa-bars me-2"></i>
                            Chức năng quản lý
                        </button>
                        <ul class="dropdown-menu w-100">
                            <li><a class="dropdown-item" href="manage_order.php">Quản lý đơn hàng</a></li>
                            <li><a class="dropdown-item active" href="manage_product.php">Quản lý sản phẩm</a></li>
                            <li><a class="dropdown-item" href="manage_revenue.php">Thống kê doanh thu</a></li>
                            <li><a class="dropdown-item" href="manage_category.php">Quản lý danh mục</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs tab-navigation">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" onclick="showAllProducts()">Tất cả sản phẩm</a>
                    </li>
                </ul>

                <!-- Search Section -->
                <div class="search-section">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <form method="GET" class="d-flex">
                                <input type="text" name="search" class="form-control me-2" 
                                       placeholder="Nhập mã sản phẩm" 
                                       value="<?php echo htmlspecialchars($searchKeyword); ?>">
                                <button type="submit" class="btn btn-info text-white">Tìm kiếm</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="product-table p-4">
                    <?php if (empty($products)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                            <p class="text-muted">Không có sản phẩm nào được tìm thấy</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <a href="add.php" class="btn btn-success mb-3"><i class="fas fa-plus"></i> Thêm sản phẩm</a>
                            <table class="table table-striped product-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên</th>
                                        <th>Giá</th>
                                        <th>Danh mục</th>
                                        <th>Mô tả</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td><?= $product->getId() ?></td>
                                            <td><?= htmlspecialchars($product->getName()) ?></td>
                                            <td><?= number_format($product->getPrice(), 0, ',', '.') ?></td>
                                            <td><?= $categoryMap[$product->getCategoryId()] ?? 'Không rõ' ?></td>
                                            <td><?= htmlspecialchars($product->getDescription()) ?></td>
                                            <td>
                                                <a href="edit.php?id=<?= $product->getId() ?>" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit"></i> Sửa
                                                </a>
                                                <button class="btn btn-danger btn-sm btn-delete" data-id="<?= $product->getId() ?>">
                                                    <i class="fas fa-trash"></i> Xóa
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Success Alert -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 11">
        <div id="successAlert" class="alert alert-success alert-dismissible fade" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <span id="successMessage"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
     <div class="text-center mt-4">
                    <a href="dashboard.php" class="btn custom-btn">
                        <i class="fas fa-tachometer-alt me-2"></i>Về Dashboard
                    </a>
                </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showAllProducts() {
            window.location.href = 'manage_product.php';
        }

        function showNewestProducts() {
            alert('Chức năng sắp xếp theo mới nhất đang được phát triển');
        }

        function showOldestProducts() {
            alert('Chức năng sắp xếp theo cũ nhất đang được phát triển');
        }

        // Check for success message from URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('success') === 'added') {
            showSuccess('Đã thêm sản phẩm thành công!');
        } else if (urlParams.get('success') === 'updated') {
            showSuccess('Đã cập nhật sản phẩm thành công!');
        }

        function showSuccess(message) {
            document.getElementById('successMessage').textContent = message;
            const alert = document.getElementById('successAlert');
            alert.classList.add('show');
        }

        // Xử lý xóa sản phẩm
       // Thay thế phần JavaScript xử lý delete trong manage_product.php
document.addEventListener("DOMContentLoaded", function () {
    const deleteButtons = document.querySelectorAll(".btn-delete");

    deleteButtons.forEach(button => {
        button.addEventListener("click", function () {
            const id = this.getAttribute("data-id");

            if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này?")) {
                // Disable button
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xóa...';
                
                // Tạo form data
                const formData = new FormData();
                formData.append('id_sanpham', id);

                fetch('delete.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccess(data.message);
                        // Reload trang sau 1 giây
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        alert('Lỗi: ' + data.message);
                        // Khôi phục button
                        this.disabled = false;
                        this.innerHTML = '<i class="fas fa-trash"></i> Xóa';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi xóa sản phẩm');
                    // Khôi phục button
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-trash"></i> Xóa';
                });
            }
        });
    });
});
    </script>
</body>
</html>