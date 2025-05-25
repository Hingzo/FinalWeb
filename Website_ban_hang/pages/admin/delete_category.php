<?php
require_once '../../classes/Database.php';
require_once '../../classes/Category.php';
require_once '../../config/db_config.php';

session_start();
$db = new Database($host, $username, $password, 'legicaft');


// Kiểm tra quyền admin
if (!isset($_SESSION['id_nguoidung']) || $_SESSION['vaitro'] != 'admin') {
    header("Location: ../user/home.php");
    exit();
}

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Kiểm tra xem danh mục có sản phẩm liên kết không
    $checkStmt = $db->getConnection()->prepare("SELECT COUNT(*) FROM tbl_sanpham WHERE id_danhmuc = ?");
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0) {
        $response['message'] = "Không thể xóa danh mục vì có sản phẩm liên kết!";
    } else {
        $category = new Category($id, '', 0, '');
        if ($category->delete($db)) {
            $response['success'] = true;
            $response['message'] = "Đã xóa danh mục thành công!";
        } else {
            $response['message'] = "Lỗi khi xóa danh mục: " . $db->getConnection()->error;
        }
    }
}

header("Content-Type: application/json");
echo json_encode($response);
exit();
?>