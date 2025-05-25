<?php
require_once 'Database.php';

class User {
    private $db;
    private $id_nguoidung;
    private $hoten;
    private $email;
    private $vaitro;

    public function __construct($host, $username, $password, $dbname) {
        $this->db = new Database($host, $username, $password, $dbname);
    }

    public function login($email, $password) {
        $conn = $this->db->getConnection();
        if ($conn) {
            $stmt = $conn->prepare("SELECT id_nguoidung, hoten, email, matkhau, vaitro FROM tbl_nguoidung WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $hashed_input_password = md5($password);
                if ($hashed_input_password === $user['matkhau']) {
                    $this->id_nguoidung = $user['id_nguoidung'];
                    $this->hoten = $user['hoten'];
                    $this->email = $user['email'];
                    $this->vaitro = $user['vaitro'];
                    return true;
                }
            }
            $stmt->close();
        }
        return false;
    }

    // Thêm phương thức register
    public function register($hoten, $gioitinh, $sdt, $email, $matkhau, $diachi) {
        $conn = $this->db->getConnection();
        if ($conn) {
            // Kiểm tra email đã tồn tại chưa
            $stmt = $conn->prepare("SELECT email FROM tbl_nguoidung WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                return "Email đã được sử dụng!";
            }

            // Mã hóa mật khẩu bằng md5
            $matkhau_md5 = md5($matkhau);

            // Thêm người dùng mới vào bảng tbl_nguoidung
            $stmt = $conn->prepare("INSERT INTO tbl_nguoidung (hoten, gioitinh, sdt, email, matkhau, diachi, vaitro) VALUES (?, ?, ?, ?, ?, ?, 'user')");
            $stmt->bind_param("ssssss", $hoten, $gioitinh, $sdt, $email, $matkhau_md5, $diachi);

            if ($stmt->execute()) {
                $stmt->close();
                return true; // Đăng ký thành công
            } else {
                $stmt->close();
                return "Đã xảy ra lỗi khi đăng ký!";
            }
        }
        return "Không thể kết nối cơ sở dữ liệu!";
    }

    public function getId() { return $this->id_nguoidung; }
    public function getHoten() { return $this->hoten; }
    public function getEmail() { return $this->email; }
    public function getVaitro() { return $this->vaitro; }
}
?>