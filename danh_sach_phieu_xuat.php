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
            </ul>
        </li>
        <li><a class="nav-link" href="danh_sach_phieu_nhap.php"><i class="fas fa-file-import me-2"></i>Phiếu nhập kho</a></li>
        <li>
            <a class="nav-link" href="#" onclick="toggleMenu('menuXuat');event.preventDefault()" style="background:rgba(234,88,12,.3);color:#fed7aa!important"><i class="fas fa-file-export me-2"></i>Phiếu xuất<i class="fas fa-chevron-down float-end mt-1" style="font-size:.7rem"></i></a>
            <ul class="nav flex-column ms-3 submenu open" id="menuXuat">
                <li><a class="nav-link" href="danh_sach_phieu_xuat.php" style="color:#fed7aa!important"><i class="fas fa-list me-2"></i>Danh sách</a></li>
                <li><a class="nav-link" href="phieu_xuat.php"><i class="fas fa-plus-circle me-2"></i>Tạo phiếu xuất</a></li>
            </ul>
        </li>
        <li>
            <a class="nav-link" href="#" onclick="toggleMenu('menuDC');event.preventDefault()"><i class="fas fa-exchange-alt me-2"></i>Điều chuyển<i class="fas fa-chevron-down float-end mt-1" style="font-size:.7rem"></i></a>
            <ul class="nav flex-column ms-3 submenu" id="menuDC">
                <li><a class="nav-link" href="danh_sach_phieu_dieuchuyen.php"><i class="fas fa-list me-2"></i>Danh sách</a></li>
                <li><a class="nav-link" href="phieu_dieuchuyen.php"><i class="fas fa-plus-circle me-2"></i>Tạo phiếu</a></li>
            </ul>
        </li>
        <li><a class="nav-link" href="tonkho.php"><i class="fas fa-chart-bar me-2"></i>Báo cáo tồn kho</a></li>
        <li>
            <a class="nav-link" href="#" onclick="toggleMenu('menuKH');event.preventDefault()"><i class="fas fa-users me-2"></i>Khách hàng<i class="fas fa-chevron-down float-end mt-1" style="font-size:.7rem"></i></a>
            <ul class="nav flex-column ms-3 submenu" id="menuKH">
                <li><a class="nav-link" href="khachhang.php"><i class="fas fa-user me-2"></i>Khách hàng</a></li>
                <li><a class="nav-link" href="loaikhachhang.php"><i class="fas fa-users-cog me-2"></i>Loại khách hàng</a></li>
            </ul>
        </li>
        <li>
            <a class="nav-link" href="#" onclick="toggleMenu('menuSX');event.preventDefault()"><i class="fas fa-cogs me-2"></i>Sản xuất<i class="fas fa-chevron-down float-end mt-1" style="font-size:.7rem"></i></a>
            <ul class="nav flex-column ms-3 submenu" id="menuSX">
                <li><a class="nav-link" href="danh_sach_lenh_san_xuat.php"><i class="fas fa-list me-2"></i>Danh sách lệnh SX</a></li>
                <li><a class="nav-link" href="lenh_san_xuat.php"><i class="fas fa-plus-circle me-2"></i>Tạo lệnh SX</a></li>
            </ul>
        </li>
        <li><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
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