<?php
require_once '../../classes/Database.php';
require_once '../../classes/Product.php';
require_once '../../config/db_config.php';

session_start();
$db = new Database($host, $username, $password, $dbname);

$products = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $keyword = $_POST['keyword'] ?? '';
    if (!empty($keyword)) {
        $products = Product::search($db, $keyword);
    } else {
        echo "<p class='text-danger text-center'>Vui lòng nhập từ khóa tìm kiếm!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>LE.GICARFT | Tìm kiếm sản phẩm</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/css/style.css">
  
</head>
<body>
  <header class="bg-light py-2 shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
      <div class="logo">
        <img src="../../assets/images/logo.png" alt="Logo LE.GICARFT" height="50">
      </div>
  <div class="search">
            <form method="POST" action="search.php">
                <input type="text" name="keyword" placeholder="Nhập sản phẩm cần tìm kiếm" required>
                <button type="submit">Tìm kiếm</button>
            </form>
        </div>
      <div class="user-cart d-flex gap-3">
        <?php if (isset($_SESSION['id_nguoidung'])): ?>
          <a href="logout.php" class="btn btn-outline-secondary">Đăng xuất</a>
        <?php else: ?>
          <a href="login.php" class="btn btn-outline-primary">Đăng nhập/Đăng ký</a>
        <?php endif; ?>
        <a href="cart.php" class="btn btn-outline-success">🛒 Giỏ hàng</a>
      </div>
    </div>
  </header>

  <section class="food-search text-center py-4">
    <div class="container">
      <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['keyword'])): ?>
        <h2 class="text-white">Sản phẩm bạn tìm kiếm <span class="text-warning">"<?php echo htmlspecialchars($_POST['keyword']); ?>"</span></h2>
      <?php endif; ?>
    </div>
  </section>

  <section class="food-menu py-4">
    <div class="container">
      <h2 class="text-center mb-4">Danh sách sản phẩm</h2>
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
                  <h4 class="card-title"><?php echo htmlspecialchars($product->getName()); ?></h4>
                  <p class="card-text"><?php echo number_format($product->getPrice(), 0, ',', '.'); ?> VNĐ</p>
                  <p class="card-text"><?php echo htmlspecialchars($product->getDescription()); ?></p>
                  <form method="GET" action="add_to_cart.php">
                    <input type="hidden" name="id" value="<?php echo $product->getId(); ?>">
                    <button type="submit" class="btn btn-outline-primary">Thêm vào giỏ</button>
                  </form>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="text-center text-danger">Không tìm thấy sản phẩm.</p>
      <?php endif; ?>
    </div>
  </section>

  <div class="container">
    <p class="mt-3 text-center"><a href="my-order.php" class="btn btn-secondary">Quay lại trang chủ</a></p>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>