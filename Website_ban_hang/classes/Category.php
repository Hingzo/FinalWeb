<?php
class Category {
    private $id;
    private $name;
    private $quantity;
    private $description;

    public function __construct($id, $name, $quantity, $description) {
        $this->id = $id;
        $this->name = $name;
        $this->quantity = $quantity;
        $this->description = $description;
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getQuantity() { return $this->quantity; }
    public function getDescription() { return $this->description; }

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