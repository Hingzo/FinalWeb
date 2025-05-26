<?php
class Product {
    private $id;
    private $name;
    private $price;
    private $quantity;
    private $description;
    private $categoryId;
    private $image;

    public function __construct($id, $name, $price, $quantity, $description, $categoryId, $image) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->description = $description;
        $this->categoryId = $categoryId;
        $this->image = $image;
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getPrice() { return $this->price; }
    public function getQuantity() { return $this->quantity; }
    public function getDescription() { return $this->description; }
    public function getCategoryId() { return $this->categoryId; }
    public function getImage() { return $this->image; }

    public static function getFeatured($db) {
        $conn = $db->getConnection();
        $result = $conn->query("SELECT * FROM tbl_sanpham LIMIT 4");
        $products = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = new Product(
                    $row['id_sanpham'],
                    $row['tensanpham'],
                    $row['giasanpham'],
                    $row['soluongsanpham'],
                    $row['mota'],
                    $row['id_danhmuc'],
                    $row['hinh_anh']
                );
            }
        } else {
            error_log("Không có sản phẩm nào trong getFeatured. Num rows: " . ($result ? $result->num_rows : "Lỗi truy vấn"));
        }
        return $products;
    }

    public static function getAll($db) {
        $conn = $db->getConnection();
        $result = $conn->query("SELECT * FROM tbl_sanpham");
        $products = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = new Product(
                    $row['id_sanpham'],
                    $row['tensanpham'],
                    $row['giasanpham'],
                    $row['soluongsanpham'],
                    $row['mota'],
                    $row['id_danhmuc'],
                    $row['hinh_anh']
                );
            }
        } else {
            error_log("Không có sản phẩm nào trong getAll. Num rows: " . ($result ? $result->num_rows : "Lỗi truy vấn"));
        }
        return $products;
    }

    public static function search($db, $keyword) {
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM tbl_sanpham WHERE LOWER(tensanpham) LIKE LOWER(?) OR LOWER(mota) LIKE LOWER(?)");
        $searchTerm = "%" . $keyword . "%";
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = new Product(
                    $row['id_sanpham'],
                    $row['tensanpham'],
                    $row['giasanpham'],
                    $row['soluongsanpham'],
                    $row['mota'],
                    $row['id_danhmuc'],
                    $row['hinh_anh']
                );
            }
        } else {
            error_log("Không tìm thấy sản phẩm với từ khóa: $keyword. Num rows: " . ($result ? $result->num_rows : "Lỗi truy vấn"));
        }
        return $products;
    }

    public static function getById($db, $id) {
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM tbl_sanpham WHERE id_sanpham = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return new Product(
                $row['id_sanpham'],
                $row['tensanpham'],
                $row['giasanpham'],
                $row['soluongsanpham'],
                $row['mota'],
                $row['id_danhmuc'],
                $row['hinh_anh']
            );
        }
        error_log("Không tìm thấy sản phẩm với ID: $id");
        return null;
    }
public static function getByCategory($db, $categoryId) {
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM tbl_sanpham WHERE id_danhmuc = ?");
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = new Product(
                    $row['id_sanpham'],
                    $row['tensanpham'],
                    $row['giasanpham'],
                    $row['soluongsanpham'],
                    $row['mota'],
                    $row['id_danhmuc'],
                    $row['hinh_anh']
                );
            }
        } else {
            error_log("Không có sản phẩm nào trong danh mục ID: $categoryId. Num rows: " . ($result ? $result->num_rows : "Lỗi truy vấn"));
        }
        return $products;
    }
    public function delete($db) {
        $conn = $db->getConnection();
        $stmt = $conn->prepare("DELETE FROM tbl_sanpham WHERE id_sanpham = ?");
        if ($stmt === false) {
            error_log("Lỗi chuẩn bị truy vấn xóa sản phẩm: " . $conn->error);
            return false;
        }
        $stmt->bind_param("i", $this->id);
        $result = $stmt->execute();
        if (!$result) {
            error_log("Lỗi khi xóa sản phẩm ID: " . $this->id . " - " . $stmt->error);
        }
        $stmt->close();
        return $result;
    }
}
?>