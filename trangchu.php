<?php
session_start();
require_once __DIR__ . '/role_helper.php';

// 1. Kiểm tra bảo mật: Nếu chưa đăng nhập thì bắt quay lại trang dangnhap.php
if (!isset($_SESSION['user'])) {
    header("Location: dangnhap.php");
    exit;
}

if (!isset($_SESSION['user'])) {
    header("Location: dangnhap.php");
    exit;
}

$role = $_SESSION['user']['role'] ?? 'guest';//

// PHÂN QUYỀN MENU
$menus = [
    'admin' => ['all' => true],
    'staff' => ['phieunhap'=>true,'phieuxuat'=>true,'khachhang'=>true,'baocao'=>true,'sanpham'=>true],
    'sanxuat' => ['sanxuat'=>true,'baocao'=>true]
];

$permission = $menus[$role] ?? [];

// Lấy thông tin người dùng từ Session để hiển thị
$user = $_SESSION['user'];
$role = $user['role'] ?? 'guest';
$roleName = getRoleName($role);

// Dữ liệu thống kê của bạn
$stats = [
    'don_hang_moi' => ['so' => '0', 'tien' => '0đ', 'icon' => 'shopping-cart', 'color' => 'text-primary'],
    'don_hang_cho' => ['so' => '0', 'icon' => 'clock', 'color' => 'text-warning'],
    'tong_tien_kh' => ['so' => '1.785.000đ', 'icon' => 'dollar-sign', 'color' => 'text-success'],
    'cong_no_hang' => ['so' => '2.125.000đ', 'icon' => 'file-invoice-dollar', 'color' => 'text-danger'],
    'ton_kho' => ['so' => '4', 'icon' => 'warehouse', 'color' => 'text-info'],
    'san_pham_het' => ['so' => '1', 'icon' => 'exclamation-triangle', 'color' => 'text-danger']
];
$page_title = "Trang Chủ - Quản Lý Kho Hàng";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background-color: #f8f9fa; 
            font-family: 'Segoe UI', sans-serif; 
        }
        
        /* Sidebar */
        .sidebar { 
            background-color: #007bff; 
            height: 100vh; 
            position: fixed; 
            width: 250px; 
            color: white; 
            padding-top: 20px; 
            top: 0;
            left: 0;
            overflow-y: auto;
        }
        
        .sidebar .nav-link {
            color: white !important;
            padding: 12px 20px;
            border-radius: 5px;
            margin: 4px 10px;
            transition: all 0.3s ease;
            font-weight: normal; /* Chữ bình thường mặc định */
        }
        
        /* CHỈ hover mới in đậm và nổi bật */
        .sidebar .nav-link:hover {
            background-color: #0069d9;    /* Nền xanh đậm hơn một chút */
            font-weight: bold;            /* Chữ in đậm */
            transform: translateX(8px);   /* Dịch nhẹ sang phải cho đẹp */
        }
        
        /* Bỏ hoàn toàn style active - tất cả đều giống nhau */
        .sidebar .nav-link.active {
            background-color: transparent;
            font-weight: normal;
            transform: none;
        }
        
        .main-content { 
            margin-left: 250px; 
            padding: 20px; 
        }
        
        .stat-card { 
            border: none; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
            border-radius: 12px; 
            text-align: center; 
            padding: 25px; 
            transition: 0.3s; 
        }
        
        .stat-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 20px rgba(0,0,0,0.15); 
        }
        
        .stat-icon { 
            font-size: 3.5rem; 
            margin-bottom: 15px; 
        }
        
        @media (max-width: 768px) { 
            .sidebar { 
                width: 100%; 
                height: auto; 
                position: relative; 
            } 
            .main-content { 
                margin-left: 0; 
            } 
        }
    </style>
