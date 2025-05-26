<?php
class Category {
    private $id;
    private $name;
    private $quantity;
    private $description;

    public function __construct($id = null, $name = '', $quantity = 0, $description = '') {
        $this->id = $id;
        $this->name = $name;
        $this->quantity = $quantity;
        $this->description = $description;
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getQuantity() { return $this->quantity; }
    public function getDescription() { return $this->description; }

    public function setName($name) { $this->name = $name; }
    public function setQuantity($quantity) { $this->quantity = $quantity; }
    public function setDescription($description) { $this->description = $description; }

    public function save($db) {
        $conn = $db->getConnection();
        if ($this->id === null) {
            // Thêm mới danh mục
            $stmt = $conn->prepare("INSERT INTO tbl_danhmuc (tendanhmuc, soluong, mota) VALUES (?, ?, ?)");
            $stmt->bind_param("sis", $this->name, $this->quantity, $this->description);
            $result = $stmt->execute();
            if ($result) {
                $this->id = $conn->insert_id; // Lấy ID mới tạo
            } else {
                error_log("Lỗi khi thêm danh mục: " . $conn->error);
            }
            $stmt->close();
            return $result;
        } else {
            // Cập nhật danh mục
            $stmt = $conn->prepare("UPDATE tbl_danhmuc SET tendanhmuc = ?, soluong = ?, mota = ? WHERE id_danhmuc = ?");
            $stmt->bind_param("sisi", $this->name, $this->quantity, $this->description, $this->id);
            $result = $stmt->execute();
            if (!$result) {
                error_log("Lỗi khi cập nhật danh mục: " . $conn->error);
            }
            $stmt->close();
            return $result;
        }
    }

    public function delete($db) {
        $conn = $db->getConnection();
        if ($this->id !== null) {
            $stmt = $conn->prepare("DELETE FROM tbl_danhmuc WHERE id_danhmuc = ?");
            $stmt->bind_param("i", $this->id);
            $result = $stmt->execute();
            if (!$result) {
                error_log("Lỗi khi xóa danh mục: " . $conn->error);
            }
            $stmt->close();
            return $result;
        }
        error_log("Không thể xóa danh mục: ID không hợp lệ");
        return false;
    }

    public static function getAll($db) {
        $conn = $db->getConnection();
        $result = $conn->query("SELECT * FROM tbl_danhmuc");
        $categories = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = new Category(
                    $row['id_danhmuc'],
                    $row['tendanhmuc'],
                    $row['soluong'],
                    $row['mota']
                );
            }
        } else {
            error_log("Không có danh mục nào trong getAll. Num rows: " . ($result ? $result->num_rows : "Lỗi truy vấn"));
        }
        return $categories;
    }
}
?>