<?php
class Revenue {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getRevenueStats() {
        $conn = $this->db->getConnection();
        $query = "SELECT 
            COUNT(*) as total_orders,
            SUM(tongtien) as total_revenue,
            tinhtrang,
            COUNT(*) as status_count
        FROM tbl_donhang
        GROUP BY tinhtrang WITH ROLLUP";
        $result = $conn->query($query);

        $stats = [];
        $totalRevenue = 0;
        while ($row = $result->fetch_assoc()) {
            if ($row['tinhtrang'] === NULL) {
                $totalRevenue = $row['total_revenue'] ?? 0;
            } else {
                $stats[$row['tinhtrang']] = $row['status_count'];
            }
        }
        return ['total_revenue' => $totalRevenue, 'stats' => $stats];
    }

    public function getMonthlyRevenue() {
        $conn = $this->db->getConnection();
        $query = "SELECT 
            DATE_FORMAT(ngaylap, '%Y-%m') as month,
            SUM(tongtien) as monthly_revenue
        FROM tbl_donhang
        GROUP BY DATE_FORMAT(ngaylap, '%Y-%m')
        ORDER BY month";
        $result = $conn->query($query);

        $monthlyRevenue = [];
        while ($row = $result->fetch_assoc()) {
            $monthlyRevenue[$row['month']] = $row['monthly_revenue'] ?? 0;
        }
        return $monthlyRevenue;
    }

    public function getTop5Products() {
        $conn = $this->db->getConnection();
        $query = "SELECT 
            sp.tensanpham,
            SUM(ctdh.soluong) as total_quantity_sold,
            SUM(ctdh.giatien * ctdh.soluong) as total_sales
        FROM tbl_donhang dh
        JOIN tbl_chitietdonhang ctdh ON dh.id_donhang = ctdh.id_donhang
        JOIN tbl_sanpham sp ON ctdh.id_sanpham = sp.id_sanpham
        GROUP BY sp.id_sanpham, sp.tensanpham
        ORDER BY total_quantity_sold DESC
        LIMIT 5";
        $result = $conn->query($query);

        $topProducts = [];
        while ($row = $result->fetch_assoc()) {
            $topProducts[] = [
                'tensanpham' => $row['tensanpham'],
                'total_quantity_sold' => $row['total_quantity_sold'],
                'total_sales' => $row['total_sales']
            ];
        }
        return $topProducts;
    }
}
?>