<?php
require_once '../../classes/Database.php';
require_once '../../config/db_config.php';

session_start();
$db = new Database($host, $username, $password, $dbname);

// Kiểm tra kết nối cơ sở dữ liệu
$conn = $db->getConnection();
if ($conn) {
    echo "Kết nối CSDL thành công!<br>";
} else {
    echo "Kết nối CSDL thất bại! Vui lòng kiểm tra db_config.php.<br>";
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? ''); // Loại bỏ ký tự trắng
    $password = trim($_POST['password'] ?? ''); // Loại bỏ ký tự trắng

    echo "Email nhập vào (sau trim): $email<br>";
    echo "Mật khẩu nhập vào (plaintext, sau trim): $password<br>";

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id_nguoidung, hoten, email, matkhau, vaitro FROM tbl_nguoidung WHERE email = ?");
        if ($stmt === false) {
            echo "Lỗi chuẩn bị truy vấn: " . $conn->error . "<br>";
            exit();
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            echo "Dữ liệu từ DB: " . var_export($user, true) . "<br>";
            $hashed_input_password = md5($password);
            echo "Mật khẩu nhập vào (md5): $hashed_input_password<br>";
            echo "Mật khẩu trong DB: " . $user['matkhau'] . "<br>";
            if ($hashed_input_password === $user['matkhau']) {
                $_SESSION['id_nguoidung'] = $user['id_nguoidung'];
                $_SESSION['hoten'] = $user['hoten'];
                $_SESSION['vaitro'] = $user['vaitro'];
                echo "Vai trò: " . $_SESSION['vaitro'] . "<br>";
                echo "Đăng nhập thành công, chuyển hướng...<br>";
                header("Location: ../../index.php");
                exit();
            } else {
                $error = "Mật khẩu không đúng! (Debug: Hash không khớp)";
            }
        } else {
            $error = "Email không tồn tại! (Debug: Không tìm thấy bản ghi)";
        }
        $stmt->close();
    } else {
        $error = "Vui lòng nhập đầy đủ email và mật khẩu!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LE.GICARFT | Đăng nhập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-light py-2 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="logo">
                <img src="../../assets/images/logo.png" alt="Logo LE.GICARFT" height="50">
            </div>
            <div class="user-cart">
                <a href="../../index.php" class="btn btn-secondary">Quay lại trang chủ</a>
            </div>
        </div>
    </header>

    <section class="login py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white text-center">
                            <h3 class="mb-0">Đăng nhập</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            <form method="POST" action="login.php">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" id="email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mật khẩu</label>
                                    <input type="password" name="password" id="password" class="form-control" required>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Đăng nhập</button>
                                </div>
                            </form>
                            <p class="mt-