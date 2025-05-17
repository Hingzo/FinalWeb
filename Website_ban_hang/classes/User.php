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

    public function getId() { return $this->id_nguoidung; }
    public function getHoten() { return $this->hoten; }
    public function getEmail() { return $this->email; }
    public function getVaitro() { return $this->vaitro; }
}
?>