<?php
require_once '../../classes/Database.php';
require_once '../../classes/ManaOrder.php';
require_once '../../config/db_config.php';

session_start();
if (!isset($_SESSION['id_nguoidung']) || $_SESSION['vaitro'] != 'admin') {
    header("Location: ../user/home.php");
    exit();
}

try {
    $db = new Database($host, $username, $password, $dbname);
    $db->getConnection()->set_charset("utf8mb4");
} catch (Exception $e) {
    die("Không thể kết nối đến cơ sở dữ liệu: " . $e->getMessage());
}

$order = new Order($db);

$message = $error = '';

// Xử lý cập nhật trạng thái đơn hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $id_donhang = $_POST['id_donhang'] ?? 0;
    $new_status = $_POST['new_status'] ?? '';
    
    if ($id_donhang && $new_status) {
        $current_order = $order->getOrderById($id_donhang);
        
        if ($current_order && $order->canUpdateStatus($current_order['tinhtrang'], $new_status)) {
            if ($order->updateOrderStatus($id_donhang, $new_status)) {
                $message = "Cập nhật trạng thái đơn hàng #$id_donhang thành công!";
            } else {
                $error = "Có lỗi xảy ra khi cập nhật trạng thái đơn hàng #$id_donhang!";
            }
        } else {
            $error = "Không thể chuyển trạng thái này hoặc đơn hàng không tồn tại!";
        }
    } else {
        $error = "Thông tin không hợp lệ!";
    }
    
    // Redirect để tránh resubmit form
    header("Location: " . $_SERVER['PHP_SELF'] . "?page=" . ($_GET['page'] ?? 1) . ($message ? "&msg=success" : "&msg=error"));
    exit();
}

// Hiển thị thông báo từ redirect
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'success') {
        $message = "Cập nhật trạng thái đơn hàng thành công!";
    } elseif ($_GET['msg'] === 'error') {
        $error = "Có lỗi xảy ra khi cập nhật trạng thái!";
    }
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 10;

if ($search) {
    $orders = $order->getOrdersWithSearchPagination($search, $page, $per_page);
    $total_orders = $order->getTotalOrdersWithSearch($search);
} else {
    $orders = $order->getOrdersWithPagination($page, $per_page);
    $total_orders = $order->getTotalOrders();
}

