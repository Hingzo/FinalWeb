<?php
// Tắt tất cả các thông báo lỗi
error_reporting(0);
ini_set('display_errors', 0);

// Set header JSON
header('Content-Type: application/json; charset=utf-8');

// Khởi tạo response
$response = array('success' => false, 'message' => '');

// Kiểm tra method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Phương thức không được hỗ trợ';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// Bắt đầu session
session_start();

// Kiểm tra quyền admin
if (!isset($_SESSION['id_nguoidung']) || $_SESSION['vaitro'] != 'admin') {
    $response['message'] = 'Không có quyền truy cập';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// Kiểm tra ID sản phẩm
if (!isset($_POST['id_sanpham']) || empty($_POST['id_sanpham'])) {
    $response['message'] = 'Thiếu ID sản phẩm';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

$id_sanpham = (int)$_POST['id_sanpham'];

if ($id_sanpham <= 0) {
    $response['message'] = 'ID sản phẩm không hợp lệ';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// Kết nối database
require_once '../../config/db_config.php';

try {
    $conn = new mysqli($host, $username, $password, 'legicaft');
    
    if ($conn->connect_error) {
        $response['message'] = 'Lỗi kết nối database';
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $conn->set_charset("utf8");
    
    // Kiểm tra sản phẩm có tồn tại không
    $check_sql = "SELECT id_sanpham FROM tbl_sanpham WHERE id_sanpham = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $id_sanpham);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows == 0) {
        $response['message'] = 'Sản phẩm không tồn tại';
        $check_stmt->close();
        $conn->close();
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    $check_stmt->close();
    
    // Kiểm tra sản phẩm có trong đơn hàng không
    $order_sql = "SELECT COUNT(*) as total FROM tbl_chitietdonhang WHERE id_sanpham = ?";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param("i", $id_sanpham);
    $order_stmt->execute();
    $order_result = $order_stmt->get_result();
    $order_row = $order_result->fetch_assoc();
    $order_count = $order_row['total'];
    $order_stmt->close();
    
    if ($order_count > 0) {
        $response['message'] = "Không thể xóa sản phẩm vì có {$order_count} đơn hàng liên kết";
        $conn->close();
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Thực hiện xóa sản phẩm
    $delete_sql = "DELETE FROM tbl_sanpham WHERE id_sanpham = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $id_sanpham);
    
    if ($delete_stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Xóa sản phẩm thành công';
    } else {
        $response['message'] = 'Không thể xóa sản phẩm';
    }
    
    $delete_stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    $response['message'] = 'Có lỗi xảy ra';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>