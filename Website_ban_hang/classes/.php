<?php
class Order {
    private $id;
    private $date;
    private $total;
    private $status;
    private $note;
    private $userId;

    public function __construct($id = null, $date = null, $total = 0, $status = 'Cho xac nhan', $note = '', $userId = null) {
        $this->id = $id;
        $this->date = $date ?? date('Y-m-d');
        $this->total = $total;
        $this->status = $status;
        $this->note = $note;
        $this->userId = $userId;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getDate() { return $this->date; }
    public function getTotal() { return $this->total; }
    public function getStatus() { return $this->status; }
    public function getNote() { return $this->note; }
    public function getUserId() { return $this->userId; }

    // Setters
    public function setTotal($total) { $this->total = $total; }
    public function setNote($note) { $this->note = $note; }

    /**
     * Tạo đơn hàng mới từ giỏ hàng
     */
    public static function createFromCart($db, $userId, $cart, $note = '') {
    $conn = $db->getConnection();
    
    if (!$conn) {
        error_log("Không thể kết nối đến cơ sở dữ liệu");
        throw new Exception("Không thể kết nối đến cơ sở dữ liệu");
    }
    
    try {
        // Bắt đầu transaction
        $conn->begin_transaction();
        
        // Tính tổng tiền
        $total = 0;
        $orderItems = [];
        
        foreach ($cart as $productId => $quantity) {
            if (!is_numeric($productId) || $productId <= 0) {
                error_log("ID sản phẩm không hợp lệ: " . $productId);
                throw new Exception("ID sản phẩm không hợp lệ: " . $productId);
            }
            $product = Product::getById($db, $productId);
            if ($product) {
                $subtotal = $product->getPrice() * $quantity;
                $total += $subtotal;
                $orderItems[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $product->getPrice()
                ];
            } else {
                error_log("Sản phẩm với ID $productId không tồn tại");
                throw new Exception("Sản phẩm với ID $productId không tồn tại");
            }
        }
        
        // Tạo đơn hàng
        $stmt = $conn->prepare("INSERT INTO tbl_donhang (ngaylap, tongtien, tinhtrang, ghichu, id_nguoidung) VALUES (?, ?, ?, ?, ?)");
        $date = date('Y-m-d');
        $status = 'Cho xac nhan';
        $stmt->bind_param("sdssi", $date, $total, $status, $note, $userId);
        
        if (!$stmt->execute()) {
            error_log("Lỗi khi tạo đơn hàng: " . $stmt->error);
            throw new Exception("Lỗi khi tạo đơn hàng: " . $stmt->error);
        }
        
        $orderId = $conn->insert_id;
        error_log("Tạo đơn hàng thành công, order ID: $orderId");
        
        // Thêm chi tiết đơn hàng
        $stmt = $conn->prepare("INSERT INTO tbl_chitietdonhang (id_donhang, id_sanpham, soluong, giatien) VALUES (?, ?, ?, ?)");
        
        foreach ($orderItems as $item) {
            $stmt->bind_param("iiid", $orderId, $item['product_id'], $item['quantity'], $item['price']);
            if (!$stmt->execute()) {
                error_log("Lỗi khi thêm chi tiết đơn hàng: " . $stmt->error);
                throw new Exception("Lỗi khi thêm chi tiết đơn hàng: " . $stmt->error);
            }
        }
        
        // Commit transaction
        $conn->commit();
        error_log("Hoàn tất transaction cho order ID: $orderId");
        
        return new Order($orderId, $date, $total, $status, $note, $userId);
        
    } catch (Exception $e) {
        // Rollback transaction nếu có lỗi
        $conn->rollback();
        error_log("Lỗi tạo đơn hàng: " . $e->getMessage());
        throw $e;
    }
}

    /**
     * Lấy đơn hàng theo ID
     */
    public static function getById($db, $orderId) {
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM tbl_donhang WHERE id_donhang = ?");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return new Order(
                $row['id_donhang'],
                $row['ngaylap'],
                $row['tongtien'],
                $row['tinhtrang'],
                $row['ghichu'],
                $row['id_nguoidung']
            );
        }
        
        return null;
    }

    /**
     * Lấy chi tiết đơn hàng
     */
    public function getOrderDetails($db) {
        $conn = $db->getConnection();
        $stmt = $conn->prepare("
            SELECT ct.*, sp.tensanpham, sp.hinh_anh 
            FROM tbl_chitietdonhang ct 
            JOIN tbl_sanpham sp ON ct.id_sanpham = sp.id_sanpham 
            WHERE ct.id_donhang = ?
        ");
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $details = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $details[] = [
                    'id_chitietdonhang' => $row['id_chitietdonhang'],
                    'id_sanpham' => $row['id_sanpham'],
                    'tensanpham' => $row['tensanpham'],
                    'hinh_anh' => $row['hinh_anh'],
                    'soluong' => $row['soluong'],
                    'giatien' => $row['giatien'],
                    'thanhtien' => $row['soluong'] * $row['giatien']
                ];
            }
        }
        
        return $details;
    }

    /**
     * Lấy danh sách đơn hàng của user
     */
    public static function getByUserId($db, $userId) {
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM tbl_donhang WHERE id_nguoidung = ? ORDER BY ngaylap DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orders = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $orders[] = new Order(
                    $row['id_donhang'],
                    $row['ngaylap'],
                    $row['tongtien'],
                    $row['tinhtrang'],
                    $row['ghichu'],
                    $row['id_nguoidung']
                );
            }
        }
        
        return $orders;
    }

    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateStatus($db, $newStatus) {
        $conn = $db->getConnection();
        $stmt = $conn->prepare("UPDATE tbl_donhang SET tinhtrang = ? WHERE id_donhang = ?");
        $stmt->bind_param("si", $newStatus, $this->id);
        
        if ($stmt->execute()) {
            $this->status = $newStatus;
            return true;
        }
        
        return false;
    }

    /**
     * Format trạng thái đơn hàng
     */
    public function getFormattedStatus() {
        $statusMap = [
            'Cho xac nhan' => 'Chờ xác nhận',
            'Dang xu ly' => 'Đang xử lý',
            'Dang giao' => 'Đang giao',
            'Da giao' => 'Đã giao',
            'Da huy' => 'Đã hủy'
        ];
        
        return $statusMap[$this->status] ?? $this->status;
    }

    /**
     * Format ngày tháng
     */
    public function getFormattedDate() {
        return date('d/m/Y', strtotime($this->date));
    }
}
?>