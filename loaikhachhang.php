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
    <title>Loại khách hàng - VLXD</title>
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
        .page-header{background:linear-gradient(135deg,#0891b2,#06b6d4);color:white;border-radius:12px;padding:24px 28px;margin-bottom:24px;box-shadow:0 4px 15px rgba(6,182,212,.3)}
        .card{border:none;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08)}
        .table th{background:#f8fafc;color:#475569;font-size:.8rem;text-transform:uppercase}
        .table td{vertical-align:middle}
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
            <h1 class="mb-1" style="font-size:1.6rem"><i class="fas fa-users-cog me-2"></i>Loại khách hàng</h1>
            <p class="mb-0 opacity-75">Quản lý các loại khách hàng trong hệ thống</p>
        </div>
        <button class="btn btn-light fw-semibold" onclick="openModal()"><i class="fas fa-plus me-2"></i>Thêm loại KH</button>
    </div>

    <div id="alertBox" class="alert d-none mb-3"></div>

    <div class="card">
        <div class="card-body p-0">
            <div class="p-3"><input class="form-control" id="searchInput" placeholder="🔍 Tìm kiếm..." oninput="filterTable()"></div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Mã loại</th><th>Tên loại</th><th>Mô tả</th><th class="text-center">Thao tác</th></tr></thead>
                    <tbody id="tbody"><tr><td colspan="4" class="text-center py-4 text-muted">Đang tải...</td></tr></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="lkhModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content" style="border-radius:12px;border:none">
        <div class="modal-header border-0"><h5 class="modal-title fw-bold" id="modalTitle">Thêm loại KH</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body pt-0">
            <div class="mb-3"><label class="form-label fw-semibold">Mã loại *</label><input class="form-control" id="fMa" placeholder="Hệ thống tự động tạo mã" disabled></div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Tên loại *</label>
                <select class="form-select" id="fTen" required>
                    <option value="">-- Chọn loại khách hàng --</option>
                    <option value="Khách lẻ">Khách lẻ</option>
                    <option value="Khách sỉ">Khách sỉ</option>
                    <option value="Đại lý cấp 1">Đại lý cấp 1</option>
                    <option value="Đại lý cấp 2">Đại lý cấp 2</option>
                    <option value="Đại lý cấp 3">Đại lý cấp 3</option>
                    <option value="Nhà thầu/Dự án">Nhà thầu/Dự án</option>
                    <option value="Nhà phân phối">Nhà phân phối</option>
                    <option value="VIP">VIP</option>
                </select>
            </div>
            <div class="mb-3"><label class="form-label fw-semibold">Mô tả</label><textarea class="form-control" id="fMota" rows="2" placeholder="Mô tả (tuỳ chọn)"></textarea></div>
        </div>
        <div class="modal-footer border-0">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button class="btn btn-primary fw-semibold" onclick="save()"><i class="fas fa-save me-1"></i>Lưu</button>
        </div>
    </div></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
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
const getHeaders = () => {
    const token = localStorage.getItem('token');
    if (!token) return null;
    return { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' };
};
const API = 'http://localhost:8000/api/v1';
let editId = null;

function toggleMenu(id){const m=document.getElementById(id);m.classList.toggle('open');}
function showAlert(msg,type='success'){const a=document.getElementById('alertBox');a.className=`alert alert-${type}`;a.textContent=msg;a.classList.remove('d-none');setTimeout(()=>a.classList.add('d-none'),4000);}
function filterTable(){const q=document.getElementById('searchInput').value.toLowerCase();document.querySelectorAll('#tbody tr').forEach(tr=>{tr.style.display=tr.textContent.toLowerCase().includes(q)?'':'none';});}

async function load(){
    const headers = getHeaders();
    if (!headers) return window.location.href = 'dangnhap.php';
    try{
        const res=await fetch(API+'/customer-types',{headers});
        const data=await res.json();
        if(!data.success)throw new Error(data.message);
        const tb=document.getElementById('tbody');
        if(!data.data.types.length){tb.innerHTML='<tr><td colspan="4" class="text-center py-4 text-muted">Chưa có loại khách hàng.</td></tr>';return;}
        tb.innerHTML=data.data.types.map(r=>`<tr>
            <td><code class="fw-bold text-primary">${r.Maloaikh}</code></td>
            <td><span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">${r.Tenloaikh}</span></td>
            <td class="text-muted">${r.Mota||''}</td>
            <td class="text-center">
                <button class="btn btn-sm btn-outline-primary me-1" onclick="editType('${r.Maloaikh}','${r.Tenloaikh}','${(r.Mota||'').replace(/'/g,`\\'`)}')"><i class="fas fa-edit"></i></button>
                <button class="btn btn-sm btn-outline-danger" onclick="del('${r.Maloaikh}','${r.Tenloaikh}')"><i class="fas fa-trash"></i></button>
            </td>
        </tr>`).join('');
    }catch(e){document.getElementById('tbody').innerHTML=`<tr><td colspan="4" class="text-center text-danger py-4">Lỗi: ${e.message}</td></tr>`;}
}

function openModal(){editId=null;document.getElementById('modalTitle').textContent='Thêm loại KH';document.getElementById('fMa').value='';document.getElementById('fMa').disabled=true;document.getElementById('fTen').value='';document.getElementById('fMota').value='';new bootstrap.Modal(document.getElementById('lkhModal')).show();}
function editType(id,ten,mota){editId=id;document.getElementById('modalTitle').textContent='Sửa loại KH';document.getElementById('fMa').value=id;document.getElementById('fMa').disabled=true;document.getElementById('fTen').value=ten;document.getElementById('fMota').value=mota;new bootstrap.Modal(document.getElementById('lkhModal')).show();}

async function save() {
    const headers = getHeaders();
    if(!headers) return;
    const isEdit = editId !== null;
    const body = {
        Maloaikh: document.getElementById('fMa').value,
        Tenloaikh: document.getElementById('fTen').value,
        Mota: document.getElementById('fMota').value
    };

    if(isEdit && !body.Maloaikh) return alert('Bị mất mã loại!');
    if(!body.Tenloaikh) return alert('Vui lòng chọn tên loại khách hàng!');

    try {
        const url = isEdit ? `${API}/customer-types/${body.Maloaikh}` : `${API}/customer-types`;
        const method = isEdit ? 'PUT' : 'POST';
        
        const res = await fetch(url, {
            method,
            headers,
            body: JSON.stringify(body)
        });
        const data = await res.json();
        
        if(data.success) {
            showAlert(isEdit ? 'Cập nhật thành công!' : 'Thêm mới thành công!');
            bootstrap.Modal.getInstance(document.getElementById('lkhModal')).hide();
            load();
        } else {
            alert('Lỗi: ' + data.message);
        }
    } catch(e) {
        alert('Lỗi kết nối API');
    }
}

async function del(id) {
    if(!confirm(`Xác nhận xóa loại khách hàng ${id}?`)) return;
    const headers = getHeaders();
    if(!headers) return;
    try {
        const res = await fetch(`${API}/customer-types/${id}`, { method: 'DELETE', headers });
        const data = await res.json();
        if(data.success) { showAlert('Đã xóa loại KH'); load(); }
        else alert(data.message);
    } catch(e) { alert('Lỗi kết nối'); }
}

load();
</script>
</body>
</html>