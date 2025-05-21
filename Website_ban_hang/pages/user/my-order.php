<?php
session_start();
require_once '../../classes/Database.php';
require_once '../../classes/Product.php';
require_once '../../classes/Category.php';
require_once '../../config/db_config.php';

// Kết nối cơ sở dữ liệu
$db = new Database($host, $username, $password, $dbname);

// Lấy danh sách sản phẩm nổi bật
$products = Product::getFeatured($db);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LE.GICARFT | Trang khách hàng thành viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="../../assets/css/user.css">
</head>
<body>
    <!-- Header -->
    <header class="topbar">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="logo">
                <img src="../../assets/images/logo.png" alt="Le Gicart Logo">
            </div>
            <div class="search flex-grow-1 mx-4">
            <form method="POST" action="search.php">
                <input type="text" name="keyword" placeholder="Nhập sản phẩm cần tìm kiếm" required>
                <button type="submit"class="btn btn-primary rounded-end">Tìm kiếm</button>
            </form>
        </div>
            <div class="user-cart d-flex align-items-center gap-3">
                <div class="user-info d-flex align-items-center">
                    <div class="avatar">
                        <img src="https://tse3.mm.bing.net/th?id=OIP.xgGxThCiW1A5jWdxmRNKJQHaHa&pid=Api&P=0&h=220.png" alt="Avatar">
                    </div>
                    <div class="account">
                        <span>Tài khoản</span>
                        <strong>
                            <?php
                            if (isset($_SESSION['hoten'])) {
                                echo htmlspecialchars($_SESSION['hoten']);
                            } else {
                                echo "Khách hàng";
                            }
                            ?>
                        </strong>
                    </div>
                </div>
                <div class="cart">
                    <a href="#" class="d-flex align-items-center gap-2">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Giỏ hàng</span>
                    </a>
                </div>
                <a href="logout.php" class="btn btn-outline-secondary">Đăng xuất</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-container d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <h3>Danh mục sản phẩm</h3>
            <ul class="category-list">
                <li class="category-item"><i class="fas fa-chevron-down"></i> HOTWHEELS</li>
                <li class="category-item"><i class="fas fa-chevron-down"></i> MINI GT</li>
                <li class="category-item"><i class="fas fa-chevron-down"></i> TARMACWORKS</li>
                <li class="category-item"><i class="fas fa-chevron-down"></i> BABY CRY</li>
                <li class="category-item"><i class="fas fa-chevron-down"></i> LABUBU</li>
                <li class="category-item"><i class="fas fa-chevron-down"></i> BABY THREE</li>
            </ul>
        </div>

        <!-- Content -->
        <div class="content flex-grow-1 p-4">
            <!-- Banner -->
            <div class="banner mb-4">
                <img src="../../assets/images/banner.jpg" alt="Banner Hotwheels" class="img-fluid rounded">
            </div>

            <!-- Featured Products -->
            <section class="product-section">
                <h2 class="text-center mb-4">Sản phẩm nổi bật</h2>
                <?php if (!empty($products)): ?>
                    <div class="row row-cols-1 row-cols-md-4 g-4">
                        <?php foreach ($products as $product): ?>
                            <div class="col">
                                <div class="product-card shadow-sm">
                                    <?php
                                    $imagePath = "../../" . htmlspecialchars($product->getImage());
                                    if (file_exists($imagePath)) {
                                        echo "<img src='$imagePath' class='img-fluid' alt='" . htmlspecialchars($product->getName()) . "'>";
                                    } else {
                                        echo "<img src='../../uploads/images/hotwheels_ferrari_sf90.jpg' class='img-fluid' alt='No Image'>";
                                        error_log("Ảnh không tồn tại: $imagePath");
                                    }
                                    ?>
                                    <div class="product-info">
                                        <h3 class="fs-5"><?php echo htmlspecialchars($product->getName()); ?></h3>
                                        <p class="price"><?php echo number_format($product->getPrice(), 0, ',', '.'); ?> VNĐ</p>
                                        <form method="POST" action="cart.php">
                                            <input type="hidden" name="id_sanpham" value="<?php echo $product->getId(); ?>">
                                            <button type="submit" name="add_to_cart" class="add-to-cart-btn">Thêm vào giỏ</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-center text-danger">Không có sản phẩm nào để hiển thị!</p>
                <?php endif; ?>
            </section>
        </div>
    </div>

    <!-- Cart Popup -->
    <div class="cart-popup-overlay" id="cartPopupOverlay">
        <div class="cart-popup">
            <img id="popupProductImage" src="" alt="Sản phẩm">
            <div class="cart-popup-details">
                <h3 id="popupProductName">Tên sản phẩm</h3>
                <p id="popupProductPrice">Giá</p>
                <div class="popup-buttons">
                    <button onclick="window.location.href='cart.html'">Đi tới giỏ hàng</button>
                    <button onclick="closeCartPopup()">Tiếp tục mua</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="filescript.js"></script>
</body>
</html>