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
    <title>Tạo phiếu nhập kho - VLXD</title>
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
        .page-header{background:linear-gradient(135deg,#1e3a5f,#2563eb);color:white;border-radius:12px;padding:24px 28px;margin-bottom:24px}
        .card{border:none;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08)}
        .card-header-custom{background:linear-gradient(135deg,#f0f9ff,#e0f2fe);border-bottom:1px solid #bae6fd;padding:16px 20px;font-weight:700;color:#0c4a6e}
        .detail-row{background:#fff;border-radius:8px;border:1px solid #e2e8f0;padding:12px;margin-bottom:8px}
        .total-bar{background:linear-gradient(135deg,#ecfdf5,#d1fae5);border-radius:8px;padding:16px;margin-top:16px}
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
    <div class="page-header">
        <h1 class="mb-1" style="font-size:1.6rem"><i class="fas fa-file-import me-2"></i>Tạo phiếu nhập kho</h1>
        <p class="mb-0 opacity-75">Ghi nhận NVL nhập và cập nhật tồn kho</p>
    </div>

    <div id="alertBox" class="alert d-none mb-3"></div>

    <div class="card">
        <div class="card-header-custom">Thông tin phiếu nhập</div>
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Mã nhập hàng *</label>
                    <input class="form-control" id="fMa" placeholder="VD: PN2024001 (để trống sẽ tự tạo)">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nhà cung cấp *</label>
                    <select class="form-select" id="fNcc"><option value="">-- Chọn NCC --</option></select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kho nhập *</label>
                    <select class="form-select" id="fKho"><option value="">-- Chọn kho --</option></select>
                </div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Ngày nhập *</label>
                    <input type="date" class="form-control" id="fNgay">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Ghi chú</label>
                    <input class="form-control" id="fGhichu" placeholder="Ghi chú (tuỳ chọn)">
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-primary mb-0"><i class="fas fa-list-ul me-1"></i>Chi tiết nguyên vật liệu</h6>
                <button class="btn btn-sm btn-outline-primary" onclick="addRow()"><i class="fas fa-plus me-1"></i>Thêm dòng</button>
            </div>

            <div id="detailContainer"></div>

            <div class="total-bar d-flex justify-content-between align-items-center">
                <span class="fw-bold text-success">Tổng tiền:</span>
                <span class="fw-bold fs-5 text-success" id="totalAmount">0 đ</span>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button class="btn btn-success fw-semibold px-4" onclick="submitReceipt()"><i class="fas fa-save me-2"></i>Lưu phiếu nhập</button>
                <a href="danh_sach_phieu_nhap.php" class="btn btn-secondary">← Quay lại</a>
            </div>
        </div>
    </div>
</div>
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
</script>
<script>
const API='http://localhost:8000/api/v1';
const headers={'Authorization':'Bearer '+localStorage.getItem('token')};
let materialsData=[];

function toggleMenu(id){const m=document.getElementById(id);m.classList.toggle('open');}
function showAlert(msg,type='success'){const a=document.getElementById('alertBox');a.className=`alert alert-${type}`;a.innerHTML=msg;a.classList.remove('d-none');if(type==='success')setTimeout(()=>a.classList.add('d-none'),4000);}
function fmtMoney(n){return Number(n||0).toLocaleString('vi-VN')+' đ';}

function calcTotal(){
    let total=0;
    document.querySelectorAll('.detail-row').forEach(row=>{
        const sl=parseFloat(row.querySelector('.inp-sl').value||0);
        const dg=parseFloat(row.querySelector('.inp-dg').value||0);
        total+=sl*dg;
    });
    document.getElementById('totalAmount').textContent=fmtMoney(total);
}

function getMatOptions(){
    return '<option value="">-- Chọn NVL --</option>'+materialsData.map(m=>`<option value="${m.Manvl}">${m.Tennvl} (${m.Dvt||''})</option>`).join('');
}

function addRow(){
    const div=document.createElement('div');div.className='detail-row';
    div.innerHTML=`
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-semibold mb-1">Nguyên vật liệu</label>
                <select class="form-select form-select-sm inp-mat">${getMatOptions()}</select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Số lượng</label>
                <input type="number" min="1" class="form-control form-control-sm inp-sl" placeholder="0" oninput="calcTotal()">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Đơn giá (đ)</label>
                <input type="number" min="0" class="form-control form-control-sm inp-dg" placeholder="0" oninput="calcTotal()">
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-outline-danger w-100" onclick="removeRow(this)"><i class="fas fa-trash"></i> Xóa</button>
            </div>
        </div>`;
    document.getElementById('detailContainer').appendChild(div);
}

function removeRow(btn){
    btn.closest('.detail-row').remove();
    calcTotal();
    if(!document.querySelectorAll('.detail-row').length)addRow();
}

async function loadDropdowns(){
    try{
        const [resNcc,resKho,resMat]=await Promise.all([
            fetch(API+'/suppliers',{headers}),
            fetch(API+'/warehouses',{headers}),
            fetch(API+'/materials',{headers})
        ]);
        const [dNcc,dKho,dMat]=await Promise.all([resNcc.json(),resKho.json(),resMat.json()]);
        if(dNcc.success)dNcc.data.suppliers.forEach(s=>{document.getElementById('fNcc').innerHTML+=`<option value="${s.Mancc}">${s.Tenncc}</option>`;});
        if(dKho.success)dKho.data.warehouses.forEach(k=>{document.getElementById('fKho').innerHTML+=`<option value="${k.Makho}">[${k.Makho}] ${k.Tenkho}</option>`;});
        if(dMat.success){materialsData=dMat.data.materials;document.querySelectorAll('.inp-mat').forEach(s=>s.innerHTML=getMatOptions());}
    }catch(e){showAlert('Lỗi tải dữ liệu: '+e.message,'danger');}
}

async function submitReceipt(){
    const mancc=document.getElementById('fNcc').value;
    const makho=document.getElementById('fKho').value;
    const ngay=document.getElementById('fNgay').value;
    if(!mancc||!makho||!ngay){showAlert('Vui lòng nhập đầy đủ NCC, Kho và Ngày nhập.','warning');return;}
    const details=[];
    document.querySelectorAll('.detail-row').forEach(row=>{
        const manvl=row.querySelector('.inp-mat').value;
        const sl=parseFloat(row.querySelector('.inp-sl').value||0);
        const dg=parseFloat(row.querySelector('.inp-dg').value||0);
        if(manvl&&sl>0)details.push({Manvl:manvl,Soluong:sl,Dongianhap:dg});
    });
    async function saveReceipt() {
        if(items.length===0) return alert('Chưa có mặt hàng!');
        const headers = getHeaders();
        if(!headers) return;
        
        const payload = {
            Manhaphang: document.getElementById('fMa').value,
            Mancc: document.getElementById('fNcc').value,
            Makho: document.getElementById('fKho').value,
            Ngaynhaphang: document.getElementById('fNgay').value,
            Ghichu: document.getElementById('fGhichu').value,
            details: items
        };
        try {
            const res = await fetch(API + '/import-receipts', {
                method: 'POST',
                headers: { ...headers, 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data=await res.json();
            if(data.success){showAlert(`<strong>Tạo phiếu nhập thành công!</strong> Mã phiếu: <code>${data.data.id}</code> &nbsp; <a href="danh_sach_phieu_nhap.php" class="btn btn-sm btn-success">← Về danh sách</a>`);document.getElementById('fMa').value='';document.getElementById('fGhichu').value='';document.getElementById('detailContainer').innerHTML='';addRow();calcTotal();}
            else showAlert(data.message,'danger');
        }catch(e){showAlert('Lỗi kết nối server','danger');}
    }
    if(!details.length){showAlert('Vui lòng thêm ít nhất một NVL','warning');return;}
    const payload={Manhaphang:document.getElementById('fMa').value||undefined,Mancc:mancc,Makho:makho,Ngaynhaphang:ngay,Ghichu:document.getElementById('fGhichu').value,details};
    try{
        const res=await fetch(API+'/import-receipts',{method:'POST',headers:{...headers,'Content-Type':'application/json'},body:JSON.stringify(payload)});
        const data=await res.json();
        if(data.success){showAlert(`<strong>Tạo phiếu nhập thành công!</strong> Mã phiếu: <code>${data.data.id}</code> &nbsp; <a href="danh_sach_phieu_nhap.php" class="btn btn-sm btn-success">← Về danh sách</a>`);document.getElementById('fMa').value='';document.getElementById('fGhichu').value='';document.getElementById('detailContainer').innerHTML='';addRow();calcTotal();}
        else showAlert(data.message,'danger');
    }catch(e){showAlert('Lỗi kết nối server','danger');}
}

document.getElementById('fNgay').valueAsDate=new Date();
loadDropdowns();
addRow();
</script>
</body>
</html>
