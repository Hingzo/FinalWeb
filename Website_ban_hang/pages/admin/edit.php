<?php
require_once '../../classes/Database.php';
require_once '../../classes/Product.php';
require_once '../../classes/Category.php';
require_once '../../config/db_config.php';

session_start();
$db = new Database($host, $username, $password, 'legicaft');

// Kiểm tra quyền admin
if (!isset($_SESSION['id_nguoidung']) || $_SESSION['vaitro'] != 'admin') {
    header("Location: ../user/home.php");
    exit();
}

$productId = intval($_GET['id'] ?? 0);
if ($productId <= 0) {
    header("Location: manage_product.php");
    exit();
}

$product = Product::getById($db, $productId);
if (!$product) {
    header("Location: manage_product.php?error=not_found");
    exit();
}

$categories = Category::getAll($db);
$error = '';
$success = '';

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $categoryId = intval($_POST['category_id'] ?? 0);
    
    // Xử lý upload hình ảnh mới (nếu có)
    $imagePath = $product->getImage(); // Giữ hình cũ
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/products/';
        
        // Tạo thư mục nếu chưa tồn tại
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($fileExtension, $allowedExtensions)) {
            $fileName = uniqid() . '.' . $fileExtension;
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                // Xóa hình cũ (nếu có)
                if ($product->getImage() && file_exists('../../' . $product->getImage())) {
                    unlink('../../' . $product->getImage());
                }
                $imagePath = 'uploads/products/' . $fileName;
            } else {
                $error = 'Không thể upload hình ảnh mới.';
            }
        } else {
            $error = 'Định dạng hình ảnh không được hỗ trợ. Vui lòng chọn file JPG, PNG hoặc GIF.';
        }
    }
    
    // Validate dữ liệu
    if (empty($error)) {
        if (empty($name)) {
            $error = 'Tên sản phẩm không được để trống.';
        } elseif ($price <= 0) {
            $error = 'Giá sản phẩm phải lớn hơn 0.';
        } elseif ($quantity < 0) {
            $error = 'Số lượng không được âm.';
        } elseif ($categoryId <= 0) {
            $error = 'Vui lòng chọn danh mục.';
        }
    }
    
    // Cập nhật sản phẩm trong database
    if (empty($error)) {
        try {
            $conn = $db->getConnection();
            $stmt = $conn->prepare("UPDATE tbl_sanpham SET tensanpham = ?, giasanpham = ?, soluongsanpham = ?, mota = ?, id_danhmuc = ?, hinh_anh = ? WHERE id_sanpham = ?");
            $stmt->bind_param("sdisisi", $name, $price, $quantity, $description, $categoryId, $imagePath, $productId);
            
            if ($stmt->execute()) {
                header("Location: manage_product.php?success=updated");
                exit();
            } else {
                $error = 'Có lỗi xảy ra khi cập nhật sản phẩm.';
            }
        } catch (Exception $e) {
            $error = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LE.GICARFT | Sửa sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .header-bg {
            background: linear-gradient(135deg, #d4a574, #b8956a);
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
        .form-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .form-control:focus {
            border-color: #5a9bb8;
            box-shadow: 0 0 0 0.2rem rgba(90, 155, 184, 0.25);
        }
        .btn-primary {
            background-color: #5a9bb8;
            border-color: #5a9bb8;
        }
        .btn-primary:hover {
            background-color: #4a8aa0;
            border-color: #4a8aa0;
        }
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.3s;
        }
        .image-preview:hover {
            border-color: #5a9bb8;
        }
        .image-preview img {
            max-width: 100%;
            max-height: 150px;
            border-radius: 4px;
        }
        .current-image {
            max-width: 200px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
    </style>
</head>
<body class="bg-light">
    <!-- Header -->
    <header class="header-bg text-white py-3 shadow-sm">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-star me-2"></i>
                        <h4 class="mb-0">LE.GICARFT</h4>
                        <small class="ms-2">KY SHOP</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Search bar placeholder -->
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
                            <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
                            <li><a class="dropdown-item" href="manage_orders.php">Quản lý đơn hàng</a></li>
                            <li><a class="dropdown-item active" href="manage_products.php">Quản lý sản phẩm</a></li>
                            <li><a class="dropdown-item" href="manage_users.php">Quản lý người dùng</a></li>
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
                        <a class="nav-link active" href="manage_product.php">Tất cả sản phẩm</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Mới nhất</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Cũ nhất</a>
                    </li>
                </ul>

                <div class="form-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3><i class="fas fa-edit me-2 text-primary"></i>Sửa sản phẩm</h3>
                        <a href="manage_product.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-tag me-2"></i>Tên sản phẩm *
                                    </label>
                                    <input type="text" name="name" class="form-control form-control-lg" 
                                           placeholder="Nhập tên sản phẩm" required
                                           value="<?php echo htmlspecialchars($_POST['name'] ?? $product->getName()); ?>">
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">
                                                <i class="fas fa-dollar-sign me-2"></i>Giá tiền *
                                            </label>
                                            <input type="number" name="price" class="form-control" 
                                                   placeholder="0" min="0" step="1000" required
                                                   value="<?php echo $_POST['price'] ?? $product->getPrice(); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">
                                                <i class="fas fa-cubes me-2"></i>Số lượng *
                                            </label>
                                            <input type="number" name="quantity" class="form-control" 
                                                   placeholder="0" min="0" required
                                                   value="<?php echo $_POST['quantity'] ?? $product->getQuantity(); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-list me-2"></i>Danh mục *
                                    </label>
                                    <select name="category_id" class="form-select" required>
                                        <option value="">Chọn danh mục</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category->getId(); ?>"
                                                    <?php 
                                                    $selectedCategoryId = $_POST['category_id'] ?? $product->getCategoryId();
                                                    echo ($selectedCategoryId == $category->getId()) ? 'selected' : ''; 
                                                    ?>>
                                                <?php echo htmlspecialchars($category->getName()); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-align-left me-2"></i>Mô tả sản phẩm
                                    </label>
                                    <textarea name="description" class="form-control" rows="4" 
                                              placeholder="Nhập mô tả sản phẩm..."><?php echo htmlspecialchars($_POST['description'] ?? $product->getDescription()); ?></textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-image me-2"></i>Hình ảnh sản phẩm
                                    </label>
                                    
                                    <?php if ($product->getImage()): ?>
                                        <div class="mb-3">
                                            <p class="text-muted mb-2">Hình ảnh hiện tại:</p>
                                            <img src="../../<?php echo htmlspecialchars($product->getImage()); ?>" 
                                                 alt="Current Image" class="current-image">
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="image-preview" onclick="document.getElementById('imageInput').click()">
                                        <div id="imagePreview">
                                            <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Click để chọn hình ảnh mới</p>
                                            <small class="text-muted">JPG, PNG, GIF</small>
                                        </div>
                                    </div>
                                    <input type="file" id="imageInput" name="image" accept="image/*" style="display: none;" onchange="previewImage(this)">
                                    <small class="text-muted">Để trống nếu không muốn thay đổi hình ảnh</small>
                                </div>
                            </div>
                        </div>

                        <div class="text-center pt-3">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-save me-2"></i>Lưu thay đổi
                            </button>
                            <a href="manage_product.php" class="btn btn-outline-secondary btn-lg px-5 ms-3">
                                <i class="fas fa-times me-2"></i>Hủy bỏ
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <p class="text-muted mb-0 mt-2">Click để thay đổi</p>
                    `;
                };
                
                reader.readAsDataURL(input.files[0]);
            }
        }
         header('Location: manage_product.php');
exit;
    </script>
   

</body>
</html>