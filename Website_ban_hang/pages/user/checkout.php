<?php
session_start();
require_once '../../classes/Database.php';
require_once '../../classes/Product.php';
require_once '../../classes/Order.php';
require_once '../../classes/Cart.php';
require_once '../../config/db_config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['id_nguoidung'])) {
    error_log("Chưa đăng nhập, chuyển hướng đến login.php");
    header("Location: ../auth/login.php");
    exit;
}

// Khởi tạo database
if (!isset($host) || !isset($username) || !isset($password) || !isset($dbname)) {
    error_log("Thiếu cấu hình cơ sở dữ liệu trong db_config.php");
    die("Database configuration not found. Please check db_config.php");
}

try {
    $db = new Database($host, $username, $password, $dbname);
    $conn = $db->getConnection();
    if (!$conn) {
        throw new Exception("Cannot connect to database");
    }
} catch (Exception $e) {
    error_log("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage());
    die("Database connection error: " . $e->getMessage());
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Kiểm tra giỏ hàng
if (empty($cart)) {
    error_log("Giỏ hàng rỗng, chuyển hướng đến cart.php");
    header("Location: cart.php");
    exit;
}

error_log("Nội dung giỏ hàng: " . print_r($cart, true));
error_log("User ID: " . $_SESSION['id_nguoidung']);

$error = '';
$success = '';
$orderId = null;

// Xử lý đặt hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $note = trim($_POST['note'] ?? '');
    $userId = $_SESSION['id_nguoidung'];
    
    error_log("Bắt đầu xử lý đặt hàng cho user ID: $userId");
    
    if (empty($cart)) {
        $error = "Giỏ hàng trống. Vui lòng thêm sản phẩm vào giỏ hàng.";
    } else {
        try {
            $order = Order::createFromCart($db, $userId, $cart, $note);
            Cart::clearCart();
            $success = "Đặt hàng thành công!";
            $orderId = $order->getId();
            error_log("Đặt hàng thành công, order ID: $orderId");
        } catch (Exception $e) {
            error_log("Order creation error: " . $e->getMessage());
            $error = "Lỗi đặt hàng: " . htmlspecialchars($e->getMessage());
        }
    }
}

// Lấy thông tin sản phẩm trong giỏ hàng
$productsInCart = [];
$total = 0;

foreach ($cart as $productId => $quantity) {
    $product = Product::getById($db, $productId);
    if ($product instanceof Product) {
        $subtotal = $quantity * $product->getPrice();
        $productsInCart[] = [
            'product' => $product,
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
        $total += $subtotal;
    } else {
        error_log("Sản phẩm với ID $productId không tồn tại trong giỏ hàng");
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán - LE.GICRAFT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .checkout-container {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
        }
        .order-summary {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .product-item {
            border-bottom: 1px solid #dee2e6;
            padding: 15px 0;
        }
        .product-item:last-child {
            border-bottom: none;
        }
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        .total-section {
            background-color: #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }
        .btn-place-order {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border: none;
            padding: 12px 30px;
            font-size: 18px;
            font-weight: bold;
        }
        .success-message {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="checkout-container">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-between">
                        <h2 class="mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Thanh toán
                        </h2>
                        <a href="cart.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Quay lại giỏ hàng
                        </a>
                    </div>
                </div>
            </div>

            <?php if ($success && $orderId): ?>
                <!-- Thông báo thành công -->
                <div class="order-confirmation">
                    <i class="fas fa-check-circle"></i>
                    <h3 class="text-success mb-3">Đặt hàng thành công!</h3>
                    <div class="alert alert-success">
                        <h5>Mã đơn hàng: #<?php echo $orderId; ?></h5>
                        <p class="mb-0">Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ liên hệ xác nhận đơn hàng trong thời gian sớm nhất.</p>
                    </div>
                    <div class="mt-4">
                        <a href="my-order.php" class="btn btn-primary me-3">
                            <i class="fas fa-home me-1"></i>
                            Về trang chủ
                        </a>
                        <a href="order-history.php" class="btn btn-outline-primary">
                            <i class="fas fa-history me-1"></i>
                            Xem lịch sử đơn hàng
                        </a>
                    </div>
                </div>

            <?php else: ?>
                <!-- Form đặt hàng -->
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <!-- Thông tin đơn hàng -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-list-alt me-2"></i>
                                    Thông tin đơn hàng
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($productsInCart)): ?>
                                    <div class="alert alert-warning">Không có sản phẩm nào trong giỏ hàng.</div>
                                <?php else: ?>
                                    <?php foreach ($productsInCart as $item): ?>
                                        <?php $product = $item['product']; ?>
                                        <div class="product-item d-flex align-items-center">
                                            <div class="me-3">
                                                <?php
                                                $imagePath = "../../" . htmlspecialchars($product->getImage());
                                                if (file_exists($imagePath)) {
                                                    echo "<img src='$imagePath' class='product-image' alt='" . htmlspecialchars($product->getName()) . "'>";
                                                } else {
                                                    echo "<img src='../../assets/images/product1.png' class='product-image' alt='No Image'>";
                                                    error_log("Ảnh không tồn tại: $imagePath");
                                                }
                                                ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($product->getName()); ?></h6>
                                                <p class="text-muted mb-1">
                                                    Giá: <?php echo number_format($product->getPrice(), 0, ',', '.'); ?> VNĐ
                                                </p>
                                                <small class="text-muted">Số lượng: <?php echo $item['quantity']; ?></small>
                                            </div>
                                            <div class="text-end">
                                                <strong class="text-primary">
                                                    <?php echo number_format($item['subtotal'], 0, ',', '.'); ?> VNĐ
                                                </strong>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Form ghi chú -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-sticky-note me-2"></i>
                                    Ghi chú đơn hàng
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" id="checkoutForm">
                                    <div class="mb-3">
                                        <label for="note" class="form-label">Ghi chú (tùy chọn)</label>
                                        <textarea class="form-control" id="note" name="note" rows="3" 
                                                placeholder="Nhập ghi chú cho đơn hàng (yêu cầu đóng gói, thời gian giao hàng...)"></textarea>
                                    </div>
                                    <input type="hidden" name="place_order" value="1">
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Tóm tắt thanh toán -->
                    <div class="col-lg-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-calculator me-2"></i>
                                    Tóm tắt đơn hàng
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tạm tính:</span>
                                    <span><?php echo number_format($total, 0, ',', '.'); ?> VNĐ</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Phí vận chuyển:</span>
                                    <span class="text-success">Miễn phí</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <strong>Tổng cộng:</strong>
                                    <strong class="text-primary fs-5">
                                        <?php echo number_format($total, 0, ',', '.'); ?> VNĐ
                                    </strong>
                                </div>

                                <div class="order-summary mb-3">
                                    <h6 class="text-muted mb-2">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Thông tin khách hàng:
                                    </h6>
                                    <p class="mb-1"><strong><?php echo htmlspecialchars($_SESSION['hoten'] ?? 'Khách hàng'); ?></strong></p>
                                    <p class="mb-1 text-muted"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></p>
                                    <p class="mb-0 text-muted"><?php echo htmlspecialchars($_SESSION['sdt'] ?? ''); ?></p>
                                </div>

                                <button type="submit" form="checkoutForm" name="place_order" 
                                        class="btn btn-place-order text-white w-100" 
                                        onclick="return confirmOrder()">
                                    <i class="fas fa-credit-card me-2"></i>
                                    Đặt hàng ngay
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmOrder() {
            console.log('Hiển thị hộp thoại xác nhận');
            return confirm('Bạn có chắc chắn muốn đặt hàng với tổng tiền ' + 
                         '<?php echo number_format($total, 0, ',', '.'); ?>' + ' VNĐ?');
        }

        document.getElementById('checkoutForm').addEventListener('submit', function() {
            console.log('Form submitted');
            const submitBtn = document.querySelector('button[name="place_order"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>