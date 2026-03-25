<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: dangnhap.php');
    exit;
}
require_once __DIR__ . '/db.php';

// =========================
// Xử lý xóa lệnh sản xuất
// =========================
if (isset($_GET['xoa']) && !empty($_GET['xoa'])) {
    $malenh = trim($_GET['xoa']);
    try {
        $pdo->beginTransaction();
        
        // Xóa chi tiết xuất NVL (nếu có)
        $pdo->prepare("DELETE FROM Chitiet_XuatNVL_Sanxuat WHERE Malenh = ?")->execute([$malenh]);
        // Xóa chi tiết nhập sản phẩm (nếu có)
        $pdo->prepare("DELETE FROM Chitiet_Nhapsanpham_Sanxuat WHERE Malenh = ?")->execute([$malenh]);
        // Xóa lệnh sản xuất
        $pdo->prepare("DELETE FROM Lenhsanxuat WHERE Malenh = ?")->execute([$malenh]);
        
        $pdo->commit();
        header("Location: danh_sach_lenh_san_xuat.php?success=xoa");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: danh_sach_lenh_san_xuat.php?error=" . urlencode($e->getMessage()));
        exit;
    }
}

// Bộ lọc tìm kiếm
$maSearch   = trim($_GET['ma'] ?? '');
$spSearch   = trim($_GET['masp'] ?? '');
$dateFrom  = trim($_GET['from'] ?? '');
$dateTo    = trim($_GET['to'] ?? '');

// Lấy danh sách sản phẩm cho dropdown lọc
$sanphams = $pdo->query("SELECT Masp, Tensp FROM Sanpham ORDER BY Tensp")->fetchAll();

// Xây dựng SQL với điều kiện lọc
$sql = "
SELECT l.*, sp.Tensp
FROM Lenhsanxuat l
LEFT JOIN Sanpham sp ON l.Masp = sp.Masp
WHERE 1=1
";

$params = [];

// Tìm theo mã lệnh
if ($maSearch !== '') {
    $sql .= " AND l.Malenh LIKE :ma";
    $params[':ma'] = '%' . $maSearch . '%';
}
// Tìm theo mã sản phẩm
if ($spSearch !== '') {
    $sql .= " AND l.Masp = :masp";
    $params[':masp'] = $spSearch;
}

// Từ ngày sản xuất
if ($dateFrom !== '') {
    $sql .= " AND l.Ngaysanxuat >= :from";
    $params[':from'] = $dateFrom;
}

// Đến ngày sản xuất
if ($dateTo !== '') {
    $sql .= " AND l.Ngaysanxuat <= :to";
    $params[':to'] = $dateTo;
}

$sql .= " ORDER BY l.Ngaysanxuat DESC, l.Malenh DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$lenhs = $stmt->fetchAll();

