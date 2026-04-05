<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once __DIR__ . '/role_helper.php';

/* 
// Authentication check (Removed because we use JWT in LocalStorage)
if (!isLoggedIn()) {
    header("Location: dangnhap.php");
    exit;
}
*/

// Get the requested page
$page = isset($_GET['page']) ? $_GET['page'] : 'trangchu';

// Mapping URL paths to view files
$views = [
    'trangchu'          => ['file' => 'trangchu.php',        'title' => 'Trang chủ - VLXD'],
    'sanpham'           => ['file' => 'sanpham.php',         'title' => 'Quản lý Sản phẩm - VLXD'],
    'dmsp'              => ['file' => 'dmsp.php',            'title' => 'Danh mục Sản phẩm - VLXD'],
    'nguyenvatlieu'     => ['file' => 'nguyenvatlieu.php',    'title' => 'Nguyên vật liệu - VLXD'],
    'nhacungcap'        => ['file' => 'nhacungcap.php',      'title' => 'Nhà cung cấp - VLXD'],
    'congthuc'          => ['file' => 'congthucsanpham.php', 'title' => 'Công thức Sản phẩm - VLXD'],
    'khachhang'         => ['file' => 'khachhang.php',       'title' => 'Khách hàng - VLXD'],
];

// Check if page exists
if (!isset($views[$page])) {
    $page = 'trangchu'; // Default fallback
}

$viewConfig = $views[$page];
$title = $viewConfig['title'];

// Header
require_once __DIR__ . '/views/layout/header.php';

// Sidebar (Includes opening of main-content)
require_once __DIR__ . '/views/layout/sidebar.php';

// View Page
$viewPath = __DIR__ . '/views/pages/' . $viewConfig['file'];
if (file_exists($viewPath)) {
    require_once $viewPath;
} else {
    echo "<div class='alert alert-warning'>Trang đang được phát triển...</div>";
}

// Footer (Includes closing of main-content)
require_once __DIR__ . '/views/layout/footer.php';
