<?php
require_once '../../classes/Database.php';
require_once '../../classes/Product.php';
require_once '../../config/db_config.php';

session_start();
$db = new Database($host, $username, $password, $dbname);

// Kiểm tra quyền user
if (!isset($_SESSION['id_nguoidung']) || $_SESSION['vaitro'] != 'user') {
    header("Location: ../../index.php");
    exit();
}

// Kiểm tra kết nối cơ sở dữ liệu
$conn = $db->getConnection();
if (!$conn) {
    echo "Kết nối CSDL thất bại!<br>";
    exit();
}

// Khởi tạo giỏ hàng trong session nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Xử lý thêm sản phẩm vào giỏ
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $id_sanpham = $_POST['id_sanpham'] ?? null;
    if (!$id_sanpham) {
        echo "Không tìm thấy id_sanpham từ form!<br>";
        exit();
    }
    echo "ID sản phẩm: $id_sanpham<br>";
    $product = Product::getById($db, $id_sanpham);
    if ($product) {
        if (!isset($_SESSION['cart'][$id_sanpham])) {
            $_SESSION['cart'][$id_sanpham] = [
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'image' => $product->getImage(),
                'quantity' => 1
            ];
        } else {
            $_SESSION['cart'][$id_sanpham]['quantity'] += 1;
        }
        header("Location: my-orders.php");
        exit();
    } else {
        echo "Không tìm thấy sản phẩm với ID: $id_sanpham<br>";
        exit();
    }
}

// Xử lý đặt hàng
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    // Logic đặt hàng: lưu đơn hàng vào cơ sở dữ liệu (chưa có bảng, nên tôi để giả lập)
    $_SESSION['cart'] = []; // Xóa giỏ hàng sau khi đặt hàng
    $success = "Đặt hàng thành công! Đơn hàng của bạn đã được ghi nhận.";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LE.GICARFT | Đơn hàng của tôi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-light py-2 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="logo">
                <img src="../../assets/images/logo.png" alt="Logo LE.GICARFT" height="50">
            </div>
            <div class="user-cart">
                <a href="../../index.php" class="btn btn-secondary">Thêm sản phẩm</a>
                <a href="logout.php" class="btn btn-outline-secondary">Đăng xuất</a>
            </div>
        </div>
    </header>

    <section class="cart py-5">
        <div class="container">
            <h2 class="text-center mb-4">Đơn hàng của bạn</h2>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php if (!empty($_SESSION['cart'])): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Tổng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $total = 0; ?>
                        <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                            <tr>
                                <td><img src="../../<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" height="50"></td>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo number_format($item['price'], 0, ',', '.'); ?> VNĐ</td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> VNĐ</td>
                            </tr>
                            <?php $total += $item['price'] * $item['quantity']; ?>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="4" class="text-end"><strong>Tổng cộng:</strong></td>
                            <td><strong><?php echo number_format($total, 0, ',', '.'); ?> VNĐ</strong></td>
                        </tr>
                    </tbody>
                </table>
                <form method="POST" action="my-orders.php" class="text-center">
                    <button type="submit" name="place_order" class="btn btn-primary">Đặt hàng</button>
                </form>
            <?php else: ?>
                <p class="text-center">Giỏ hàng của bạn đang trống! Hãy thêm sản phẩm từ trang chủ.</p>
            <?php endif; ?>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>