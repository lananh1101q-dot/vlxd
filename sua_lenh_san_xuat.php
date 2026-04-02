<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: dangnhap.php');
    exit;
}
require_once __DIR__ . '/db.php';

$errors = [];
$success = '';

// Lấy Mã lệnh từ URL (ví dụ: sua_lenh_san_xuat.php?id=LSX01)
$malenh = trim($_GET['id'] ?? '');

if ($malenh === '') {
    header('Location: danh_sach_lenh_san_xuat.php');
    exit;
}

// Lấy thông tin hiện tại của lệnh sản xuất
$stmt = $pdo->prepare("SELECT * FROM Lenhsanxuat WHERE Malenh = ?");
$stmt->execute([$malenh]);
$lenh = $stmt->fetch();

if (!$lenh) {
    header('Location: danh_sach_lenh_san_xuat.php?error=Không tìm thấy lệnh sản xuất');
    exit;
}

// ==========================================
// THÊM MỚI: XỬ LÝ HỦY TRẠNG THÁI HOÀN THÀNH
// ==========================================
if (isset($_GET['action']) && $_GET['action'] === 'undo' && $lenh['Trangthai'] === 'Hoàn thành') {
    try {
        $pdo->beginTransaction();
        
        // 1. Tìm kho mà trước đó đã nhập thành phẩm
        $nhapSP = $pdo->prepare("SELECT Makho, Masp, Soluong FROM Chitiet_Nhapsanpham_Sanxuat WHERE Malenh = ?");
        $nhapSP->execute([$malenh]);
        $chiTietNhap = $nhapSP->fetch();
        
        if ($chiTietNhap) {
            $makho = $chiTietNhap['Makho'];
            
            // 2. Trừ đi số lượng thành phẩm đã lỡ nhập vào kho
            $pdo->prepare("UPDATE Tonkho_sp SET Soluongton = Soluongton - ? WHERE Makho = ? AND Masp = ?")
                ->execute([$chiTietNhap['Soluong'], $makho, $chiTietNhap['Masp']]);
                
            // 3. Cộng trả lại số lượng nguyên vật liệu vào kho
            $xuatNVL = $pdo->prepare("SELECT Manvl, Soluong FROM Chitiet_XuatNVL_Sanxuat WHERE Malenh = ?");
            $xuatNVL->execute([$malenh]);
            $chiTietXuat = $xuatNVL->fetchAll();
            
            foreach ($chiTietXuat as $nvl) {
                $pdo->prepare("UPDATE Tonkho_nvl SET Soluongton = Soluongton + ? WHERE Makho = ? AND Manvl = ?")
                    ->execute([$nvl['Soluong'], $makho, $nvl['Manvl']]);
            }
            
            // 4. Xóa lịch sử chi tiết nhập/xuất của lệnh này
            $pdo->prepare("DELETE FROM Chitiet_Nhapsanpham_Sanxuat WHERE Malenh = ?")->execute([$malenh]);
            $pdo->prepare("DELETE FROM Chitiet_XuatNVL_Sanxuat WHERE Malenh = ?")->execute([$malenh]);
        }
        
        // 5. Cập nhật lại trạng thái lệnh
        $pdo->prepare("UPDATE Lenhsanxuat SET Trangthai = 'Đang sản xuất' WHERE Malenh = ?")->execute([$malenh]);
        
        $pdo->commit();
        header("Location: sua_lenh_san_xuat.php?id=" . urlencode($malenh) . "&success=undo");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $errors[] = "Lỗi khi hủy hoàn thành: " . $e->getMessage();
    }
}

// Bắt thông báo thành công khi vừa undo xong
if (isset($_GET['success']) && $_GET['success'] === 'undo') {
    $success = 'Đã hủy hoàn thành! Trả lại nguyên vật liệu và thu hồi thành phẩm khỏi kho thành công.';
    $lenh['Trangthai'] = 'Đang sản xuất'; // Cập nhật lại biến để giao diện mở khóa Form
}

// Lấy danh sách sản phẩm cho dropdown
$sanphams = $pdo->query("SELECT Masp, Tensp FROM Sanpham ORDER BY Tensp")->fetchAll();

