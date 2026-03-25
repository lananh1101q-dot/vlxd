<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: dangnhap.php');
    exit;
}
require_once __DIR__ . '/db.php';

$errors = [];
$success = '';

// Lấy dữ liệu dropdown
$sanphams = $pdo->query("SELECT Masp, Tensp FROM Sanpham ORDER BY Tensp")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $malenh = trim($_POST['malenh'] ?? '');
    $masp = trim($_POST['masp'] ?? '');
    $ngaysanxuat = $_POST['ngaysanxuat'] ?? '';
    $soluongsanxuat = (int)($_POST['soluongsanxuat'] ?? 0);
    $ghichu = trim($_POST['ghichu'] ?? '');

    if ($malenh === '' || $masp === '' || $ngaysanxuat === '' || $soluongsanxuat <= 0) {
        $errors[] = 'Vui lòng nhập đầy đủ Mã lệnh, Sản phẩm, Ngày sản xuất, Số lượng.';
    }

    if (!$errors) {
        try {
            $pdo->beginTransaction();

            // Lưu lệnh sản xuất
            $stmtLenh = $pdo->prepare("
                INSERT INTO Lenhsanxuat (Malenh, Masp, Ngaysanxuat, Soluongsanxuat, Ghichu)
                VALUES (:malenh, :masp, :ngay, :sl, :ghichu)
            ");
            $stmtLenh->execute([
                ':malenh' => $malenh,
                ':masp' => $masp,
                ':ngay' => $ngaysanxuat,
                ':sl' => $soluongsanxuat,
                ':ghichu' => $ghichu,
            ]);

            $pdo->commit();
            $success = 'Tạo lệnh sản xuất thành công.';
            
            // Xóa dữ liệu post sau khi thành công để trống form
            unset($_POST);
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Lỗi khi lưu lệnh: (Có thể mã lệnh đã tồn tại) ' . htmlspecialchars($e->getMessage());
        }
    }
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Tạo Lệnh Sản Xuất</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
        body { 
            background-color: #f8f9fa; 
            font-family: 'Segoe UI', sans-serif; 
            color: #333; 
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
            z-index: 1000;
        }
        
        .sidebar .nav-link {
            color: white !important;
            padding: 12px 20px;
            border-radius: 5px;
            margin: 4px 10px;
            transition: all 0.3s ease;
            font-weight: normal;
        }
        
        .sidebar .nav-link:hover {
            background-color: #0069d9;
            font-weight: bold;
            transform: translateX(8px);
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
        .d-none {
            display: none !important;
        }
    </style>
</head>
<body>
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
            <a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
        </li>
    </ul>
</nav>
<div class="main-content">
  <div class="max-w-5xl mx-auto p-6 space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Tạo lệnh sản xuất</h1>
        <p class="text-slate-500 text-sm mt-1">Lập kế hoạch và thêm lệnh sản xuất mới vào hệ thống</p>
      </div>
      <div class="flex gap-2 text-sm">
        <a href="danh_sach_lenh_san_xuat.php" class="px-4 py-2 rounded bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold shadow-sm transition-colors">Quay lại danh sách</a>
      </div>
    </div>

    <?php if ($errors): ?>
    <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded shadow-sm">
      <ul class="list-disc list-inside space-y-1">
        <?php foreach ($errors as $er): ?>
          <li><?= htmlspecialchars($er) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>

    <?php if ($success): ?>
    <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded shadow-sm">
      <i class="fas fa-check-circle mr-2"></i> <?= htmlspecialchars($success) ?>
    </div>
    <?php endif; ?>

    <form method="post" class="bg-white rounded-lg p-6 space-y-5 shadow-sm border border-slate-200">
      <div class="grid md:grid-cols-2 gap-5">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Mã lệnh sản xuất <span class="text-red-500">*</span></label>
          <input name="malenh" required 
                 class="w-full px-4 py-2.5 rounded bg-white border border-slate-300 text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" 
                 value="<?= htmlspecialchars($_POST['malenh'] ?? '') ?>" 
                 placeholder="VD: LSX001" />
        </div>
        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Sản phẩm <span class="text-red-500">*</span></label>
          <select name="masp" required class="w-full px-4 py-2.5 rounded bg-white border border-slate-300 text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="">-- Chọn sản phẩm --</option>
            <?php foreach ($sanphams as $sp): ?>
              <option value="<?= htmlspecialchars($sp['Masp']) ?>"
                <?= (($_POST['masp'] ?? '') === $sp['Masp']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($sp['Tensp']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Ngày sản xuất <span class="text-red-500">*</span></label>
          <input type="date" name="ngaysanxuat" required
            class="w-full px-4 py-2.5 rounded bg-white border border-slate-300 text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
             value="<?= htmlspecialchars($_POST['ngaysanxuat'] ?? date('Y-m-d')) ?>" />
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Số lượng sản xuất <span class="text-red-500">*</span></label>
          <input type="number" name="soluongsanxuat" required min="1"
            class="w-full px-4 py-2.5 rounded bg-white border border-slate-300 text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
             value="<?= htmlspecialchars($_POST['soluongsanxuat'] ?? '') ?>" placeholder="Nhập số lượng..." />
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">Ghi chú thêm</label>
        <textarea name="ghichu" rows="3" 
                  class="w-full px-4 py-2.5 rounded bg-white border border-slate-300 text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" 
                  placeholder="Nhập mô tả hoặc ghi chú..."><?= htmlspecialchars($_POST['ghichu'] ?? '') ?></textarea>
      </div>

      <div class="pt-4 border-t border-slate-100">
        <button type="submit" class="px-8 py-2.5 rounded bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-md transition-colors">
          <i class="fas fa-save mr-2"></i> Lưu Lệnh Sản Xuất
        </button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const menuConfig = {
        "btnSanPham": "submenuSanPham",
        "btnPhieuNhap": "submenuPhieuNhap",
        "btnPhieuXuat": "submenuPhieuXuat",
        "btnSanXuat": "submenuSanXuat",
        "btnBaoCao": "submenuBaoCao",
        "btnKhachHang": "submenuKhachHang"
    };

    Object.keys(menuConfig).forEach(btnId => {
        const btn = document.getElementById(btnId);
        const sub = document.getElementById(menuConfig[btnId]);
        if(btn && sub) {
            btn.addEventListener("click", function() {
                sub.classList.toggle("d-none");
            });
        }
    });

    const path = window.location.pathname;
    if (path.includes("lenh_san_xuat.php") || path.includes("danh_sach_lenh_san_xuat.php")) {
        const submenuSanXuat = document.getElementById("submenuSanXuat");
        if(submenuSanXuat) {
            submenuSanXuat.classList.remove("d-none");
        }
    }
});
</script>
</body>
</html>