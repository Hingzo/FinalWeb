<?php
session_start();
require_once '../../classes/Database.php';
require_once '../../classes/Product.php';
require_once '../../classes/Cart.php';
require_once '../../config/db_config.php';

$db = new Database($host, $username, $password, $dbname);

// Xử lý cập nhật/xóa sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? null;

    if ($id !== null) {
        switch ($action) {
            case 'update':
                $quantity = max(1, intval($_POST['quantity']));
                $_SESSION['cart'][$id] = $quantity;
                break;
            case 'delete':
                unset($_SESSION['cart'][$id]);
                break;
        }
    }
    header("Location: cart.php");
    exit;
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

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
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-white text-dark">
<div class="container py-5">
    <h2 class="mb-4 text-center">Giỏ hàng của bạn</h2>
    <div class="mt-4 text-left py-2">
        <a href="../../pages/user/my-order.php" class="btn btn-primary">←</a>
    </div>

    <?php if (empty($productsInCart)): ?>
        <div class="alert alert-info text-center">Giỏ hàng trống.</div>
    <?php else: ?>
        <?php foreach ($productsInCart as $item): ?>
            <?php $product = $item['product']; ?>
            <div class="card mb-3 shadow-sm">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img src="../../<?= htmlspecialchars($product->getImage()) ?>" class="img-fluid rounded-start" alt="<?= htmlspecialchars($product->getName()) ?>">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product->getName()) ?></h5>
                            <p class="card-text">Giá: <?= number_format($product->getPrice(), 0, ',', '.') ?> VNĐ</p>
                            <form class="row g-2" method="POST">
                                <input type="hidden" name="id" value="<?= $product->getId() ?>">
                                <input type="hidden" name="action" value="update">
                                <div class="col-4">
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="form-control">
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                                </div>
                            </form>
                            <form method="POST" class="mt-2">
                                <input type="hidden" name="id" value="<?= $product->getId() ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="btn btn-danger">Xóa</button>
                            </form>
                            <p class="card-text fw-bold mt-3">Thành tiền: <?= number_format($item['subtotal'], 0, ',', '.') ?> VNĐ</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="text-end">
            <h4>Tổng cộng: <?= number_format($total, 0, ',', '.') ?> VNĐ</h4>
            <a href="my-order.php" class="btn btn-success mt-3">Thanh toán</a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>