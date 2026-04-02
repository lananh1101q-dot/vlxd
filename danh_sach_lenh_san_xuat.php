<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Danh sách lệnh sản xuất - VLXD</title>
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
    <div class="text-center mb-3"><h4><i class="fas fa-warehouse me-2"></i>Quản Lý Kho</h4></div>
    <ul class="nav flex-column">
        <li><a class="nav-link" href="trangchu.php"><i class="fas fa-home me-2"></i>Trang Chủ</a></li>
        <li>
            <a class="nav-link" href="#" onclick="toggleMenu('menuSP');event.preventDefault()"><i class="fas fa-box me-2"></i>Quản lý sản phẩm<i class="fas fa-chevron-down float-end mt-1" style="font-size:.7rem"></i></a>
            <ul class="nav flex-column ms-3 submenu" id="menuSP">
                <li><a class="nav-link" href="Sanpham.php"><i class="fas fa-cube me-2"></i>Sản phẩm</a></li>
                <li><a class="nav-link" href="dmsp.php"><i class="fas fa-tags me-2"></i>Danh mục</a></li>
                <li><a class="nav-link" href="Nhacungcap.php"><i class="fas fa-truck me-2"></i>Nhà cung cấp</a></li>
                <li><a class="nav-link" href="Nguyenvatlieu.php"><i class="fas fa-layer-group me-2"></i>Nguyên vật liệu</a></li>
                <li><a class="nav-link" href="Congthucsanpham.php"><i class="fas fa-file-invoice me-2"></i>Công thức SP</a></li>
            </ul>
        </li>
        <li><a class="nav-link" href="danh_sach_phieu_nhap.php"><i class="fas fa-file-import me-2"></i>Phiếu nhập kho</a></li>
        <li><a class="nav-link" href="danh_sach_phieu_xuat.php"><i class="fas fa-file-export me-2"></i>Phiếu xuất</a></li>
        <li><a class="nav-link" href="danh_sach_phieu_dieuchuyen.php"><i class="fas fa-exchange-alt me-2"></i>Điều chuyển</a></li>
        <li><a class="nav-link" href="tonkho.php"><i class="fas fa-chart-bar me-2"></i>Báo cáo tồn kho</a></li>
        <li><a class="nav-link" href="khachhang.php"><i class="fas fa-users me-2"></i>Khách hàng</a></li>
        <li>
            <a class="nav-link" href="#" onclick="toggleMenu('menuSX');event.preventDefault()" style="background:rgba(124,58,237,.3);color:#ddd6fe!important"><i class="fas fa-cogs me-2"></i>Sản xuất<i class="fas fa-chevron-down float-end mt-1" style="font-size:.7rem"></i></a>
            <ul class="nav flex-column ms-3 submenu open" id="menuSX">
                <li><a class="nav-link" href="danh_sach_lenh_san_xuat.php" style="color:#ddd6fe!important"><i class="fas fa-list me-2"></i>Danh sách lệnh SX</a></li>
                <li><a class="nav-link" href="lenh_san_xuat.php"><i class="fas fa-plus-circle me-2"></i>Tạo lệnh SX</a></li>
            </ul>
        </li>
        <li><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
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
                    ${r.Trangthai!=='hoan_thanh'&&r.Trangthai!=='huy'?`
                    <button class="btn btn-sm btn-outline-success" onclick="openComplete('${r.Malenh}')" title="Hoàn thành & nhập kho"><i class="fas fa-check-circle"></i></button>
                    <button class="btn btn-sm btn-outline-warning" onclick="updateStatus('${r.Malenh}','dang_san_xuat')" title="Đánh dấu đang SX"><i class="fas fa-play"></i></button>
                    `:''}
                    <button class="btn btn-sm btn-outline-danger" onclick="del('${r.Malenh}')" title="Xóa"><i class="fas fa-trash"></i></button>
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

async function confirmComplete(){
    const makho=document.getElementById('htKho').value;
    if(!makho){alert('Vui lòng chọn kho');return;}
    try{
        const res=await fetch(API+'/complete-production',{method:'POST',headers:{...headers,'Content-Type':'application/json'},body:JSON.stringify({Malenh:currentMalenh,Makho:makho})});
        const data=await res.json();
        bootstrap.Modal.getInstance(document.getElementById('hoanThanhModal')).hide();
        if(data.success){showAlert('Sản xuất hoàn thành! Đã nhập thành phẩm vào kho.');load();}
        else showAlert(data.message,'danger');
    }catch(e){showAlert('Lỗi kết nối','danger');}
}

load();
</script>
</body>
</html>