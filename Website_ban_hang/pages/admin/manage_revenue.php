<?php
require_once '../../classes/Database.php';
require_once '../../classes/Revenue.php';
require_once '../../config/db_config.php';

session_start();
try {
    $db = new Database($host, $username, $password, $dbname);
    // Kiểm tra quyền admin
    if (!isset($_SESSION['id_nguoidung']) || $_SESSION['vaitro'] != 'admin') {
        header("Location: ../user/home.php");
        exit();
    }

    $revenue = new Revenue($db);
    $stats = $revenue->getRevenueStats();
    $totalRevenue = $stats['total_revenue'];
    $orderStats = $stats['stats'];
    $monthlyRevenue = $revenue->getMonthlyRevenue();
    $topProducts = $revenue->getTop5Products();
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LE.GICARFT | Thống kê doanh thu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #4f92a5;
            --primary-hover: #3d7589;
            --accent-color: #f8f9fa;
            --text-dark: #2c3e50;
        }

        body {
            background: #ffffff;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .header-gradient {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: white;
            padding: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .brand-text {
            font-size: 1.8rem;
            font-weight: bold;
            background: linear-gradient(45deg, #74b9ff, #00cec9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .welcome-section {
            background: linear-gradient(135deg, #4f92a5 0%, #3d7589 100%);
            border-radius: 20px;
            padding: 30px;
            margin: 30px 0;
            text-align: center;
            box-shadow: 0 8px 32px rgba(79, 146, 165, 0.2);
        }

        .stats-section {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .stat-card {
            background: rgba(255,255,255,0.9);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .admin-card {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 20px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            margin-bottom: 30px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .admin-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), #74b9ff);
        }

        .admin-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .card-icon {
            font-size: 4rem;
            background: linear-gradient(135deg, var(--primary-color), #74b9ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .admin-card:hover .card-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .custom-btn {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(79, 146, 165, 0.3);
            position: relative;
            overflow: hidden;
            text-decoration: none;
        }

        .custom-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 146, 165, 0.4);
            background: linear-gradient(135deg, var(--primary-hover), var(--primary-color));
            color: white;
            text-decoration: none;
        }

        .logout-btn {
            background: transparent;
            border: 2px solid rgba(255,255,255,0.7);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.2);
            border-color: white;
            color: white;
            transform: translateY(-1px);
            text-decoration: none;
        }

        .card-title {
            color: var(--text-dark);
            font-weight: 700;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }

        .card-text {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .dashboard-title {
            color: white;
            text-align: center;
            font-size: 2.5rem;
            font-weight: 300;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .dashboard-subtitle {
            color: rgba(255,255,255,0.8);
            text-align: center;
            font-size: 1.1rem;
            margin-bottom: 0;
        }

        .floating-shapes {
            position: fixed;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .shape {
            position: absolute;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            top: 10%;
            left: 10%;
            width: 60px;
            height: 60px;
            background: #74b9ff;
            border-radius: 50%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            top: 20%;
            right: 20%;
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            transform: rotate(45deg);
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            bottom: 20%;
            left: 20%;
            width: 50px;
            height: 50px;
            background: #00cec9;
            clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .chart-container {
            position: relative;
            margin: auto;
            height: 400px;
            width: 600px;
        }

        /* Thêm style cho sidebar */
        .sidebar {
            background-color: #5a9bb8;
            min-height: 100vh;
        }

        .sidebar .nav-link {
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px 0;
        }

        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.2);
            color: white;
        }

        @media (max-width: 768px) {
            .dashboard-title {
                font-size: 2rem;
            }
            .chart-container {
                width: 100%;
                height: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <header class="text-white py-3 shadow-lg">
        <div class="container d-flex justify-content-between align-items-center me-2">
            <div class="logo-container">
                <img src="../../assets/images/logo.png" alt="LE.GICARFT Logo" width="100" height="60">
            </div>
            <div class="user-cart">
                <span class="text-black me-3">
                    <i class="fas fa-user-shield me-2"></i>
                    Xin chào, <strong><?php echo htmlspecialchars($_SESSION['hoten']); ?></strong>!
                </span>
                <a href="../user/logout.php" class="btn logout-btn text-black">
                    <i class="fas fa-sign-out-alt me-2 text-black"></i>Đăng xuất
                </a>
            </div>
        </div>
    </header>

    <!-- Container chính -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="p-3">
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle d-flex align-items-center w-100" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bars me-2"></i> Chức năng quản lý
                        </button>
                        <ul class="dropdown-menu w-100">
                             <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
                            <li><a class="dropdown-item" href="manage_order.php">Quản lý đơn hàng</a></li>
                            <li><a class="dropdown-item" href="manage_product.php">Quản lý sản phẩm</a></li>
                            <li><a class="dropdown-item active" href="manage_revenue.php">Thống kê doanh thu</a></li>
                            <li><a class="dropdown-item" href="manage_category.php">Quản lý danh mục</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="welcome-section">
                    <h1 class="dashboard-title fw-bold">
                        <i class="fas fa-chart-bar me-3"></i>THỐNG KÊ DOANH THU
                    </h1>
                    <p class="dashboard-subtitle">
                        Phân tích và theo dõi doanh thu cửa hàng
                    </p>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-12">
                        <div class="admin-card text-center h-100">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <div class="card-icon">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                    <h4 class="card-title">Tổng doanh thu</h4>
                                    <p class="card-text"><?= number_format($totalRevenue, 0, ',', '.') ?> đ</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center mt-4">
                    <div class="col-lg-6 col-md-12">
                        <div class="admin-card text-center h-150">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <div class="card-icon">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                    <h4 class="card-title">Tổng doanh thu theo tháng</h4>
                                    <ul class="list-group">
                                        <?php foreach ($monthlyRevenue as $month => $revenue): ?>
                                            <li class="list-group-item"><?= htmlspecialchars($month) ?>: <?= number_format($revenue, 0, ',', '.') ?> đ</li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div class="row justify-content-center mt-4">
                    <div class="col-lg-6 col-md-12">
                        <div class="admin-card text-center h-100">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <div class="card-icon">
                                        <i class="fas fa-trophy"></i>
                                    </div>
                                    <h4 class="card-title">Top 5 sản phẩm bán chạy</h4>
                                    <ul class="list-group">
                                        <?php foreach ($topProducts as $index => $product): ?>
                                            <li class="list-group-item">
                                                <?= ($index + 1) . ". " . htmlspecialchars($product['ten_sanpham'] ?? 'Không có tên') ?>: 
                                                <?= $product['total_quantity_sold'] ?? 0 ?> sản phẩm (<?= number_format($product['total_sales'] ?? 0, 0, ',', '.') ?> đ)
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

                <div class="row justify-content-center mt-4">
                    <div class="col-lg-8 col-md-12">
                        <div class="admin-card text-center h-80 px-3 py-3" style="max-width: 700px; margin: 0 auto;">
                            <div class="card-body">
                                <h4 class="card-title text-break mb-4" style="word-break: break-word; white-space: normal;">
                                    <strong>Biểu đồ tăng trưởng doanh thu trong năm</strong>
                                </h4>
                                <div class="chart-container">
                                    <canvas id="growthChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
 <div class="text-center mt-4">
                    <a href="dashboard.php" class="btn custom-btn">
                        <i class="fas fa-tachometer-alt me-2"></i>Về Dashboard
                    </a>
                </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const ctxGrowth = document.getElementById('growthChart').getContext('2d');
        const growthChart = new Chart(ctxGrowth, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_keys($monthlyRevenue)) ?>,
                datasets: [{
                    label: 'Doanh thu (đ)',
                    data: <?= json_encode(array_values($monthlyRevenue)) ?>,
                    backgroundColor: 'rgba(79, 146, 165, 0.2)',
                    borderColor: 'rgba(79, 146, 165, 1)',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('vi-VN') + ' đ';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>