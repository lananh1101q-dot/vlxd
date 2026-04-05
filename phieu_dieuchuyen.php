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
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <script>
        const token = localStorage.getItem('token');
        if (!token) window.location.href = 'dangnhap.php';
    </script>
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
            background-color: #ffffff;
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
<body class="bg-white">
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
    <div class="main-content">
        <div class="max-w-5xl mx-auto p-6 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Phiếu điều chuyển kho</h1>
                    <p class="text-slate-600 text-sm mt-1">Điều chuyển hàng hóa giữa các kho</p>
                </div>
                <div class="flex gap-2 text-sm">
                    <a href="danh_sach_phieu_dieuchuyen.php" class="px-4 py-2 rounded bg-slate-600 hover:bg-slate-700 font-semibold">← Danh sách phiếu điều chuyển</a>
                </div>
            </div>

            <div id="alertMsg" class="hidden px-4 py-3 rounded mb-4"></div>

            <form id="formDieuChuyen" onsubmit="event.preventDefault(); submitTransfer();" class="bg-white border border-slate-300 rounded-lg p-5 space-y-4">
                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm text-slate-800 mb-2 font-semibold">Mã điều chuyển *</label>
                        <input id="madieuchuyen" name="madieuchuyen" required class="w-full px-3 py-2 rounded bg-slate-50 border border-slate-300 text-slate-900" placeholder="Tự động nếu để trống" />
                    </div>

                    <div>
                        <label class="block text-sm text-slate-800 mb-2 font-semibold">Kho xuất *</label>
                        <select id="khoxuat" name="khoxuat" required class="w-full px-3 py-2 rounded bg-slate-50 border border-slate-300 text-slate-900">
                            <option value="">-- Chọn kho xuất --</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-800 mb-2 font-semibold">Kho nhập *</label>
                        <select id="khonhap" name="khonhap" required class="w-full px-3 py-2 rounded bg-slate-50 border border-slate-300 text-slate-900">
                            <option value="">-- Chọn kho nhập --</option>
                        </select>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-slate-800 mb-2 font-semibold">Ngày điều chuyển *</label>
                        <input id="ngaydieuchuyen" name="ngaydieuchuyen" type="date" required class="w-full px-3 py-2 rounded bg-slate-50 border border-slate-300 text-slate-900" />
                    </div>

                    <div>
                        <label class="block text-sm text-slate-800 mb-2 font-semibold">Ghi chú</label>
                        <input name="ghichu" class="w-full px-3 py-2 rounded bg-slate-50 border border-slate-300 text-slate-900" value="" />
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-900">Chi tiết sản phẩm điều chuyển</h3>
                    <div id="product-list">
                        <div class="product-item grid md:grid-cols-3 gap-4 items-end">
                            <div>
                                <label class="block text-sm text-slate-800 mb-2 font-semibold">Sản phẩm</label>
                                <select name="masp[]" class="sp-select w-full px-3 py-2 rounded bg-slate-50 border border-slate-300 text-slate-900">
                                    <option value="">-- Chọn sản phẩm --</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm text-slate-800 mb-2 font-semibold">Số lượng</label>
                                <input name="soluong[]" type="number" step="0.01" min="0" class="w-full px-3 py-2 rounded bg-slate-50 border border-slate-300 text-slate-900" />
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

        // ======================= MICROSERVICES FETCH API =========================
        let productsData = [];

        async function initData() {
            // Gọi Gateway lấy kho và sản phẩm
            try {
                const token = localStorage.getItem('token');
                const headers = { 'Authorization': 'Bearer ' + token };
                const [resKho, resSp] = await Promise.all([
                    fetch('http://localhost:8000/api/v1/warehouses', { headers }),
                    fetch('http://localhost:8000/api/v1/products', { headers })
                ]);
                const dataKho = await resKho.json();
                const dataSp = await resSp.json();

                if(dataKho.success && dataKho.data.warehouses) {
                    let kHtml = '<option value="">-- Chọn kho --</option>';
                    dataKho.data.warehouses.forEach(k => {
                        kHtml += `<option value="${k.Makho}">${k.Tenkho}</option>`;
                    });
                    document.getElementById('khoxuat').innerHTML = kHtml;
                    document.getElementById('khonhap').innerHTML = kHtml;
                }

                if(dataSp.success && dataSp.data.products) {
                    productsData = dataSp.data.products;
                    updateProductSelects();
                }
            } catch(e) {
                console.error("Lỗi lấy dữ liệu API", e);
            }
        }

        function updateProductSelects() {
            let pHtml = '<option value="">-- Chọn sản phẩm --</option>';
            productsData.forEach(p => {
                pHtml += `<option value="${p.Masp}">${p.Masp} - ${p.Tensp}</option>`;
            });
            document.querySelectorAll('.sp-select').forEach(sel => {
                if(sel.options.length <= 1) sel.innerHTML = pHtml;
            });
        }

        document.getElementById("add-product").addEventListener("click", function () {
            const productList = document.getElementById("product-list");
            const newItem = productList.querySelector(".product-item").cloneNode(true);
            newItem.querySelector("select").selectedIndex = 0;
            newItem.querySelector("input").value = "";
            newItem.querySelector(".remove-item").style.display = "block";
            productList.appendChild(newItem);
        });

        async function submitTransfer() {
            const mag = document.getElementById("madieuchuyen").value;
            const kx = document.getElementById("khoxuat").value;
            const kn = document.getElementById("khonhap").value;
            const ns = document.getElementById("ngaydieuchuyen").value;

            const items = [];
            document.querySelectorAll('.product-item').forEach(el => {
                const p = el.querySelector('select').value;
                const sl = el.querySelector('input').value;
                if(p && sl) {
                    items.push({ Masp: p, Soluong: sl });
                }
            });

            if(items.length === 0) {
                showAlert('Vui lòng thêm sản phẩm vào phiếu.', false);
                return;
            }

            const payload = {
                Madieuchuyen: mag,
                Khoxuat: kx,
                Khonhap: kn,
                Ngaydieuchuyen: ns,
                details: items
            };

            try {
                const token = localStorage.getItem('token');
                const res = await fetch('http://localhost:8000/api/v1/transfers', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify(payload)
                });
                const d = await res.json();
                if(d.success) {
                    showAlert('Tạo phiếu điều chuyển thành công!', true);
                    setTimeout(() => window.location.href='danh_sach_phieu_dieuchuyen.php', 1000);
                } else {
                    showAlert(d.message, false);
                }
            } catch(e) {
                showAlert('Lỗi kết nối máy chủ', false);
            }
        }

        function showAlert(msg, isSuccess) {
            const a = document.getElementById('alertMsg');
            a.classList.remove('hidden', 'bg-red-900/60', 'text-red-200', 'bg-emerald-900/60', 'text-emerald-100');
            if(isSuccess) {
                a.classList.add('bg-emerald-900/60', 'text-emerald-100');
            } else {
                a.classList.add('bg-red-900/60', 'text-red-200');
            }
            a.innerHTML = msg;
        }

        initData();
        document.getElementById('ngaydieuchuyen').valueAsDate = new Date();
    </script>
</body>
</html>