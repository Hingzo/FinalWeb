<?php
   require_once '../../classes/Database.php';
   require_once '../../classes/Product.php';
   require_once '../../classes/Category.php';
   require_once '../../config/db_config.php';

   session_start();
   $db = new Database($host, $username, $password, $dbname);

   // Lấy danh sách danh mục
   $categories = Category::getAll($db);

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
   </head>
   <body>
       <header class="bg-light py-2 shadow-sm">
           <div class="container d-flex justify-content-between align-items-center">
               <div class="logo">
                   <img src="../../assets/images/logo.png" alt="Logo LE.GICARFT" height="50">
               </div>
               <div class="search flex-grow-1 mx-4">
                   <form method="POST" action="search.php" class="d-flex">
                       <input type="text" name="keyword" class="form-control rounded-start" placeholder="Nhập sản phẩm cần tìm kiếm" required>
                       <button type="submit" class="btn btn-primary rounded-end">Tìm kiếm</button>
                   </form>
               </div>
               <div class="user-cart d-flex gap-3">
                   <?php if (isset($_SESSION['id_nguoidung'])): ?>
                       <span class="align-self-center">Xin chào, <?php echo htmlspecialchars($_SESSION['hoten']); ?>!</span>
                       <a href="logout.php" class="btn btn-outline-secondary">Đăng xuất</a>
                   <?php else: ?>
                       <a href="login.php" class="btn btn-outline-primary">Đăng nhập/Đăng ký</a>
                   <?php endif; ?>
                   <a href="cart.php" class="btn btn-outline-success">🛒 Giỏ hàng</a>
               </div>
           </div>
       </header>

       <div class="container mt-4">
           <div class="row">
               <aside class="col-md-3">
                   <div class="card">
                       <div class="card-header bg-primary text-white text-center">
                           <h3 class="mb-0">Danh mục sản phẩm</h3>
                       </div>
                       <ul class="list-group list-group-flush">
                           <?php if (!empty($categories)): ?>
                               <?php foreach ($categories as $category): ?>
                                   <li class="list-group-item"><?php echo htmlspecialchars($category->getName()); ?></li>
                               <?php endforeach; ?>
                           <?php else: ?>
                               <li class="list-group-item text-danger">Không có danh mục nào!</li>
                           <?php endif; ?>
                       </ul>
                   </div>
               </aside>

               <main class="col-md-9">
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
                                               echo "<img src='../../assets/images/no-image.jpg' class='card-img-top img-fluid' alt='No Image'>";
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