$total_pages = ceil($total_orders / $per_page);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LE.GICARFT | Quản lý đơn hàng</title>
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
            background: #f5f5f5;
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

        .welcome-section {
            background: linear-gradient(135deg, #4f92a5 0%, #3d7589 100%);
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
            box-shadow: 0 6px 20px rgba(79, 146, 165, 0.2);
        }

        .dashboard-title {
            color: white;
            font-size: 2rem;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .dashboard-subtitle {
            color: rgba(255,255,255,0.8);
            font-size: 1rem;
        }

        .admin-card {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .admin-card .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: var(--text-dark);
            border-bottom: 2px solid #dee2e6;
        }

        .table td {
            vertical-align: middle;
        }

        .custom-btn {
            background: var(--primary-color);
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .custom-btn:hover {
            background: var(--primary-hover);
            color: white;
            transform: translateY(-1px);
        }

        .view-btn {
            background: #6c757d;
            border: none;
            padding: 8px 12px;
            border-radius: 20px;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 0.875rem;
        }

        .view-btn:hover {
            background: #5a6268;
            color: white;
            transform: translateY(-1px);
        }

        .update-btn {
            background: #28a745;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .update-btn:hover {
            background: #218838;
            color: white;
            transform: translateY(-1px);
        }

        .logout-btn {
            background: transparent;
            border: 2px solid rgba(255,255,255,0.7);
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.2);
            border-color: white;
            color: white;
            text-decoration: none;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .order-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .modal-header {
            background: var(--primary-color);
            color: white;
            border-radius: 15px 15px 0 0;
        }

        .modal-header .btn-close {
            filter: invert(1);
        }

        .order-info-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .detail-item {
            margin-bottom: 8px;
        }

        .detail-label {
            font-weight: 600;
            color: var(--text-dark);
        }

        .pagination .page-link {
            color: var(--primary-color);
            border-radius: 20px;
            margin: 0 2px;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .pagination .page-item.disabled .page-link {
            color: #6c757d;
        }

        .loading-spinner {
            display: none;
        }

        .btn-loading .loading-spinner {
            display: inline-block;
        }

        .btn-loading .btn-text {
            display: none;
        }

        .search-container {
            margin: 20px 0;
            display: flex;
            gap: 10px;
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
                font-size: 1.5rem;
            }
            .dashboard-subtitle {
                font-size: 0.9rem;
            }
            .table th, .table td {
                font-size: 0.85rem;
                padding: 8px 4px;
            }
            .order-actions {
                flex-direction: column;
            }
            .order-actions .btn {
                margin-bottom: 4px;
            }
            .update-btn {
                padding: 8px 16px;
                font-size: 0.875rem;
            }
            .search-container {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
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
                            <li><a class="dropdown-item active" href="manage_order.php">Quản lý đơn hàng</a></li>
                            <li><a class="dropdown-item" href="manage_product.php">Quản lý sản phẩm</a></li>
                            <li><a class="dropdown-item" href="manage_revenue.php">Thống kê doanh thu</a></li>
                            <li><a class="dropdown-item" href="manage_category.php">Quản lý danh mục</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="welcome-section">
                    <h1 class="dashboard-title">
                        <i class="fas fa-shopping-cart me-3"></i>QUẢN LÝ ĐƠN HÀNG
                    </h1>
                    <p class="dashboard-subtitle">
                        Theo dõi và cập nhật trạng thái các đơn hàng của khách hàng
                    </p>
                </div>

                <!-- Thanh tìm kiếm -->
                <div class="search-container">
                    <form method="GET" class="d-flex" style="width: 90%; height: 40px;">
                        <input type="text" name="search" class="form-control" placeholder="Tìm theo mã ĐH hoặc tên khách..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" style="width: 200px;" class="btn d-flex align-items-center justify-content-center">
                            <i class="fas fa-search me-2"></i>Tìm kiếm
                        </button>
                    </form>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="admin-card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-hashtag me-2"></i>Mã ĐH</th>
                                        <th><i class="fas fa-user me-2"></i>Khách hàng</th>
                                        <th><i class="fas fa-calendar me-2"></i>Ngày đặt</th>
                                        <th><i class="fas fa-money-bill me-2"></i>Tổng tiền</th>
                                        <th><i class="fas fa-info-circle me-2"></i>Trạng thái</th>
                                        <th><i class="fas fa-sticky-note me-2"></i>Ghi chú</th>
                                        <th><i class="fas fa-cogs me-2"></i>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($orders)): ?>
                                        <?php foreach ($orders as $ord): ?>
                                            <tr>
                                                <td><strong>#<?php echo $ord['id_donhang']; ?></strong></td>
                                                <td><?php echo htmlspecialchars($ord['hoten'] ?? 'N/A'); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($ord['ngaylap'])); ?></td>
                                                <td><strong><?php echo number_format($ord['tongtien'], 0, ',', '.') . ' VNĐ'; ?></strong></td>
                                                <td>
                                                    <span class="status-badge <?php echo $order->getStatusBadgeClass($ord['tinhtrang']); ?>">
                                                        <?php echo htmlspecialchars($ord['tinhtrang'] ?: 'Chưa xác định'); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $ghichu = htmlspecialchars($ord['ghichu'] ?? '');
                                                    echo $ghichu ? (strlen($ghichu) > 30 ? substr($ghichu, 0, 30) . '...' : $ghichu) : '<em>Không có</em>';
                                                    ?>
                                                </td>
                                                <td>
                                                    <div class="order-actions">
                                                        <button class="btn view-btn btn-sm" data-bs-toggle="modal" data-bs-target="#detailsModal<?php echo $ord['id_donhang']; ?>">
                                                            <i class="fas fa-eye me-1"></i>Chi tiết
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">Không có đơn hàng nào<?php echo $search ? ' khớp với "' . htmlspecialchars($search) . '"' : ''; ?></p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <nav aria-label="Phân trang đơn hàng" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                            <i class="fas fa-chevron-left"></i> Trước
                                        </a>
                                    </li>
                                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                        <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                            Sau <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Modals -->
                <?php foreach ($orders as $ord): ?>
                    <!-- Modal xem chi tiết -->
                    <div class="modal fade" id="detailsModal<?php echo $ord['id_donhang']; ?>" tabindex="-1" aria-labelledby="detailsModalLabel<?php echo $ord['id_donhang']; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="detailsModalLabel<?php echo $ord['id_donhang']; ?>">
                                        <i class="fas fa-file-invoice me-2"></i>Chi tiết đơn hàng #<?php echo $ord['id_donhang']; ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="order-info-card">
                                                <div class="detail-item">
                                                    <span class="detail-label">Mã đơn hàng:</span> #<?php echo $ord['id_donhang']; ?>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label">Khách hàng:</span> <?php echo htmlspecialchars($ord['hoten'] ?? 'N/A'); ?>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label">Ngày đặt:</span> <?php echo date('d/m/Y', strtotime($ord['ngaylap'])); ?>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label">Tổng tiền:</span> <strong><?php echo number_format($ord['tongtien'], 0, ',', '.') . ' VNĐ'; ?></strong>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label">Trạng thái:</span> 
                                                    <span class="status-badge <?php echo $order->getStatusBadgeClass($ord['tinhtrang']); ?>">
                                                        <?php echo htmlspecialchars($ord['tinhtrang'] ?: 'Chưa xác định'); ?>
                                                    </span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label">Ghi chú:</span> <?php echo htmlspecialchars($ord['ghichu'] ?? 'Không có'); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="mb-3">Chi tiết sản phẩm</h6>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Tên sản phẩm</th>
                                                        <th>Số lượng</th>
                                                        <th>Giá tiền</th>
                                                        <th>Thành tiền</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $details = $order->getOrderDetails($ord['id_donhang']);
                                                    $totalDetail = 0;
                                                    foreach ($details as $item):
                                                        $itemTotal = $item['soluong'] * $item['giatien'];
                                                        $totalDetail += $itemTotal;
                                                    ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($item['tensanpham'] ?? 'N/A'); ?></td>
                                                            <td><?php echo $item['soluong']; ?></td>
                                                            <td><?php echo number_format($item['giatien'], 0, ',', '.') . ' VNĐ'; ?></td>
                                                            <td><?php echo number_format($itemTotal, 0, ',', '.') . ' VNĐ'; ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                    <?php if (empty($details)): ?>
                                                        <tr><td colspan="4" class="text-center">Không có sản phẩm nào</td></tr>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                                                            <td><strong><?php echo number_format($totalDetail, 0, ',', '.') . ' VNĐ'; ?></strong></td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn update-btn" data-bs-toggle="modal" data-bs-target="#updateModal<?php echo $ord['id_donhang']; ?>" title="Cập nhật trạng thái đơn hàng">
                                        <i class="fas fa-edit me-2"></i>Cập nhật trạng thái
                                    </button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="fas fa-times me-2"></i>Đóng
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal cập nhật trạng thái -->
                    <div class="modal fade" id="updateModal<?php echo $ord['id_donhang']; ?>" tabindex="-1" aria-labelledby="updateModalLabel<?php echo $ord['id_donhang']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="updateModalLabel<?php echo $ord['id_donhang']; ?>">
                                        <i class="fas fa-edit me-2"></i>Cập nhật trạng thái đơn hàng #<?php echo $ord['id_donhang']; ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="order-info-card">
                                        <div class="detail-item">
                                            <span class="detail-label">Khách hàng:</span> <?php echo htmlspecialchars($ord['hoten'] ?? 'N/A'); ?>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Tổng tiền:</span> <strong><?php echo number_format($ord['tongtien'], 0, ',', '.') . ' VNĐ'; ?></strong>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Trạng thái hiện tại:</span> 
                                            <span class="status-badge <?php echo $order->getStatusBadgeClass($ord['tinhtrang']); ?>">
                                                <?php echo htmlspecialchars($ord['tinhtrang'] ?: 'Chưa xác định'); ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <?php
                                    $available_statuses = $order->getStatusOptions($ord['tinhtrang']);
                                    if (empty($available_statuses)):
                                    ?>
                                        <div class="alert alert-warning" role="alert">
                                            <i class="fas fa-exclamation-triangle me-2"></i>Không có trạng thái nào khả dụng để chuyển đổi. Vui lòng kiểm tra dữ liệu.
                                        </div>
                                    <?php else: ?>
                                        <form method="POST" class="update-status-form">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="id_donhang" value="<?php echo $ord['id_donhang']; ?>">
                                            <div class="mb-3">
                                                <label for="new_status_<?php echo $ord['id_donhang']; ?>" class="form-label">
                                                    <i class="fas fa-arrow-right me-2"></i>Chọn trạng thái mới
                                                </label>
                                                <select name="new_status" id="new_status_<?php echo $ord['id_donhang']; ?>" class="form-control" required>
                                                    <option value="">-- Chọn trạng thái --</option>
                                                    <?php foreach ($available_statuses as $status): ?>
                                                        <option value="<?php echo htmlspecialchars($status); ?>"><?php echo htmlspecialchars($status); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn custom-btn" id="submitBtn_<?php echo $ord['id_donhang']; ?>">
                                                    <span class="loading-spinner spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                                    <span class="btn-text"><i class="fas fa-save me-2"></i>Cập nhật</span>
                                                </button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <i class="fas fa-times me-2"></i>Hủy
                                                </button>
                                            </div>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="text-center mt-4">
                    <a href="dashboard.php" class="btn custom-btn">
                        <i class="fas fa-tachometer-alt me-2"></i>Về Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Xử lý loading khi submit form cập nhật trạng thái
            document.querySelectorAll('.update-status-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    submitBtn.classList.add('btn-loading');
                    submitBtn.disabled = true;
                });
            });

            // Đặt lại trạng thái tải khi modal đóng
            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('hidden.bs.modal', function() {
                    const submitBtn = document.querySelector('#submitBtn_' + this.id.replace('updateModal', ''));
                    if (submitBtn) {
                        submitBtn.classList.remove('btn-loading');
                        submitBtn.disabled = false;
                    }
                });
            });

            // Đóng modal chi tiết khi mở modal cập nhật
            document.querySelectorAll('.update-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const detailsModal = document.querySelector('#detailsModal' + this.getAttribute('data-bs-target').replace('#updateModal', ''));
                    if (detailsModal) {
                        const modalInstance = bootstrap.Modal.getInstance(detailsModal);
                        if (modalInstance) modalInstance.hide();
                    }
                });
            });
        });
    </script>
</body>
</html>