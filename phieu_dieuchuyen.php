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
$khos = $pdo->query("SELECT Makho, Tenkho FROM Kho ORDER BY Tenkho")->fetchAll();

$sanphams = $pdo->query("
    SELECT Masp, Tensp, Dvt
    FROM Sanpham
    ORDER BY Tensp
")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $madieuchuyen = trim($_POST['madieuchuyen'] ?? '');
    $khoxuat = trim($_POST['khoxuat'] ?? '');
    $khonhap = trim($_POST['khonhap'] ?? '');
    $ngaydieuchuyen = $_POST['ngaydieuchuyen'] ?? '';
    $ghichu = trim($_POST['ghichu'] ?? '');

    $maspArr = $_POST['masp'] ?? [];
    $soluongArr = $_POST['soluong'] ?? [];

    // Kiểm tra dữ liệu chính
    if ($madieuchuyen === '' || $khoxuat === '' || $khonhap === '' || $ngaydieuchuyen === '') {
        $errors[] = 'Vui lòng nhập đầy đủ Mã điều chuyển, Kho xuất, Kho nhập, Ngày điều chuyển.';
    }
    if ($khoxuat === $khonhap) {
        $errors[] = 'Kho xuất và kho nhập không được trùng nhau.';
    }

    // Chuẩn hóa chi tiết sản phẩm
    $items = [];
    for ($i = 0; $i < count($maspArr); $i++) {
        $masp = trim($maspArr[$i] ?? '');
        $soluong = (float)($soluongArr[$i] ?? 0);
        if ($masp !== '' && $soluong > 0) {
            // Kiểm tra tồn kho ở kho xuất
            $stmt = $pdo->prepare("SELECT Soluongton FROM Tonkho_sp WHERE Makho = ? AND Masp = ?");
            $stmt->execute([$khoxuat, $masp]);
            $tonkho = $stmt->fetchColumn();
            if ($tonkho === false || $tonkho < $soluong) {
                $errors[] = "Sản phẩm $masp không đủ tồn kho ở kho xuất (còn: " . ($tonkho ?: 0) . ").";
            } else {
                $items[] = ['masp' => $masp, 'soluong' => $soluong];
            }
        }
    }

    if (empty($items)) {
        $errors[] = 'Vui lòng chọn ít nhất một sản phẩm để điều chuyển.';
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // Insert phiếu điều chuyển
            $stmt = $pdo->prepare("
                INSERT INTO Phieudieuchuyen (Madieuchuyen, Khoxuat, Khonhap, Ngaydieuchuyen, Ghichu)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$madieuchuyen, $khoxuat, $khonhap, $ngaydieuchuyen, $ghichu]);

            // Insert chi tiết
            $stmtDetail = $pdo->prepare("
                INSERT INTO Chitiet_Phieudieuchuyen (Madieuchuyen, Masp, Soluong)
                VALUES (?, ?, ?)
            ");
            foreach ($items as $item) {
                $stmtDetail->execute([$madieuchuyen, $item['masp'], $item['soluong']]);
            }

            $pdo->commit();
            $success = 'Tạo phiếu điều chuyển thành công.';
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Lỗi khi tạo phiếu: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phiếu điều chuyển kho</title>
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
        <div class="max-w-5xl mx-auto p-6 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Phiếu điều chuyển kho</h1>
                    <p class="text-slate-400 text-sm mt-1">Điều chuyển hàng hóa giữa các kho</p>
                </div>
                <div class="flex gap-2 text-sm">
                    <a href="danh_sach_phieu_dieuchuyen.php" class="px-4 py-2 rounded bg-slate-600 hover:bg-slate-700 font-semibold">← Danh sách phiếu điều chuyển</a>
                </div>
            </div>

            <?php if ($errors): ?>
                <div class="bg-red-900/60 border border-red-700 text-red-200 px-4 py-3 rounded">
                    <ul class="list-disc list-inside space-y-1">
                        <?php foreach ($errors as $er): ?>
                            <li><?= htmlspecialchars($er) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-emerald-900/60 border border-emerald-700 text-emerald-100 px-4 py-3 rounded">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <form method="post" class="bg-slate-800 rounded-lg p-5 space-y-4">
                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm text-slate-300 mb-2">Mã điều chuyển *</label>
                        <input name="madieuchuyen" required class="w-full px-3 py-2 rounded bg-slate-900 border border-slate-700" value="<?= htmlspecialchars($_POST['madieuchuyen'] ?? '') ?>" />
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2">Kho xuất *</label>
                        <select name="khoxuat" required class="w-full px-3 py-2 rounded bg-slate-900 border border-slate-700">
                            <option value="">-- Chọn kho xuất --</option>
                            <?php foreach ($khos as $k): ?>
                                <option value="<?= htmlspecialchars($k['Makho']) ?>"
                                    <?= (($_POST['khoxuat'] ?? '') === $k['Makho']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($k['Tenkho']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2">Kho nhập *</label>
                        <select name="khonhap" required class="w-full px-3 py-2 rounded bg-slate-900 border border-slate-700">
                            <option value="">-- Chọn kho nhập --</option>
                            <?php foreach ($khos as $k): ?>
                                <option value="<?= htmlspecialchars($k['Makho']) ?>"
                                    <?= (($_POST['khonhap'] ?? '') === $k['Makho']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($k['Tenkho']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-slate-300 mb-2">Ngày điều chuyển *</label>
                        <input name="ngaydieuchuyen" type="date" required class="w-full px-3 py-2 rounded bg-slate-900 border border-slate-700" value="<?= htmlspecialchars($_POST['ngaydieuchuyen'] ?? '') ?>" />
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2">Ghi chú</label>
                        <input name="ghichu" class="w-full px-3 py-2 rounded bg-slate-900 border border-slate-700" value="<?= htmlspecialchars($_POST['ghichu'] ?? '') ?>" />
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-200">Chi tiết sản phẩm điều chuyển</h3>
                    <div id="product-list">
                        <div class="product-item grid md:grid-cols-3 gap-4 items-end">
                            <div>
                                <label class="block text-sm text-slate-300 mb-2">Sản phẩm</label>
                                <select name="masp[]" class="w-full px-3 py-2 rounded bg-slate-900 border border-slate-700">
                                    <option value="">-- Chọn sản phẩm --</option>
                                    <?php foreach ($sanphams as $sp): ?>
                                        <option value="<?= htmlspecialchars($sp['Masp']) ?>">
                                            <?= htmlspecialchars($sp['Tensp']) ?> (<?= htmlspecialchars($sp['Dvt']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm text-slate-300 mb-2">Số lượng</label>
                                <input name="soluong[]" type="number" step="0.01" min="0" class="w-full px-3 py-2 rounded bg-slate-900 border border-slate-700" />
                            </div>
                            <div>
                                <button type="button" class="remove-item px-3 py-2 rounded bg-red-600 hover:bg-red-700 text-white" style="display: none;">Xóa</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="add-product" class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">+ Thêm sản phẩm</button>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 rounded bg-green-600 hover:bg-green-700 text-white font-semibold">Tạo phiếu điều chuyển</button>
                </div>
            </form>
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

        // Add product functionality
        document.getElementById("add-product").addEventListener("click", function () {
            const productList = document.getElementById("product-list");
            const newItem = productList.querySelector(".product-item").cloneNode(true);
            newItem.querySelector("select").selectedIndex = 0;
            newItem.querySelector("input").value = "";
            newItem.querySelector(".remove-item").style.display = "block";
            productList.appendChild(newItem);
        });

        document.addEventListener("click", function (e) {
            if (e.target.classList.contains("remove-item")) {
                e.target.closest(".product-item").remove();
            }
        });
    </script>
</body>
</html>