<?php
class Order {
    private $conn;

    public function __construct($database) {
        $this->conn = $database->getConnection();
    }

    public function getOrdersWithPagination($page = 1, $per_page = 10) {
        $offset = ($page - 1) * $per_page;
        $query = "SELECT d.id_donhang, d.id_nguoidung, d.ngaylap, d.tongtien, d.tinhtrang, d.ghichu, n.hoten 
                  FROM tbl_donhang d 
                  LEFT JOIN tbl_nguoidung n ON d.id_nguoidung = n.id_nguoidung 
                  ORDER BY d.id_donhang ASC 
                  LIMIT ? OFFSET ?";
        
        try {
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("ii", $per_page, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            $orders = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $orders;
        } catch (Exception $e) {
            error_log("Error fetching orders: " . $e->getMessage());
            return [];
        }
    }

    public function getOrdersWithSearchPagination($search = '', $page = 1, $per_page = 10) {
        $offset = ($page - 1) * $per_page;
        $search = "%" . $this->conn->real_escape_string(trim($search)) . "%";
        $query = "SELECT d.id_donhang, d.id_nguoidung, d.ngaylap, d.tongtien, d.tinhtrang, d.ghichu, n.hoten 
                  FROM tbl_donhang d 
                  LEFT JOIN tbl_nguoidung n ON d.id_nguoidung = n.id_nguoidung 
                  WHERE d.id_donhang LIKE ? OR n.hoten LIKE ? 
                  ORDER BY d.id_donhang ASC 
                  LIMIT ? OFFSET ?";
        
        try {
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("ssii", $search, $search, $per_page, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            $orders = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $orders;
        } catch (Exception $e) {
            error_log("Error fetching orders with search: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalOrders() {
        $query = "SELECT COUNT(*) as total FROM tbl_donhang";
        try {
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $total = (int) $row['total'];
            } else {
                $total = 0;
            }
            $stmt->close();
            return $total;
        } catch (Exception $e) {
            error_log("Error fetching total orders: " . $e->getMessage());
            return 0;
        }
    }

    public function getTotalOrdersWithSearch($search = '') {
        $search = "%" . $this->conn->real_escape_string(trim($search)) . "%";
        $query = "SELECT COUNT(*) as total 
                  FROM tbl_donhang d 
                  LEFT JOIN tbl_nguoidung n ON d.id_nguoidung = n.id_nguoidung 
                  WHERE d.id_donhang LIKE ? OR n.hoten LIKE ?";
        
        try {
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("ss", $search, $search);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $total = (int) $row['total'];
            } else {
                $total = 0;
            }
            $stmt->close();
            return $total;
        } catch (Exception $e) {
            error_log("Error fetching total orders with search: " . $e->getMessage());
            return 0;
        }
    }

    public function getOrderById($id_donhang) {
        $query = "SELECT d.id_donhang, d.ngaylap, d.tongtien, d.tinhtrang, d.ghichu, d.id_nguoidung, n.hoten 
                  FROM tbl_donhang d 
                  LEFT JOIN tbl_nguoidung n ON d.id_nguoidung = n.id_nguoidung 
                  WHERE d.id_donhang = ?";
        
        try {
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("i", $id_donhang);
            $stmt->execute();
            $result = $stmt->get_result();
            $order = $result->fetch_assoc();
            $stmt->close();
            return $order;
        } catch (Exception $e) {
            error_log("Error fetching order by ID: " . $e->getMessage());
            return null;
        }
    }

    public function getOrderDetails($id_donhang) {
        $query = "SELECT c.id_chitietdonhang, c.id_sanpham, c.soluong, c.giatien, s.tensanpham 
                  FROM tbl_chitietdonhang c 
                  LEFT JOIN tbl_sanpham s ON c.id_sanpham = s.id_sanpham 
                  WHERE c.id_donhang = ?
                  ORDER BY c.id_chitietdonhang";
        
        try {
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("i", $id_donhang);
            $stmt->execute();
            $result = $stmt->get_result();
            $details = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $details;
        } catch (Exception $e) {
            error_log("Error fetching order details: " . $e->getMessage());
            return [];
        }
    }

    public function updateOrderStatus($id_donhang, $new_status) {
        $query = "UPDATE tbl_donhang 
                  SET tinhtrang = ? 
                  WHERE id_donhang = ?";
        
        try {
            $this->conn->set_charset("utf8mb4");
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("si", $new_status, $id_donhang);
            $success = $stmt->execute();
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            
            return $success && $affected_rows > 0;
        } catch (Exception $e) {
            error_log("Error updating order status: " . $e->getMessage());
            return false;
        }
    }

    public function getStatusBadgeClass($status) {
        switch ($status) {
            case 'Dang xuu ly':
                return 'bg-warning text-dark';
            case 'Da xac nhan':
                return 'bg-info text-white';
            case 'Dang giao hang':
                return 'bg-primary text-white';
            case 'Da giao hang':
                return 'bg-success text-white';
            case 'Da huy':
                return 'bg-danger text-white';
            case 'Cho xac nhan':
                return 'bg-secondary text-white';
            default:
                return 'bg-light text-dark';
        }
    }

    public function getStatusOptions($current_status = '') {
        $all_statuses = $this->getAllStatuses();
        // Nếu trạng thái hiện tại không xác định hoặc không hợp lệ, trả về tất cả trạng thái
        if (empty($current_status) || !in_array($current_status, $all_statuses)) {
            return $all_statuses;
        }
        // Loại bỏ trạng thái hiện tại khỏi danh sách tùy chọn
        $options = array_filter($all_statuses, function($s) use ($current_status) {
            return $s !== $current_status;
        });
        // Đảm bảo mảng được đánh số lại để tránh lỗi khi render
        return array_values($options);
    }

    public function canUpdateStatus($current_status, $new_status) {
        $transitions = $this->getStatusOptions($current_status);
        return in_array($new_status, $transitions);
    }

    public function getAllStatuses() {
        return ['Dang xu ly', 'Da xac nhan', 'Dang giao hang', 'Da giao hang', 'Da huy', 'Cho xac nhan'];
    }
}