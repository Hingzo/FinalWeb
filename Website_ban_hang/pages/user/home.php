<?php
require_once '../../classes/Database.php';
require_once '../../classes/Product.php';
require_once '../../classes/Category.php';
require_once '../../config/db_config.php';

session_start();
$db = new Database($host, $username, $password, $dbname);

// Lấy danh sách sản phẩm nổi bật
$products = Product::getFeatured($db);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LE.GICARFT | Trang chủ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="topbar">
        <div class="logo">
            <img src="../../assets/images/logo.png" alt="Le Gicart Logo">
        </div>
        <div class="search">
            <form method="POST" action="search.php">
                <input type="text" name="keyword" placeholder="Nhập sản phẩm cần tìm kiếm" required>
                <button type="submit">Tìm kiếm</button>
            </form>
        </div>
        <div class="user-cart">
            <a href="login.php" class="cart">Đăng nhập</a>
            <a href="register.php" class="cart">Đăng ký</a>  
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

        <div class="content">
            <section class="banner mb-4">
                <img src="../../assets/images/banner.jpg" alt="Banner Hotwheels" class="img-fluid rounded">
            </section>

                   <section class="product-section">
                       <h2 class="text-center mb-4">Sản phẩm nổi bật</h2>
                       <?php if (!empty($products)): ?>
                           <div class="row row-cols-1 row-cols-md-4 g-4">
                               <?php foreach ($products as $product): ?>
                                   <div class="col">
                                       <div class="card h-100 text-center shadow-sm">
                                           <?php
                                           $imagePath = "../../" . htmlspecialchars($product->getImage());
                                           if (file_exists($imagePath)) {
                                               echo "<img src='$imagePath' class='card-img-top img-fluid' alt='" . htmlspecialchars($product->getName()) . "'>";
                                           } else {
                                               echo "<img src='../../assets/images/product1.png' class='card-img-top img-fluid' alt='No Image'>";
                                               error_log("Ảnh không tồn tại: $imagePath");
                                           }
                                           ?>
                                           <div class="card-body">
                                               <h5 class="card-title"><?php echo htmlspecialchars($product->getName()); ?></h5>
                                               <p class="card-text"><?php echo number_format($product->getPrice(), 0, ',', '.'); ?> VNĐ</p>
                                               <p class="card-text"><?php echo htmlspecialchars($product->getDescription()); ?></p>
                                               <form method="POST" action="cart.php">
                                                   <input type="hidden" name="id_sanpham" value="<?php echo $product->getId(); ?>">
                                                   <button type="submit" name="add_to_cart" class="btn btn-outline-primary">Thêm vào giỏ</button>
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
               </main>
           </div>
       </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>