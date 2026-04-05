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
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Danh sách phiếu xuất - VLXD</title>
    <script>const token=localStorage.getItem('token');if(!token)window.location.href='dangnhap.php';</script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body{background:#f0f4f8;font-family:'Segoe UI',sans-serif}
        .sidebar{background:linear-gradient(180deg,#1e3a5f,#0d2137);height:100vh;position:fixed;width:250px;color:white;padding-top:20px;top:0;left:0;overflow-y:auto;z-index:100}
        .sidebar h4{font-size:1rem;font-weight:700;padding:0 20px 15px;border-bottom:1px solid rgba(255,255,255,.1)}
        .sidebar .nav-link{color:rgba(255,255,255,.8)!important;padding:9px 18px;border-radius:6px;margin:2px 8px;transition:all .25s;font-size:.87rem}
        .sidebar .nav-link:hover{background:rgba(255,255,255,.15)!important;color:#fff!important;transform:translateX(4px)}
        .submenu{display:none}.submenu.open{display:block}
        .main-content{margin-left:250px;padding:30px}
        .page-header{background:linear-gradient(135deg,#7c2d12,#ea580c);color:white;border-radius:12px;padding:24px 28px;margin-bottom:24px}
        .card{border:none;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08)}
        .table th{background:#f8fafc;color:#475569;font-size:.8rem;text-transform:uppercase}
        .table td{vertical-align:middle}
        .money{color:#16a34a;font-weight:700}
        .stat-badge{background:#fed7aa;color:#9a3412;padding:2px 10px;border-radius:20px;font-size:.78rem;font-weight:600}
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

<div class="main-content">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-1" style="font-size:1.6rem"><i class="fas fa-file-export me-2"></i>Danh sách phiếu xuất kho</h1>
            <p class="mb-0 opacity-75">Quản lý phiếu xuất thành phẩm cho khách hàng</p>
        </div>
        <a href="phieu_xuat.php" class="btn btn-light fw-semibold"><i class="fas fa-plus me-2"></i>Tạo phiếu xuất</a>
    </div>

    <div id="alertBox" class="alert d-none mb-3"></div>

    <div class="card mb-4">
        <div class="card-body p-0">
            <div class="p-3 d-flex gap-2">
                <input class="form-control" id="searchInput" placeholder="🔍 Tìm theo mã phiếu, khách hàng, kho..." oninput="filterTable()">
                <button class="btn btn-outline-secondary" onclick="loadReceipts()"><i class="fas fa-sync-alt"></i></button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr>
                        <th>Mã phiếu</th><th>Khách hàng</th><th>Kho xuất</th><th>Ngày xuất</th>
                        <th class="text-center">Số SP</th><th class="text-end">Tổng tiền</th><th class="text-center">Thao tác</th>
                    </tr></thead>
                    <tbody id="tbody"><tr><td colspan="7" class="text-center py-4 text-muted">Đang tải...</td></tr></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
const API='http://localhost:8000/api/v1';
const headers={'Authorization':'Bearer '+localStorage.getItem('token')};
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

function toggleMenu(id){const m=document.getElementById(id);m.classList.toggle('open');}
function showAlert(msg,type='success'){const a=document.getElementById('alertBox');a.className=`alert alert-${type}`;a.textContent=msg;a.classList.remove('d-none');setTimeout(()=>a.classList.add('d-none'),4000);}
function filterTable(){const q=document.getElementById('searchInput').value.toLowerCase();document.querySelectorAll('#tbody tr').forEach(tr=>{tr.style.display=tr.textContent.toLowerCase().includes(q)?'':'none';});}
function fmtDate(s){if(!s)return'—';return new Date(s).toLocaleDateString('vi-VN');}
function fmtMoney(n){return Number(n||0).toLocaleString('vi-VN')+' đ';}

async function loadReceipts(){
    try{
        const res=await fetch(API+'/export-receipts',{headers});
        const data=await res.json();
        if(!data.success)throw new Error(data.message);
        const tb=document.getElementById('tbody');
        if(!data.data.receipts.length){tb.innerHTML='<tr><td colspan="7" class="text-center py-4 text-muted">Chưa có phiếu xuất nào.</td></tr>';return;}
        tb.innerHTML=data.data.receipts.map(r=>`<tr>
            <td><code class="fw-bold text-warning">${r.Maxuathang}</code></td>
            <td>${r.Tenkh||r.Makh||'—'}</td>
            <td>${r.Tenkho||r.Makho||'—'}</td>
            <td>${fmtDate(r.Ngayxuat)}</td>
            <td class="text-center"><span class="stat-badge">${r.SoMatHang||0}</span></td>
            <td class="text-end money">${fmtMoney(r.Tongtienxuat)}</td>
            <td class="text-center">
                <a href="chi_tiet_phieu_xuat.php?id=${encodeURIComponent(r.Maxuathang)}" class="btn btn-sm btn-outline-success me-1" title="Chi tiết"><i class="fas fa-eye"></i></a>
                <button class="btn btn-sm btn-outline-danger" onclick="del('${r.Maxuathang}')" title="Xóa"><i class="fas fa-trash"></i></button>
            </td>
        </tr>`).join('');
    }catch(e){document.getElementById('tbody').innerHTML=`<tr><td colspan="7" class="text-center text-danger py-4">Lỗi: ${e.message}</td></tr>`;}
}

async function del(id){
    if(!confirm('Xóa phiếu xuất '+id+'?\nHành động này sẽ hoàn lại tồn kho SP.'))return;
    try{
        const res=await fetch(API+'/export-receipts/'+id,{method:'DELETE',headers});
        const data=await res.json();
        if(data.success){showAlert('Đã xóa phiếu xuất');loadReceipts();}
        else showAlert(data.message,'danger');
    }catch(e){showAlert('Lỗi kết nối','danger');}
}

loadReceipts();
</script>
</body>
</html>