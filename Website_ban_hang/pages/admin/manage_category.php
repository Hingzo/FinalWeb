<?php
require_once '../../classes/Database.php';
require_once '../../classes/Category.php';
require_once '../../config/db_config.php';

session_start();
$db = new Database($host, $username, $password, $dbname);

// Kiểm tra quyền admin
if (!isset($_SESSION['id_nguoidung']) || $_SESSION['vaitro'] != 'admin') {
    header("Location: ../user/home.php");
    exit();
}

$categories = Category::getAll($db);

if (isset($_GET['success']) && $_GET['success'] === 'added') {
    $successMessage = "Đã thêm danh mục thành công!";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LE.GICARFT | Quản lý danh mục</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .header-bg { background: linear-gradient(135deg,rgb(238, 236, 236),rgb(255, 255, 255)); }
        .sidebar { background-color: #5a9bb8; min-height: 100vh; }
        .sidebar .nav-link { color: white; padding: 12px 20px; border-radius: 8px; margin: 5px 0; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(255,255,255,0.2); color: white; }
        .category-table { background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .btn-edit { background-color: #5a9bb8; border: none; color: white; padding: 5px 12px; border-radius: 5px; font-size: 12px; }
        .btn-delete { background-color: #dc3545; border: none; color: white; padding: 5px 12px; border-radius: 5px; font-size: 12px; }
        .btn-add { background-color: #28a745; border: none; color: white; padding: 8px 20px; border-radius: 5px; }
        .search-section { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
    </style>
</head>
<body class="bg-light">
    <!-- Header -->
    <header class="header-bg text-white py-3 shadow-sm">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <img src="../../assets/images/logo.png" alt="LE.GICARFT Logo" width="100" height="60">
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
                <div class="search-section">
                    <div class="row align-items-center">
                        <div class="col-md-8"></div>
                        <div class="col-md-4 text-end">
                            <a href="add_category.php" class="btn btn-add">Thêm danh mục</a>
                        </div>
                    </div>
                </div>

                <div class="category-table p-4">
                    <?php if (empty($categories)): ?>
                        <div class="text-center py-5">
                            <p class="text-muted">Không có danh mục nào được tìm thấy</p>
                        </div>
                    <?php else: ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Mã số</th>
                                    <th>Tên danh mục</th>
                                    <th>Số lượng sản phẩm</th>
                                    <th>Mô tả</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): 
                                    // Lưu giá trị ID vào biến
                                    $categoryId = $category->getId();
                                    // Đếm số lượng sản phẩm liên kết
                                    $countStmt = $db->getConnection()->prepare("SELECT COUNT(*) FROM tbl_sanpham WHERE id_danhmuc = ?");
                                    $countStmt->bind_param("i", $categoryId);
                                    $countStmt->execute();
                                    $countStmt->bind_result($productCount);
                                    $countStmt->fetch();
                                    $countStmt->close();
                                ?>
                                    <tr>
                                        <td><?php echo $category->getId(); ?></td>
                                        <td><?php echo htmlspecialchars($category->getName()); ?></td>
                                        <td><?php echo $productCount; ?></td>
                                        <td><?php echo htmlspecialchars($category->getDescription()); ?></td>
                                        <td>
                                           
                                            <button class="btn btn-delete" data-id="<?php echo $category->getId(); ?>">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                <?php if (isset($successMessage)): ?>
                    <div class="alert alert-success mt-3"><?php echo $successMessage; ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
 <div class="text-center mt-4">
                    <a href="dashboard.php" class="btn custom-btn">
                        <i class="fas fa-tachometer-alt me-2"></i>Về Dashboard
                    </a>
                </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const deleteButtons = document.querySelectorAll(".btn-delete");

            deleteButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const id = this.getAttribute("data-id");

                    if (confirm("Bạn có chắc chắn muốn xóa danh mục này?")) {
                        fetch("delete_category.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: "id=" + encodeURIComponent(id)
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                                location.reload();
                            } else {
                                alert("Lỗi: " + data.message);
                            }
                        })
                        .catch(error => {
                            alert("Có lỗi xảy ra: " + error.message);
                        });
                    }
                });
            });
        });
    </script>
    <style>
        .logo-img { height: 70px; width: auto; object-fit: contain; }
    </style>
</body>
</html>