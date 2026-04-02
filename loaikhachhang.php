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
    <div class="text-center mb-3"><h4><i class="fas fa-warehouse me-2"></i>Quản Lý Kho</h4></div>
    <ul class="nav flex-column">
        <li><a class="nav-link" href="trangchu.php"><i class="fas fa-home me-2"></i>Trang Chủ</a></li>
        <li><a class="nav-link" href="danh_sach_phieu_nhap.php"><i class="fas fa-file-import me-2"></i>Phiếu nhập kho</a></li>
        <li><a class="nav-link" href="danh_sach_phieu_xuat.php"><i class="fas fa-file-export me-2"></i>Phiếu xuất</a></li>
        <li><a class="nav-link" href="tonkho.php"><i class="fas fa-chart-bar me-2"></i>Báo cáo tồn kho</a></li>
        <li>
            <a class="nav-link" href="#" onclick="toggleMenu('menuKH');event.preventDefault()" style="background:rgba(6,182,212,.25);color:#a5f3fc!important"><i class="fas fa-users me-2"></i>Khách hàng<i class="fas fa-chevron-down float-end mt-1" style="font-size:.7rem"></i></a>
            <ul class="nav flex-column ms-3 submenu open" id="menuKH">
                <li><a class="nav-link" href="khachhang.php"><i class="fas fa-user me-2"></i>Khách hàng</a></li>
                <li><a class="nav-link" href="loaikhachhang.php" style="color:#a5f3fc!important"><i class="fas fa-users-cog me-2"></i>Loại khách hàng</a></li>
            </ul>
        </li>
        <li><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
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
            <div class="mb-3"><label class="form-label fw-semibold">Mã loại *</label><input class="form-control" id="fMa" placeholder="VD: LKH001" required></div>
            <div class="mb-3"><label class="form-label fw-semibold">Tên loại *</label><input class="form-control" id="fTen" placeholder="Tên loại khách hàng" required></div>
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

function openModal(){editId=null;document.getElementById('modalTitle').textContent='Thêm loại KH';document.getElementById('fMa').value='';document.getElementById('fMa').disabled=false;document.getElementById('fTen').value='';document.getElementById('fMota').value='';new bootstrap.Modal(document.getElementById('lkhModal')).show();}
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

    if(!body.Maloaikh || !body.Tenloaikh) return alert('Vui lòng nhập mã và tên loại!');

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