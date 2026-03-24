<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: dangnhap.php');
    exit;
}
require_once __DIR__ . '/db.php';

// Xử lý xóa phiếu
if (isset($_GET['delete']) && $_GET['delete'] === 'yes' && isset($_GET['id'])) {
    $madieuchuyen = $_GET['id'];
    try {
        $pdo->beginTransaction();
        $pdo->prepare("DELETE FROM Chitiet_Phieudieuchuyen WHERE Madieuchuyen = ?")->execute([$madieuchuyen]);
        $pdo->prepare("DELETE FROM Phieudieuchuyen WHERE Madieuchuyen = ?")->execute([$madieuchuyen]);
        $pdo->commit();
        $success = 'xoa';
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = 'Lỗi khi xóa: ' . $e->getMessage();
    }
}

// Lấy danh sách phiếu điều chuyển
$query = "
    SELECT p.*, kx.Tenkho AS TenKhoXuat, kn.Tenkho AS TenKhoNhap
    FROM Phieudieuchuyen p
    LEFT JOIN Kho kx ON p.Khoxuat = kx.Makho
    LEFT JOIN Kho kn ON p.Khonhap = kn.Makho
    ORDER BY p.Ngaydieuchuyen DESC
";
$phieus = $pdo->query($query)->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách phiếu điều chuyển</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100%;
            width: 250px;
            background-color: #1f2937;
            color: white;
            padding-top: 20px;
            overflow-y: auto;
            z-index: 1000;
        }
        .sidebar .nav-link {
            color: #d1d5db;
            padding: 10px 20px;
            text-decoration: none;
            display: block;
            border-radius: 5px;
            margin: 5px 10px;
            transition: background-color 0.3s;
        }
        .sidebar .nav-link:hover {
            background-color: #374151;
        }
        .sidebar .nav-link.active {
            background-color: #3b82f6;
            color: white;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
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
<body class="bg-gray-100">
    <nav class="sidebar">
        <div class="text-center mb-4">
            <h4><i class="fas fa-warehouse"></i> Quản Lý Kho</h4>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="trangchu.php"><i class="fas fa-home"></i> Trang Chủ</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0)" id="btnSanPham">
                    <i class="fas fa-box"></i> Quản lý sản phẩm
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <ul class="nav flex-column ms-3 d-none" id="submenuSanPham">
                    <li class="nav-item"><a class="nav-link" href="Sanpham.php"><i class="fas fa-cube"></i> Sản phẩm</a></li>
                    <li class="nav-item"><a class="nav-link" href="dmsp.php"><i class="fas fa-tags"></i> Danh mục sản phẩm</a></li>
                    <li class="nav-item"><a class="nav-link" href="Nhacungcap.php"><i class="fas fa-truck"></i> Nhà cung cấp</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0)" id="btnPhieuNhap">
                    <i class="fas fa-file-import"></i> Phiếu nhập kho
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <ul class="nav flex-column ms-3 d-none" id="submenuPhieuNhap">
                    <li class="nav-item"><a class="nav-link" href="danh_sach_phieu_nhap.php"><i class="fas fa-list"></i> Danh sách phiếu nhập</a></li>
                    <li class="nav-item"><a class="nav-link" href="phieu_nhap.php"><i class="fas fa-plus-circle"></i> Tạo phiếu nhập</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0)" id="btnPhieuXuat">
                    <i class="fas fa-file-export"></i> Phiếu xuất
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <ul class="nav flex-column ms-3 d-none" id="submenuPhieuXuat">
                    <li class="nav-item"><a class="nav-link" href="danh_sach_phieu_xuat.php"><i class="fas fa-list"></i> Danh sách phiếu xuất</a></li>
                    <li class="nav-item"><a class="nav-link" href="phieu_xuat.php"><i class="fas fa-plus-circle"></i> Tạo phiếu xuất</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0)" id="btnDieuChuyen">
                    <i class="fas fa-exchange-alt"></i> Điều chuyển
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <ul class="nav flex-column ms-3 d-none" id="submenuDieuChuyen">
                    <li class="nav-item"><a class="nav-link" href="danh_sach_phieu_dieuchuyen.php"><i class="fas fa-list"></i> Danh sách phiếu điều chuyển</a></li>
                    <li class="nav-item"><a class="nav-link" href="phieu_dieuchuyen.php"><i class="fas fa-plus-circle"></i> Tạo phiếu điều chuyển</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0)" id="btnBaoCao">
                    <i class="fas fa-chart-bar"></i> Báo cáo & Thống kê
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <ul class="nav flex-column ms-3 d-none" id="submenuBaoCao">
                    <li class="nav-item"><a class="nav-link" href="tonkho.php"><i class="fas fa-warehouse"></i> Báo cáo tồn kho</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0)" id="btnKhachHang">
                    <i class="fas fa-users"></i> Quản lý khách hàng
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <ul class="nav flex-column ms-3 d-none" id="submenuKhachHang">
                    <li class="nav-item"><a class="nav-link" href="khachhang.php"><i class="fas fa-user"></i> Khách hàng</a></li>
                    <li class="nav-item"><a class="nav-link" href="loaikhachhang.php"><i class="fas fa-users-cog"></i> Loại khách hàng</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0)" id="btnSanXuat">
                    <i class="fas fa-cogs"></i> Sản xuất
                    <i class="fas fa-chevron-down float-end"></i>
                </a>
                <ul class="nav flex-column ms-3 d-none" id="submenuSanXuat">
                    <li class="nav-item"><a class="nav-link" href="danh_sach_lenh_san_xuat.php"><i class="fas fa-list"></i> Danh sách lệnh sản xuất</a></li>
                    <li class="nav-item"><a class="nav-link" href="lenh_san_xuat.php"><i class="fas fa-plus-circle"></i> Tạo lệnh sản xuất</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
            </li>
        </ul>
    </nav>

    <div class="main-content">
        <div class="max-w-7xl mx-auto p-6 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Danh sách phiếu điều chuyển</h1>
                    <p class="text-slate-400 text-sm mt-1">Quản lý các phiếu điều chuyển kho</p>
                </div>
                <div class="flex gap-2 text-sm">
                    <a href="phieu_dieuchuyen.php" class="px-4 py-2 rounded bg-sky-600 hover:bg-sky-700 font-semibold">+ Tạo phiếu điều chuyển</a>
                    <a href="dashboard.php" class="px-3 py-2 rounded bg-slate-800 hover:bg-slate-700">← Dashboard</a>
                    <a href="logout.php" class="px-3 py-2 rounded bg-red-600 hover:bg-red-700">Đăng xuất</a>
                </div>
            </div>

            <?php if (isset($success) && $success === 'xoa'): ?>
                <div class="bg-emerald-900/60 border border-emerald-700 text-emerald-100 px-4 py-3 rounded">
                    Đã xóa phiếu điều chuyển thành công.
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="bg-red-900/60 border border-red-700 text-red-200 px-4 py-3 rounded">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="bg-slate-800 rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-slate-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-slate-300">Mã phiếu</th>
                            <th class="px-4 py-3 text-left text-slate-300">Kho xuất</th>
                            <th class="px-4 py-3 text-left text-slate-300">Kho nhập</th>
                            <th class="px-4 py-3 text-left text-slate-300">Ngày điều chuyển</th>
                            <th class="px-4 py-3 text-left text-slate-300">Ghi chú</th>
                            <th class="px-4 py-3 text-center text-slate-300">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-600">
                        <?php if (empty($phieus)): ?>
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                    Chưa có phiếu điều chuyển nào.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($phieus as $p): ?>
                                <tr class="hover:bg-slate-700/50">
                                    <td class="px-4 py-3 text-slate-200"><?= htmlspecialchars($p['Madieuchuyen']) ?></td>
                                    <td class="px-4 py-3 text-slate-200"><?= htmlspecialchars($p['TenKhoXuat']) ?></td>
                                    <td class="px-4 py-3 text-slate-200"><?= htmlspecialchars($p['TenKhoNhap']) ?></td>
                                    <td class="px-4 py-3 text-slate-200"><?= htmlspecialchars(date('d/m/Y', strtotime($p['Ngaydieuchuyen']))) ?></td>
                                    <td class="px-4 py-3 text-slate-200"><?= htmlspecialchars($p['Ghichu'] ?: '') ?></td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="chi_tiet_phieu_dieuchuyen.php?id=<?= urlencode($p['Madieuchuyen']) ?>" class="text-blue-400 hover:text-blue-300 mr-2">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </a>
                                        <a href="?delete=yes&id=<?= urlencode($p['Madieuchuyen']) ?>" onclick="return confirm('Bạn có chắc muốn xóa phiếu này?')" class="text-red-400 hover:text-red-300">
                                            <i class="fas fa-trash"></i> Xóa
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Toggle submenus
        document.getElementById("btnSanPham").addEventListener("click", function () {
            document.getElementById("submenuSanPham").classList.toggle("d-none");
        });
        document.getElementById("btnPhieuNhap").addEventListener("click", function () {
            document.getElementById("submenuPhieuNhap").classList.toggle("d-none");
        });
        document.getElementById("btnPhieuXuat").addEventListener("click", function () {
            document.getElementById("submenuPhieuXuat").classList.toggle("d-none");
        });
        document.getElementById("btnDieuChuyen").addEventListener("click", function () {
            document.getElementById("submenuDieuChuyen").classList.toggle("d-none");
        });
        document.getElementById("btnBaoCao").addEventListener("click", function () {
            document.getElementById("submenuBaoCao").classList.toggle("d-none");
        });
        document.getElementById("btnKhachHang").addEventListener("click", function () {
            document.getElementById("submenuKhachHang").classList.toggle("d-none");
        });
        document.getElementById("btnSanXuat").addEventListener("click", function () {
            document.getElementById("submenuSanXuat").classList.toggle("d-none");
        });
    </script>
</body>
</html>