</head>
<body>
 <nav class="sidebar">
    <div class="text-center mb-4">
        <h4><i class="fas fa-warehouse"></i> Quản Lý Kho</h4>
        <div style="font-size: 0.85rem; margin-top: 10px; padding: 10px; background-color: rgba(255,255,255,0.1); border-radius: 5px;">
            <div><strong><?= htmlspecialchars($user['fullname'] ?? $user['username']) ?></strong></div>
            <div style="font-size: 0.75rem; margin-top: 5px;">
                <i class="fas fa-user-circle"></i> <?= $role ?>
            </div>
        </div>
    </div>

    <ul class="nav flex-column">

        <!-- Trang chủ -->
        <li class="nav-item">
            <a class="nav-link" href="trangchu.php"><i class="fas fa-home"></i> Trang Chủ</a>
        </li>

        <!-- SẢN PHẨM -->
        <?php if (!empty($permission['sanpham']) || isset($permission['all'])): ?>
        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0)" id="btnSanPham">
                <i class="fas fa-box"></i> Quản lý sản phẩm
            </a>
            <ul class="nav flex-column ms-3 d-none" id="submenuSanPham">
                <li><a class="nav-link" href="Sanpham.php">Sản phẩm</a></li>
                <li><a class="nav-link" href="dmsp.php">Danh mục</a></li>
                <li><a class="nav-link" href="Nhacungcap.php">Nhà cung cấp</a></li>
            </ul>
        </li>
        <?php endif; ?>

        <!-- PHIẾU NHẬP -->
        <?php if (!empty($permission['phieunhap']) || isset($permission['all'])): ?>
        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0)" id="btnPhieuNhap">
                <i class="fas fa-file-import"></i> Phiếu nhập
            </a>
            <ul class="nav flex-column ms-3 d-none" id="submenuPhieuNhap">
                <li><a class="nav-link" href="danh_sach_phieu_nhap.php">Danh sách</a></li>
                <li><a class="nav-link" href="phieu_nhap.php">Tạo phiếu</a></li>
            </ul>
        </li>
        <?php endif; ?>
   <!-- PHIẾU xuất -->
        <?php if (!empty($permission['phieuxuat']) || isset($permission['all'])): ?>
        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0)" id="btnPhieuXuat">
                <i class="fas fa-file-import"></i> Phiếu xuất
            </a>
            <ul class="nav flex-column ms-3 d-none" id="submenuPhieuXuat">
                <li><a class="nav-link" href="danh_sach_phieu_xuat.php">Danh sách</a></li>
                <li><a class="nav-link" href="phieu_xuat.php">Tạo phiếu</a></li>
            </ul>
        </li>
        <?php endif; ?>
        <!-- PHIẾU điều chuyển -->
        <?php if (!empty($permission['phieudc']) || isset($permission['all'])): ?>
        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0)" id="btnPhieudc">
                <i class="fas fa-file-export"></i> Phiếu điều chuyển
            </a>
            <ul class="nav flex-column ms-3 d-none" id="submenuPhieudc">
                <li><a class="nav-link" href="danh_sach_phieu_dieuchuyen.php">Danh sách</a></li>
                <li><a class="nav-link" href="phieu_dieuchuyen.php">Tạo phiếu</a></li>
            </ul>
        </li>
        <?php endif; ?>
     

        <!-- SẢN XUẤT -->
        <?php if (!empty($permission['sanxuat']) || isset($permission['all'])): ?>
        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0)" id="btnSanXuat">
                <i class="fas fa-cogs"></i> Sản xuất
            </a>
            <ul class="nav flex-column ms-3 d-none" id="submenuSanXuat">
                <li><a class="nav-link" href="danh_sach_lenh_san_xuat.php">Danh sách</a></li>
                <li><a class="nav-link" href="lenh_san_xuat.php">Tạo lệnh</a></li>
            </ul>
        </li>
        <?php endif; ?>

        <!-- BÁO CÁO -->
        <?php if (!empty($permission['baocao']) || isset($permission['all'])): ?>
        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0)" id="btnBaoCao">
                <i class="fas fa-chart-bar"></i> Báo cáo
            </a>
            <ul class="nav flex-column ms-3 d-none" id="submenuBaoCao">
                <li><a class="nav-link" href="tonkho.php">Tồn kho</a></li>
            </ul>
        </li>
        <?php endif; ?>

        <!-- KHÁCH HÀNG -->
        <?php if (!empty($permission['khachhang']) || isset($permission['all'])): ?>
        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0)" id="btnKhachHang">
                <i class="fas fa-users"></i> Khách hàng
            </a>
            <ul class="nav flex-column ms-3 d-none" id="submenuKhachHang">
                <li><a class="nav-link" href="khachhang.php">Khách hàng</a></li>
                <li><a class="nav-link" href="loaikhachhang.php">Loại KH</a></li>
            </ul>
        </li>
        <?php endif; ?>

        <!-- LOGOUT -->
        <li class="nav-item mt-4 pt-3 border-top">
            <a class="nav-link text-danger" href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </a>
        </li>

    </ul>
</nav>


 <main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h2>Trang Chủ</h2>
        <div class="user-info">
       
        </div>
    </div>

    <div class="text-center mt-5 pt-5">
        <h1 class="display-3 fw-bold text-primary mb-4">
            <i class="fas fa-warehouse me-3"></i>
            Chào mừng <?= htmlspecialchars($user['fullname'] ?? $user['username']) ?>!
        </h1>
        <p class="lead text-muted fs-4">
            <?php 
            $messages = [
                'admin'  => 'Bạn có quyền quản trị viên - Toàn bộ tính năng đã sẵn sàng',
                'staff'  => 'Bạn có quyền nhân viên - Có thể tạo và quản lý phiếu',
                'sanxuat'  => 'Bạn có quyền nhân viên kho'
            ];
            echo $messages[$role] ?? 'Chào mừng đến với hệ thống';
            ?>
        </p>
        <hr class="w-50 mx-auto my-5 border-primary opacity-50">
        <p class="text-secondary fs-5">
            Hãy chọn chức năng từ menu bên trái để bắt đầu.
        </p>
    </div>
</main>

    
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    function toggleMenu(btnId, subId) {
        const btn = document.getElementById(btnId);
        const sub = document.getElementById(subId);

        if (btn && sub) {
            btn.addEventListener("click", function () {
                sub.classList.toggle("d-none");
            });
        }
    }

    // GỌI CHO TẤT CẢ MENU
    toggleMenu("btnSanPham", "submenuSanPham");
    toggleMenu("btnPhieuNhap", "submenuPhieuNhap");
    toggleMenu("btnPhieuXuat", "submenuPhieuXuat");
     toggleMenu("btnPhieudc", "submenuPhieudc");
    toggleMenu("btnSanXuat", "submenuSanXuat");
    toggleMenu("btnBaoCao", "submenuBaoCao");
    toggleMenu("btnKhachHang", "submenuKhachHang");

});
</script>
</body>
</html>