// Xử lý khi người dùng bấm nút Cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $masp = trim($_POST['masp'] ?? '');
    $ngaysanxuat = $_POST['ngaysanxuat'] ?? '';
    $soluongsanxuat = (int)($_POST['soluongsanxuat'] ?? 0);
    $ghichu = trim($_POST['ghichu'] ?? '');

    if ($masp === '' || $ngaysanxuat === '' || $soluongsanxuat <= 0) {
        $errors[] = 'Vui lòng nhập đầy đủ Sản phẩm, Ngày sản xuất và Số lượng.';
    }

    // Tùy chọn: Không cho phép sửa nếu lệnh đã hoàn thành
    if ($lenh['Trangthai'] === 'Hoàn thành') {
        $errors[] = 'Không thể sửa đổi lệnh sản xuất đã hoàn thành!';
    }

    if (!$errors) {
        try {
            $stmtUpdate = $pdo->prepare("
                UPDATE Lenhsanxuat 
                SET Masp = :masp, Ngaysanxuat = :ngay, Soluongsanxuat = :sl, Ghichu = :ghichu 
                WHERE Malenh = :malenh
            ");
            $stmtUpdate->execute([
                ':masp' => $masp,
                ':ngay' => $ngaysanxuat,
                ':sl' => $soluongsanxuat,
                ':ghichu' => $ghichu,
                ':malenh' => $malenh
            ]);

            $success = 'Cập nhật lệnh sản xuất thành công.';
            
            // Cập nhật lại mảng $lenh để hiển thị dữ liệu mới nhất lên form
            $lenh['Masp'] = $masp;
            $lenh['Ngaysanxuat'] = $ngaysanxuat;
            $lenh['Soluongsanxuat'] = $soluongsanxuat;
            $lenh['Ghichu'] = $ghichu;

        } catch (Exception $e) {
            $errors[] = 'Lỗi khi cập nhật: ' . htmlspecialchars($e->getMessage());
        }
    }
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Sửa lệnh sản xuất</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; color: #333; }
        .sidebar { background-color: #007bff; height: 100vh; position: fixed; width: 250px; color: white; padding-top: 20px; top: 0; left: 0; overflow-y: auto; z-index: 1000;}
        .sidebar .nav-link { color: white !important; padding: 12px 20px; border-radius: 5px; margin: 4px 10px; transition: all 0.3s ease; font-weight: normal; }
        .sidebar .nav-link:hover { background-color: #0069d9; font-weight: bold; transform: translateX(8px); }
        .main-content { margin-left: 250px; padding: 20px; }
        .d-none { display: none !important; }
        @media (max-width: 768px) { .sidebar { width: 100%; height: auto; position: relative; } .main-content { margin-left: 0; } }
  </style>
</head>
<body>
<nav class="sidebar">
    <div class="text-center mb-4">
        <h4><i class="fas fa-warehouse"></i> Quản Lý Kho</h4>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link" href="trangchu.php"><i class="fas fa-home"></i> Trang Chủ</a></li>
        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0)" id="btnSanPham"><i class="fas fa-box"></i> Quản lý sản phẩm <i class="fas fa-chevron-down float-end"></i></a>
            <ul class="nav flex-column ms-3 d-none" id="submenuSanPham">
                <li class="nav-item"><a class="nav-link" href="Sanpham.php"><i class="fas fa-cube"></i> Sản phẩm</a></li>
                <li class="nav-item"><a class="nav-link" href="dmsp.php"><i class="fas fa-tags"></i> Danh mục sản phẩm</a></li>
                <li class="nav-item"><a class="nav-link" href="Nhacungcap.php"><i class="fas fa-truck"></i> Nhà cung cấp</a></li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0)" id="btnPhieuNhap"><i class="fas fa-file-import"></i> Phiếu nhập kho <i class="fas fa-chevron-down float-end"></i></a>
            <ul class="nav flex-column ms-3 d-none" id="submenuPhieuNhap">
                <li class="nav-item"><a class="nav-link" href="danh_sach_phieu_nhap.php"><i class="fas fa-list"></i> Danh sách phiếu nhập</a></li>
                <li class="nav-item"><a class="nav-link" href="phieu_nhap.php"><i class="fas fa-plus-circle"></i> Tạo phiếu nhập</a></li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0)" id="btnPhieuXuat"><i class="fas fa-file-export"></i> Phiếu xuất <i class="fas fa-chevron-down float-end"></i></a>
            <ul class="nav flex-column ms-3 d-none" id="submenuPhieuXuat">
                <li class="nav-item"><a class="nav-link" href="danh_sach_phieu_xuat.php"><i class="fas fa-list"></i> Danh sách phiếu xuất</a></li>
                <li class="nav-item"><a class="nav-link" href="phieu_xuat.php"><i class="fas fa-plus-circle"></i> Tạo phiếu xuất</a></li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0)" id="btnSanXuat"><i class="fas fa-cogs"></i> Sản xuất <i class="fas fa-chevron-down float-end"></i></a>
            <ul class="nav flex-column ms-3 d-none" id="submenuSanXuat">
                <li class="nav-item"><a class="nav-link" href="danh_sach_lenh_san_xuat.php"><i class="fas fa-list"></i> Danh sách lệnh sản xuất</a></li>
                <li class="nav-item"><a class="nav-link" href="lenh_san_xuat.php"><i class="fas fa-plus-circle"></i> Tạo lệnh sản xuất</a></li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0)" id="btnBaoCao"><i class="fas fa-chart-bar"></i> Báo cáo & Thống kê <i class="fas fa-chevron-down float-end"></i></a>
            <ul class="nav flex-column ms-3 d-none" id="submenuBaoCao">
                <li class="nav-item"><a class="nav-link" href="tonkho.php"><i class="fas fa-warehouse"></i> Báo cáo tồn kho</a></li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0)" id="btnKhachHang"><i class="fas fa-users"></i> Quản lý khách hàng <i class="fas fa-chevron-down float-end"></i></a>
            <ul class="nav flex-column ms-3 d-none" id="submenuKhachHang">
                <li class="nav-item"><a class="nav-link" href="khachhang.php"><i class="fas fa-user"></i> Khách hàng</a></li>
                <li class="nav-item"><a class="nav-link" href="loaikhachhang.php"><i class="fas fa-users-cog"></i> Loại khách hàng</a></li>
            </ul>
        </li>
        <li class="nav-item"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
    </ul>
