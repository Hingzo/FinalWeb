<?php
session_start(); // Bắt buộc để làm việc với session
require_once '../../classes/Cart.php';
require_once '../../classes/Database.php';
require_once '../../classes/Product.php';
require_once '../../config/db_config.php';

$db = new Database($host, $username, $password, $dbname);

if (isset($_GET['id'])) {
    $productId = $_GET['id'];
    
    // Thêm sản phẩm vào giỏ hàng
    Cart::addToCart($productId);
    
    // Điều hướng về trang giỏ hàng
    header("Location: cart.php");
    exit();
} else {
    // Nếu không có ID, quay lại trang trước đó
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>
