<?php
/**
 * EXAMPLE: Dashboard Page with Role-Based Content
 * Shows different content based on user role
 */

session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/role_helper.php';

// Require login first
requireLogin();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - VLXD</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="min-h-screen">
    <!-- Navbar -->
    <nav class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">🏭 VLXD Dashboard</h1>
            <div>
                <span class="mr-4">👤 <?= htmlspecialchars(getCurrentUser()['fullname']) ?></span>
                <span class="mr-4"><?= getRoleBadge(getCurrentRole()) ?></span>
                <a href="logout.php" class="text-red-200 hover:text-red-100">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto p-4">
        
        <!-- Welcome Message -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-3xl font-bold mb-2">Chào mừng, <?= htmlspecialchars(getCurrentUser()['fullname']) ?></h2>
            <p class="text-gray-600">Role: <strong><?= getRoleName(getCurrentRole()) ?></strong></p>
            <p class="text-sm text-gray-500">Đăng nhập lúc: <?= date('d/m/Y H:i') ?></p>
        </div>

        <!-- Admin Panel (Admin only) -->
        <?php if (isAdmin()): ?>
        <div class="bg-red-50 border-2 border-red-200 rounded-lg p-6 mb-6">
            <h3 class="text-xl font-bold text-red-800 mb-4">🔐 Admin Panel</h3>
            <p class="text-red-700 mb-4">Bạn có quyền truy cập các chức năng Admin:</p>
            <ul class="list-disc list-inside space-y-2 text-red-700">
                <li>✅ Quản lý người dùng (CRUD)</li>
                <li>✅ Xóa sản phẩm/kho</li>
                <li>✅ Xem system logs</li>
                <li>✅ Cài đặt hệ thống</li>
            </ul>
            <div class="mt-4 space-x-2">
                <a href="trangchu.php" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Users Management</a>
                <a href="sanpham.php" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Products</a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Staff Panel (Staff and Admin) -->
        <?php if (hasAnyRole([ROLE_ADMIN, ROLE_STAFF])): ?>
        <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-6 mb-6">
            <h3 class="text-xl font-bold text-blue-800 mb-4">📊 Staff Panel</h3>
            <p class="text-blue-700 mb-4">Bạn có quyền truy cập:</p>
            <ul class="list-disc list-inside space-y-2 text-blue-700">
                <li>✅ Xem/Thêm sản phẩm</li>
                <li>✅ Lập phiếu nhập/xuất</li>
                <li>✅ Xem báo cáo kho</li>
                <li><?= isAdmin() ? '✅ Quản lý nhân viên' : '❌ Không quản lý được nhân viên' ?></li>
            </ul>
            <div class="mt-4 space-x-2">
                <a href="danh_sach_phieu_nhap.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Phiếu Nhập</a>
                <a href="danh_sach_phieu_xuat.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Phiếu Xuất</a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Everyone can see -->
        <div class="bg-green-50 border-2 border-green-200 rounded-lg p-6">
            <h3 class="text-xl font-bold text-green-800 mb-4">📦 Tất Cả Người Dùng</h3>
            <p class="text-green-700 mb-4">Bạn có thể truy cập:</p>
            <ul class="list-disc list-inside space-y-2 text-green-700">
                <li>✅ Xem danh sách sản phẩm</li>
                <li>✅ Xem giá bán</li>
                <li>✅ Xem danh sách kho</li>
            </ul>
            <div class="mt-4">
                <a href="sanpham.php" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Xem Sản Phẩm</a>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mt-8 bg-yellow-50 border border-yellow-300 rounded p-4">
            <p class="text-sm text-gray-700">
                <strong>💡 Ghi Chú:</strong> Các mục bên trên sẽ ẩn/hiện tùy thuộc vào role của bạn.
                Thử đăng nhập với các account khác nhau để xem sự khác biệt!
            </p>
        </div>

    </div>
</div>

</body>
</html>