</nav>

<div class="main-content">
  <div class="max-w-5xl mx-auto p-6 space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Sửa lệnh sản xuất</h1>
        <p class="text-slate-500 text-sm mt-1">Chỉnh sửa thông tin lệnh sản xuất: <span class="text-blue-600 font-semibold"><?= htmlspecialchars($malenh) ?></span></p>
      </div>
      <div class="flex gap-2 text-sm">
        <a href="danh_sach_lenh_san_xuat.php" class="px-4 py-2.5 rounded bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold shadow-sm transition">Quay lại danh sách</a>
      </div>
    </div>

    <?php if ($errors): ?>
    <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded">
      <ul class="list-disc list-inside space-y-1">
        <?php foreach ($errors as $er): ?>
          <li><?= htmlspecialchars($er) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>

    <?php if ($success): ?>
    <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
      <?= htmlspecialchars($success) ?>
    </div>
    <?php endif; ?>

    <form method="post" class="bg-white rounded-lg p-6 shadow-sm border border-slate-200 space-y-5">
      <div class="grid md:grid-cols-2 gap-5">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Mã lệnh sản xuất</label>
          <input type="text" disabled class="w-full px-4 py-2.5 rounded bg-slate-100 border border-slate-300 cursor-not-allowed text-slate-500 font-medium" value="<?= htmlspecialchars($lenh['Malenh']) ?>" />
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Sản phẩm <span class="text-red-500">*</span></label>
          <select name="masp" required class="w-full px-4 py-2.5 rounded bg-white border border-slate-300 text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="">-- Chọn sản phẩm --</option>
            <?php foreach ($sanphams as $sp): ?>
              <option value="<?= htmlspecialchars($sp['Masp']) ?>"
                <?= ($lenh['Masp'] === $sp['Masp']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($sp['Tensp']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Ngày sản xuất <span class="text-red-500">*</span></label>
          <input type="date" name="ngaysanxuat" required class="w-full px-4 py-2.5 rounded bg-white border border-slate-300 text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" value="<?= htmlspecialchars($lenh['Ngaysanxuat']) ?>" />
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Số lượng sản xuất <span class="text-red-500">*</span></label>
          <input type="number" name="soluongsanxuat" required min="1" class="w-full px-4 py-2.5 rounded bg-white border border-slate-300 text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" value="<?= htmlspecialchars($lenh['Soluongsanxuat']) ?>" />
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">Ghi chú thêm</label>
        <textarea name="ghichu" rows="3" class="w-full px-4 py-2.5 rounded bg-white border border-slate-300 text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"><?= htmlspecialchars($lenh['Ghichu'] ?? '') ?></textarea>
      </div>

      <div class="pt-4 border-t border-slate-100 flex flex-wrap gap-3 items-center">
        <button type="submit" class="px-6 py-2.5 rounded bg-blue-600 hover:bg-blue-700 text-white font-semibold transition-colors shadow-md" <?= ($lenh['Trangthai'] === 'Hoàn thành') ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : '' ?>>
          <i class="fas fa-save mr-2"></i> Lưu thay đổi
        </button>
        
        <?php if ($lenh['Trangthai'] === 'Hoàn thành'): ?>
            <a href="sua_lenh_san_xuat.php?id=<?= urlencode($malenh) ?>&action=undo" 
               onclick="return confirm('Bạn có chắc muốn ĐƯA VỀ CHƯA HOÀN THÀNH?\n\nHệ thống sẽ thu hồi thành phẩm và hoàn trả nguyên vật liệu vào kho!');"
               class="px-6 py-2.5 rounded bg-red-600 hover:bg-red-700 text-white font-semibold flex items-center transition-colors shadow-md">
               <i class="fas fa-undo mr-2"></i> Trả về chưa hoàn thành
            </a>
            <span class="text-yellow-600 text-sm italic ml-2">* Lệnh đã hoàn thành không thể sửa đổi trực tiếp.</span>
        <?php endif; ?>
      </div>
    </form>
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