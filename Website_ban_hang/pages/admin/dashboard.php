<?php
require_once '../../classes/Database.php';
require_once '../../classes/Product.php';
require_once '../../classes/Category.php';
require_once '../../config/db_config.php';

session_start();
$db = new Database($host, $username, $password, $dbname);

// Kiểm tra quyền admin
if (!isset($_SESSION['id_nguoidung']) || $_SESSION['vaitro'] != 'admin') {
    header("Location: ../user/home.php");
    exit();
}


?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LE.GICARFT | ADMIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        @media (max-width: 768px) {
            .dashboard-title {
                font-size: 2rem;
            }
            
            .admin-card {
                margin-bottom: 20px;
            }
            
            .card-icon {
                font-size: 3rem;
            }

            .brand-text {
                font-size: 1.3rem;
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

    <header class=" text-white py-3 shadow-lg">
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

    <div class="container mt-4">
        <div class="welcome-section">
            <h1 class="dashboard-title">BẢNG ĐIỀU KHIỂN HỆ THỐNG</h1>
            <p class="dashboard-subtitle">
                <i class="fas fa-crown me-2"></i>
                Quản lý toàn bộ hệ thống LE.GICARFT
            </p>
        </div>

        <!-- Các chức năng quản lý -->
        <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6">
                <div class="card admin-card text-center h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="card-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h4 class="card-title">Quản lý đơn hàng</h4>
                            <p class="card-text">Xem và cập nhật trạng thái các đơn hàng, theo dõi quy trình giao hàng</p>
                        </div>
                        <a href="manage_order.php" class="btn custom-btn mt-3">
                            <i class="fas fa-tasks me-2"></i>Quản lý đơn hàng
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card admin-card text-center h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="card-icon">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <h4 class="card-title">Quản lý sản phẩm</h4>
                            <p class="card-text">Thêm, sửa, xóa sản phẩm trong hệ thống, quản lý kho hàng</p>
                        </div>
                        <a href="manage_product.php" class="btn custom-btn mt-3">
                            <i class="fas fa-edit me-2"></i>Quản lý sản phẩm
                        </a>
                    </div>
                </div>
            </div>
            
            
        </div>
        
        <div class="row justify-content-center mt-4">
            <div class="col-lg-4 col-md-6">
                <div class="card admin-card text-center h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="card-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <h4 class="card-title">Thống kê doanh thu</h4>
                            <p class="card-text">Xem báo cáo và thống kê doanh thu, phân tích xu hướng kinh doanh</p>
                        </div>
                       <a href="manage_revenue.php" class="btn custom-btn mt-3">
                            <i class="fas fa-analytics me-2"></i>Xem thống kê
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card admin-card text-center h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="card-icon">
                                <i class="fas fa-tags"></i>
                            </div>
                            <h4 class="card-title">Quản lý danh mục</h4>
                            <p class="card-text">Thêm, sửa, xóa danh mục sản phẩm, tổ chức cấu trúc danh mục</p>
                        </div>
                        <a href="manage_category.php" class="btn custom-btn mt-3">
                            <i class="fas fa-folder-open me-2"></i>Quản lý danh mục
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add smooth animations on page load
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.admin-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Animate stats cards
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.4s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 50);
            });
        });

        // Refresh stats every 30 seconds
        setInterval(function() {
            fetch('get_stats.php')
                .then(response => response.json())
                .then(data => {
                    document.querySelector('.stat-card:nth-child(1) .stat-number').textContent = data.products;
                    document.querySelector('.stat-card:nth-child(2) .stat-number').textContent = data.orders;
                    document.querySelector('.stat-card:nth-child(3) .stat-number').textContent = data.users;
                    document.querySelector('.stat-card:nth-child(4) .stat-number').textContent = data.revenue + 'đ';
                })
                .catch(error => console.log('Stats refresh error:', error));
        }, 30000);
    </script>
</body>
</html>