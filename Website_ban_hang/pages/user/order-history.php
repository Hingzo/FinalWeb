<?php
session_start();
require_once '../../classes/Database.php';
require_once '../../classes/Order.php';
require_once '../../classes/Product.php';
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

// Lấy danh sách đơn hàng của người dùng
$userId = $_SESSION['id_nguoidung'];
$orders = Order::getByUserId($db, $userId);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử đơn hàng - LE.GICRAFT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .order-history-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .order-card {
            background-color: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .order-header {
            background-color: #007bff;
            color: white;
            padding: 15px;
            border-radius: 10px 10px 0 0;
        }
        .order-details {
            padding: 20px;
        }
        .product-item {
            border-bottom: 1px solid #dee2e6;
            padding: 10px 0;
        }
        .product-item:last-child {
            border-bottom: none;
        }
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        .status-badge {
            font-size: 0.9em;
            padding: 5px 10px;
            border-radius: 12px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="order-history-container">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-between">
                        <h2 class="mb-0">
                            <i class="fas fa-history me-2"></i>
                            Lịch sử đơn hàng
                        </h2>
                        <a href="my-order.php" class="btn btn-outline-secondary">
                            Về trang chủ
                        </a>
                    </div>
                </div>
            </div>

            <?php if (empty($orders)): ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>
                    Bạn chưa có đơn hàng nào.
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-card shadow-sm">
                        <div class="order-header">
                            <h5 class="mb-0">
                                <i class="fas fa-receipt me-2"></i>
                                Đơn hàng #<?php echo htmlspecialchars($order->getId()); ?>
                                <span class="float-end">
                                    <span class="status-badge bg-<?php 
                                        switch ($order->getStatus()) {
                                            case 'Cho xac nhan':
                                                echo 'warning';
                                                break;
                                            case 'Dang xu ly':
                                                echo 'primary';
                                                break;
                                            case 'Dang giao':
                                                echo 'info';
                                                break;
                                            case 'Da giao':
                                                echo 'success';
                                                break;
                                            case 'Da huy':
                                                echo 'danger';
                                                break;
                                            default:
                                                echo 'secondary';
                                        }
                                    ?>">
                                        <?php echo htmlspecialchars($order->getFormattedStatus()); ?>
                                    </span>
                                </span>
                            </h5>
                        </div>
                        <div class="order-details">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Ngày đặt:</strong> <?php echo htmlspecialchars($order->getFormattedDate()); ?></p>
                                    <p><strong>Tổng tiền:</strong> <?php echo number_format($order->getTotal(), 0, ',', '.'); ?> VNĐ</p>
                                    <p><strong>Ghi chú:</strong> <?php echo htmlspecialchars($order->getNote() ?: 'Không có ghi chú'); ?></p>
                                </div>
                            </div>
                            <hr>
                            <h6><i class="fas fa-box-open me-2"></i>Chi tiết đơn hàng</h6>
                            <?php $orderDetails = $order->getOrderDetails($db); ?>
                            <?php if (empty($orderDetails)): ?>
                                <p class="text-muted">Không có chi tiết đơn hàng.</p>
                            <?php else: ?>
                                <?php foreach ($orderDetails as $detail): ?>
                                    <div class="product-item d-flex align-items-center">
                                        <div class="me-3">
                                            <?php
                                            $imagePath = "../../" . htmlspecialchars($detail['hinh_anh']);
                                            if (file_exists($imagePath)) {
                                                echo "<img src='$imagePath' class='product-image' alt='" . htmlspecialchars($detail['tensanpham']) . "'>";
                                            } else {
                                                echo "<img src='../../assets/images/product1.png' class='product-image' alt='No Image'>";
                                                error_log("Ảnh không tồn tại: $imagePath");
                                            }
                                            ?>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($detail['tensanpham']); ?></h6>
                                            <p class="text-muted mb-1">
                                                Giá: <?php echo number_format($detail['giatien'], 0, ',', '.'); ?> VNĐ
                                            </p>
                                            <small class="text-muted">Số lượng: <?php echo $detail['soluong']; ?></small>
                                        </div>
                                        <div class="text-end">
                                            <strong class="text-primary">
                                                <?php echo number_format($detail['thanhtien'], 0, ',', '.'); ?> VNĐ
                                            </strong>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>