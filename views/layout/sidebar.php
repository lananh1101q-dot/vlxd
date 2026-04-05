<?php
$currentPage = $page ?? '';
?>
<nav class="sidebar">
    <div class="text-center mb-4 px-3">
        <h4 class="mb-0"><i class="fas fa-warehouse text-warning me-2"></i> KHO VLXD</h4>
        <div class="mt-2 py-2 px-3 bg-white bg-opacity-10 rounded shadow-sm">
            <!-- Thông tin người dùng sẽ được điền bằng JavaScript -->
            <div id="user-fullname" class="fw-bold small text-truncate">Đang tải...</div>
            <div id="user-role" class="opacity-50" style="font-size: 0.7rem;"><i class="fas fa-user-shield me-1"></i> Guest</div>
        </div>
    </div>
    
    <ul class="nav flex-column pb-4">
        <li class="nav-item">
            <a class="nav-link <?php echo $currentPage === 'trangchu' ? 'active' : ''; ?>" href="trangchu">
                <i class="fas fa-home me-2"></i>Trang Chủ
            </a>
        </li>
        
        <li class="nav-item mt-3 px-3 small text-uppercase opacity-50 fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Danh mục & Hàng hóa</li>
        <li class="nav-item">
            <a class="nav-link <?php echo $currentPage === 'sanpham' ? 'active' : ''; ?>" href="sanpham">
                <i class="fas fa-cube me-2"></i>Sản phẩm
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $currentPage === 'dmsp' ? 'active' : ''; ?>" href="dmsp">
                <i class="fas fa-tags me-2"></i>Danh mục SP
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $currentPage === 'nguyenvatlieu' ? 'active' : ''; ?>" href="nguyenvatlieu">
                <i class="fas fa-layer-group me-2"></i>Nguyên vật liệu
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $currentPage === 'nhacungcap' ? 'active' : ''; ?>" href="nhacungcap">
                <i class="fas fa-truck me-2"></i>Nhà cung cấp
            </a>
        </li>
        
        <!-- Phần Kho bãi (Chỉ Admin/Staff mới thấy) -->
        <div id="menu-warehouse" style="display: none;">
            <li class="nav-item mt-3 px-3 small text-uppercase opacity-50 fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Kho bãi</li>
            <li class="nav-item">
                <a class="nav-link" href="danh_sach_phieu_nhap.php">
                    <i class="fas fa-file-import me-2"></i>Phiếu nhập kho
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="danh_sach_phieu_xuat.php">
                    <i class="fas fa-file-export me-2"></i>Phiếu xuất kho
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="tonkho.php">
                    <i class="fas fa-chart-pie me-2"></i>Báo cáo tồn kho
                </a>
            </li>
        </div>

        <li class="nav-item mt-3 px-3 small text-uppercase opacity-50 fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Sản xuất & Đối tác</li>
        <li class="nav-item">
            <a class="nav-link <?php echo $currentPage === 'congthuc' ? 'active' : ''; ?>" href="congthuc">
                <i class="fas fa-flask me-2"></i>Công thức SP
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $currentPage === 'khachhang' ? 'active' : ''; ?>" href="khachhang">
                <i class="fas fa-users me-2"></i>Khách hàng
            </a>
        </li>

        <li class="nav-item mt-auto"><hr class="mx-3 bg-white opacity-10"></li>
        <li class="nav-item px-2">
            <a class="nav-link text-danger fw-bold" href="logout.php" id="btn-logout">
                <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
            </a>
        </li>
    </ul>
</nav>
<div class="main-content">
