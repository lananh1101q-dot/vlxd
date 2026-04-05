<!DOCTYPE html>
<html lang="vi">
<head>
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
$role = $user['role'] ?? 'sanxuat';
$roleName = getRoleName($role);
?>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Danh sách lệnh sản xuất - VLXD</title>
    <script>const token=localStorage.getItem('token');</script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body{background:#f0f4f8;font-family:'Segoe UI',sans-serif}
        .sidebar{background:linear-gradient(180deg,#007bff);height:100vh;position:fixed;width:250px;color:white;padding-top:20px;top:0;left:0;overflow-y:auto;z-index:100}
        .sidebar h4{font-size:1rem;font-weight:700;padding:0 20px 15px;border-bottom:1px solid rgba(5, 121, 236, 0.84)}
        .sidebar .nav-link{color:rgba(255,255,255,.8)!important;padding:9px 18px;border-radius:6px;margin:2px 8px;transition:all .25s;font-size:.87rem}
        .sidebar .nav-link:hover{background:hsla(199, 100%, 50%, 0.97)!important;color:#fff!important;transform:translateX(4px)}
        .submenu{display:none}.submenu.open{display:block}
        .main-content{margin-left:250px;padding:30px}
        .page-header{background:linear-gradient(135deg,#1e1b4b,#7c3aed);color:white;border-radius:12px;padding:24px 28px;margin-bottom:24px}
        .card{border:none;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08)}
        .table th{background:#f8fafc;color:#475569;font-size:.8rem;text-transform:uppercase}
        .table td{vertical-align:middle}
        .badge-status{padding:4px 12px;border-radius:20px;font-size:.76rem;font-weight:600}
        .status-cho{background:#fef9c3;color:#a16207}
        .status-dang{background:#dbeafe;color:#1d4ed8}
        .status-hoan{background:#dcfce7;color:#15803d}
        .status-huy{background:#fee2e2;color:#dc2626}
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

        <!-- PHIẾU XUẤT -->
        <?php if (!empty($permission['phieuxuat']) || isset($permission['all'])): ?>
        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0)" id="btnPhieuXuat">
                <i class="fas fa-file-export"></i> Phiếu xuất
            </a>
            <ul class="nav flex-column ms-3 d-none" id="submenuPhieuXuat">
                <li><a class="nav-link" href="danh_sach_phieu_xuat.php">Danh sách</a></li>
                <li><a class="nav-link" href="phieu_xuat.php">Tạo phiếu</a></li>
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
            <h1 class="mb-1" style="font-size:1.6rem"><i class="fas fa-cogs me-2"></i>Danh sách lệnh sản xuất</h1>
            <p class="mb-0 opacity-75">Quản lý quy trình sản xuất thành phẩm</p>
        </div>
        <a href="lenh_san_xuat.php" class="btn btn-light fw-semibold"><i class="fas fa-plus me-2"></i>Tạo lệnh SX</a>
    </div>

    <div id="alertBox" class="alert d-none mb-3"></div>

    <div class="card mb-4">
        <div class="card-body p-0">
            <div class="p-3 d-flex gap-2 align-items-center">
                <input class="form-control" id="searchInput" placeholder="🔍 Tìm theo mã lệnh, sản phẩm..." oninput="filterTable()" style="max-width:360px">
                <div class="ms-auto d-flex gap-2">
                    <button class="btn btn-outline-secondary btn-sm" onclick="load()"><i class="fas fa-sync-alt"></i></button>
                    <select class="form-select form-select-sm" id="filterStatus" onchange="load()" style="width:auto">
                        <option value="">Tất cả trạng thái</option>
                        <option value="cho_xu_ly">Chờ xử lý</option>
                        <option value="dang_san_xuat">Đang sản xuất</option>
                        <option value="hoan_thanh">Hoàn thành</option>
                        <option value="huy">Đã hủy</option>
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr>
                        <th>Mã lệnh</th><th>Sản phẩm</th><th>Ngày SX</th>
                        <th class="text-center">SL sản xuất</th><th class="text-center">Trạng thái</th><th class="text-center">Thao tác</th>
                    </tr></thead>
                    <tbody id="tbody"><tr><td colspan="6" class="text-center py-4 text-muted">Đang tải...</td></tr></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal hoàn thành sản xuất -->
<div class="modal fade" id="hoanThanhModal" tabindex="-1">
    <div class="modal-dialog modal-sm"><div class="modal-content" style="border-radius:12px">
        <div class="modal-header border-0"><h6 class="modal-title fw-bold">Hoàn thành sản xuất</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body pt-0">
            <p class="text-muted small mb-3">Nhập kho cho lệnh: <strong id="htMalenh"></strong></p>
            <label class="form-label fw-semibold small">Kho nhập thành phẩm *</label>
            <select class="form-select form-select-sm" id="htKho"><option value="">-- Chọn kho --</option></select>
        </div>
        <div class="modal-footer border-0 pt-0">
            <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
            <button class="btn btn-success btn-sm fw-semibold" onclick="confirmComplete()"><i class="fas fa-check me-1"></i>Xác nhận</button>
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
    toggleMenu("btnSanXuat", "submenuSanXuat");
    toggleMenu("btnBaoCao", "submenuBaoCao");
    toggleMenu("btnKhachHang", "submenuKhachHang");

});
</script>
<script>
const API='http://localhost:8000/api/v1';
const headers={'Authorization':'Bearer '+localStorage.getItem('token')};
let currentMalenh=null;


function toggleMenu(id){const m=document.getElementById(id);m.classList.toggle('open');}
function showAlert(msg,type='success'){const a=document.getElementById('alertBox');a.className=`alert alert-${type}`;a.textContent=msg;a.classList.remove('d-none');setTimeout(()=>a.classList.add('d-none'),4000);}
function filterTable(){const q=document.getElementById('searchInput').value.toLowerCase();document.querySelectorAll('#tbody tr').forEach(tr=>{tr.style.display=tr.textContent.toLowerCase().includes(q)?'':'none';});}
function fmtDate(s){if(!s)return'—';return new Date(s).toLocaleDateString('vi-VN');}
function statusBadge(s){const m={cho_xu_ly:'status-cho',dang_san_xuat:'status-dang',hoan_thanh:'status-hoan',huy:'status-huy'};const l={cho_xu_ly:'Chờ xử lý',dang_san_xuat:'Đang sản xuất',hoan_thanh:'Hoàn thành',huy:'Đã hủy'};return`<span class="badge-status ${m[s]||'status-cho'}">${l[s]||s}</span>`;}

async function load(){
    try{
        const res=await fetch(API+'/production-orders',{headers});
        const data=await res.json();
        if(!data.success)throw new Error(data.message);
        const tb=document.getElementById('tbody');
        let rows=data.data.orders;
        const flt=document.getElementById('filterStatus').value;
        if(flt)rows=rows.filter(r=>r.Trangthai===flt);
        if(!rows.length){tb.innerHTML='<tr><td colspan="6" class="text-center py-4 text-muted">Không có lệnh sản xuất nào.</td></tr>';return;}
        tb.innerHTML=rows.map(r=>`<tr>
            <td><code class="fw-bold text-purple-600" style="color:#7c3aed">${r.Malenh}</code></td>
            <td><strong>${r.Tensp||r.Masp}</strong><br><small class="text-muted">${r.Masp}</small></td>
            <td>${fmtDate(r.Ngaysanxuat)}</td>
            <td class="text-center fw-bold">${Number(r.Soluongsanxuat||0).toLocaleString('vi-VN')}</td>
            <td class="text-center">${statusBadge(r.Trangthai)}</td>
            <td class="text-center">
                <div class="d-flex gap-1 justify-content-center">

        <!-- NÚT SỬA -->
        ${r.Trangthai !== 'hoan_thanh' ? `
        <a href="sua_lenh_san_xuat.php?id=${r.Malenh}" 
           class="btn btn-sm btn-outline-primary" 
           title="Sửa">
            <i class="fas fa-edit"></i>
        </a>
        ` : ''}

        ${r.Trangthai!=='hoan_thanh' && r.Trangthai!=='huy' ? `
        <button class="btn btn-sm btn-outline-success" 
            onclick="openComplete('${r.Malenh}')" 
            title="Hoàn thành & nhập kho">
            <i class="fas fa-check-circle"></i>
        </button>

        <button class="btn btn-sm btn-outline-warning" 
            onclick="updateStatus('${r.Malenh}','dang_san_xuat')" 
            title="Đánh dấu đang SX">
            <i class="fas fa-play"></i>
        </button>
        ` : ''}

        <button class="btn btn-sm btn-outline-danger" 
            onclick="del('${r.Malenh}')" 
            title="Xóa">
            <i class="fas fa-trash"></i>
        </button>

                </div>
            </td>
        </tr>`).join('');
    }catch(e){document.getElementById('tbody').innerHTML=`<tr><td colspan="6" class="text-center text-danger py-4">Lỗi: ${e.message}</td></tr>`;}
}

async function updateStatus(malenh, status){
    try{
        const res=await fetch(API+'/production-orders/'+malenh,{method:'PUT',headers:{...headers,'Content-Type':'application/json'},body:JSON.stringify({Trangthai:status})});
        const data=await res.json();
        if(data.success){showAlert('Cập nhật trạng thái thành công');load();}
        else showAlert(data.message,'danger');
    }catch(e){showAlert('Lỗi kết nối','danger');}
}

async function del(id){
    if(!confirm('Xóa lệnh sản xuất '+id+'?'))return;
    try{
        const res=await fetch(API+'/production-orders/'+id,{method:'DELETE',headers});
        const data=await res.json();
        if(data.success){showAlert('Đã xóa lệnh sản xuất');load();}
        else showAlert(data.message,'danger');
    }catch(e){showAlert('Lỗi kết nối','danger');}
}

async function openComplete(malenh){
    currentMalenh=malenh;
    document.getElementById('htMalenh').textContent=malenh;
    // Load warehouses
    const sel=document.getElementById('htKho');
    if(sel.options.length<=1){
        const res=await fetch(API+'/warehouses',{headers});
        const data=await res.json();
        if(data.success)data.data.warehouses.forEach(k=>{sel.innerHTML+=`<option value="${k.Makho}">[${k.Makho}] ${k.Tenkho}</option>`;});
    }
    new bootstrap.Modal(document.getElementById('hoanThanhModal')).show();
}

async function confirmComplete() {
    // 1. Sửa ID từ selKho thành htKho cho đúng với HTML bên trên
    const selectKhoElement = document.getElementById('htKho');
    const makho = selectKhoElement.value;
    
    if (!makho) {
        alert("Vui lòng chọn kho nhập hàng!");
        return;
    }

    console.log("Đang gửi:", { Malenh: currentMalenh, Makho: makho });

    try {
        const res = await fetch(`${API}/complete-production`, {
            method: 'POST',
            // 2. Sử dụng biến headers đã khai báo ở đầu file JS
            headers: {
                ...headers,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                Malenh: currentMalenh, 
                Makho: makho           
            })
        });

        const data = await res.json();

        if (data.success) {
            alert("Thành công: " + data.message);
            
            // 3. Cách đóng Modal chuẩn của Bootstrap 5 khi chưa gán biến
            const modalElement = document.getElementById('hoanThanhModal');
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            modalInstance.hide();
            
            load(); // Gọi lại hàm load() để cập nhật danh sách
        } else {
            alert("Thất bại: " + data.message);
        }
    } catch (e) {
        console.error("Lỗi:", e);
        alert("Lỗi kết nối API. Kiểm tra Console (F12)!");
    }
}

load();
</script>
</body>
</html>