$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Danh sách lệnh sản xuất</title>
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
  <div class="max-w-7xl mx-auto p-6 space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Danh sách lệnh sản xuất</h1>
        <p class="text-slate-500 text-sm mt-1">Quản lý và theo dõi tiến độ các lệnh sản xuất</p>
      </div>
      <div class="flex gap-2 text-sm">
        <a href="lenh_san_xuat.php" class="px-4 py-2.5 rounded bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-md"><i class="fas fa-plus mr-2"></i> Tạo lệnh mới</a>
      </div>
    </div>

    <?php if ($success): ?>
    <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
      Xóa lệnh sản xuất thành công.
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded">
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="GET" class="bg-white rounded-lg p-5 shadow-sm border border-slate-200 space-y-4">
      <div class="grid md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Mã lệnh</label>
          <input name="ma" class="w-full px-3 py-2 rounded bg-white border border-slate-300 text-slate-800" value="<?= htmlspecialchars($maSearch) ?>" placeholder="Tìm mã..." />
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Sản phẩm</label>
          <select name="masp" class="w-full px-3 py-2 rounded bg-white border border-slate-300 text-slate-800">
            <option value="">-- Tất cả --</option>
            <?php foreach ($sanphams as $sp): ?>
              <option value="<?= htmlspecialchars($sp['Masp']) ?>" <?= ($spSearch === $sp['Masp']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($sp['Tensp']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Từ ngày</label>
          <input type="date" name="from" class="w-full px-3 py-2 rounded bg-white border border-slate-300 text-slate-800" value="<?= htmlspecialchars($dateFrom) ?>" />
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Đến ngày</label>
          <input type="date" name="to" class="w-full px-3 py-2 rounded bg-white border border-slate-300 text-slate-800" value="<?= htmlspecialchars($dateTo) ?>" />
        </div>
      </div>
      <div class="pt-2">
        <button type="submit" class="px-6 py-2 rounded bg-slate-800 hover:bg-slate-900 text-white font-semibold shadow transition">
          <i class="fas fa-search mr-2"></i> Lọc dữ liệu
        </button>
        <a href="danh_sach_lenh_san_xuat.php" class="ml-2 px-6 py-2 rounded bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold transition">Xóa bộ lọc</a>
      </div>
    </form>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-100 text-slate-700">
          <tr>
            <th class="px-4 py-3 text-left font-semibold">Mã lệnh</th>
            <th class="px-4 py-3 text-left font-semibold">Sản phẩm</th>
            <th class="px-4 py-3 text-left font-semibold">Ngày sản xuất</th>
            <th class="px-4 py-3 text-right font-semibold">Số lượng</th>
            <th class="px-4 py-3 text-left font-semibold">Trạng thái</th>
            <th class="px-4 py-3 text-left font-semibold">Ghi chú</th>
            <th class="px-4 py-3 text-center font-semibold">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($lenhs)): ?>
            <tr><td colspan="7" class="px-4 py-6 text-center text-slate-500">Chưa có lệnh sản xuất nào.</td></tr>
          <?php else: ?>
            <?php foreach ($lenhs as $l): ?>
              <tr class="border-t border-slate-200 hover:bg-slate-50 transition">
                <td class="px-4 py-3 font-semibold text-blue-600"><?= htmlspecialchars($l['Malenh']) ?></td>
                <td class="px-4 py-3 text-slate-800"><?= htmlspecialchars($l['Tensp'] ?? 'N/A') ?></td>
                <td class="px-4 py-3 text-slate-600"><?= date('d/m/Y', strtotime($l['Ngaysanxuat'])) ?></td>
                <td class="px-4 py-3 text-right font-medium text-slate-800"><?= number_format($l['Soluongsanxuat']) ?></td>
                <td class="px-4 py-3">
                    <?php if($l['Trangthai'] == 'Hoàn thành'): ?>
                        <span class="px-2 py-1 rounded bg-green-100 text-green-700 text-xs font-bold border border-green-200">Hoàn thành</span>
                    <?php else: ?>
                        <span class="px-2 py-1 rounded bg-orange-100 text-orange-700 text-xs font-bold border border-orange-200">Đang sản xuất</span>
                    <?php endif; ?>
                </td>
                <td class="px-4 py-3 text-slate-500 italic"><?= htmlspecialchars(mb_substr($l['Ghichu'] ?? '', 0, 40)) ?><?= mb_strlen($l['Ghichu'] ?? '') > 40 ? '...' : '' ?></td>
                <td class="px-4 py-3">
                  <div class="flex items-center justify-center gap-2">
                    <a title="Hoàn thành lệnh" href="hoan_thanh_san_xuat.php?id=<?= urlencode($l['Malenh']) ?>" class="w-8 h-8 flex items-center justify-center rounded bg-green-500 hover:bg-green-600 text-white shadow"><i class="fas fa-check"></i></a>
                    <a title="Sửa lệnh" href="sua_lenh_san_xuat.php?id=<?= urlencode($l['Malenh']) ?>" class="w-8 h-8 flex items-center justify-center rounded bg-blue-500 hover:bg-blue-600 text-white shadow"><i class="fas fa-edit"></i></a>
                    <a title="Xóa lệnh" href="danh_sach_lenh_san_xuat.php?xoa=<?= urlencode($l['Malenh']) ?>" onclick="return confirm('Bạn có chắc muốn xóa lệnh sản xuất này? Mọi dữ liệu liên quan sẽ bị mất.')" class="w-8 h-8 flex items-center justify-center rounded bg-red-500 hover:bg-red-600 text-white shadow"><i class="fas fa-trash"></i></a>
                  </div>
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
    const menus = ["SanPham", "PhieuNhap", "PhieuXuat", "SanXuat", "BaoCao", "KhachHang"];
    menus.forEach(menu => {
        document.getElementById(`btn${menu}`)?.addEventListener("click", function () {
            document.getElementById(`submenu${menu}`).classList.toggle("d-none");
        });
    });
    // Tự động mở menu Sản xuất
    document.getElementById("submenuSanXuat")?.classList.remove("d-none");
</script>
</body>
</html>