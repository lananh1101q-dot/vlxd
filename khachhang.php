<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Quản lý khách hàng - VLXD</title>
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
        .page-header{background:linear-gradient(135deg,#7c3aed,#a855f7);color:white;border-radius:12px;padding:24px 28px;margin-bottom:24px;box-shadow:0 4px 15px rgba(168,85,247,.3)}
        .card{border:none;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08)}
        .table th{background:#f8fafc;color:#475569;font-size:.8rem;text-transform:uppercase}
        .table td{vertical-align:middle}
        .modal-content{border-radius:12px;border:none}
        .btn-action{width:32px;height:32px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;font-size:.85rem}
    </style>
</head>
<body>
<nav class="sidebar">
    <div class="text-center mb-3"><h4><i class="fas fa-warehouse me-2"></i>Quản Lý Kho</h4></div>
    <ul class="nav flex-column">
        <li><a class="nav-link" href="trangchu.php"><i class="fas fa-home me-2"></i>Trang Chủ</a></li>
        <li>
            <a class="nav-link" href="#" onclick="toggleMenu('menuSP',this)"><i class="fas fa-box me-2"></i>Quản lý sản phẩm<i class="fas fa-chevron-down float-end mt-1" style="font-size:.7rem"></i></a>
            <ul class="nav flex-column ms-3 submenu" id="menuSP">
                <li><a class="nav-link" href="Sanpham.php"><i class="fas fa-cube me-2"></i>Sản phẩm</a></li>
                <li><a class="nav-link" href="dmsp.php"><i class="fas fa-tags me-2"></i>Danh mục</a></li>
                <li><a class="nav-link" href="Nhacungcap.php"><i class="fas fa-truck me-2"></i>Nhà cung cấp</a></li>
                <li><a class="nav-link" href="Nguyenvatlieu.php"><i class="fas fa-layer-group me-2"></i>Nguyên vật liệu</a></li>
            </ul>
        </li>
        <li>
            <a class="nav-link" href="#" onclick="toggleMenu('menuNhap',this)"><i class="fas fa-file-import me-2"></i>Phiếu nhập kho<i class="fas fa-chevron-down float-end mt-1" style="font-size:.7rem"></i></a>
            <ul class="nav flex-column ms-3 submenu" id="menuNhap">
                <li><a class="nav-link" href="danh_sach_phieu_nhap.php"><i class="fas fa-list me-2"></i>Danh sách</a></li>
                <li><a class="nav-link" href="phieu_nhap.php"><i class="fas fa-plus-circle me-2"></i>Tạo phiếu nhập</a></li>
            </ul>
        </li>
        <li>
            <a class="nav-link" href="#" onclick="toggleMenu('menuXuat',this)"><i class="fas fa-file-export me-2"></i>Phiếu xuất<i class="fas fa-chevron-down float-end mt-1" style="font-size:.7rem"></i></a>
            <ul class="nav flex-column ms-3 submenu" id="menuXuat">
                <li><a class="nav-link" href="danh_sach_phieu_xuat.php"><i class="fas fa-list me-2"></i>Danh sách</a></li>
                <li><a class="nav-link" href="phieu_xuat.php"><i class="fas fa-plus-circle me-2"></i>Tạo phiếu xuất</a></li>
            </ul>
        </li>
        <li><a class="nav-link" href="tonkho.php"><i class="fas fa-chart-bar me-2"></i>Báo cáo tồn kho</a></li>
        <li>
            <a class="nav-link" href="#" onclick="toggleMenu('menuKH',this)" style="background:rgba(168,85,247,.25);color:#e9d5ff!important"><i class="fas fa-users me-2"></i>Khách hàng<i class="fas fa-chevron-down float-end mt-1" style="font-size:.7rem"></i></a>
            <ul class="nav flex-column ms-3 submenu open" id="menuKH">
                <li><a class="nav-link" href="khachhang.php" style="color:#e9d5ff!important"><i class="fas fa-user me-2"></i>Khách hàng</a></li>
                <li><a class="nav-link" href="loaikhachhang.php"><i class="fas fa-users-cog me-2"></i>Loại khách hàng</a></li>
            </ul>
        </li>
        <li>
            <a class="nav-link" href="#" onclick="toggleMenu('menuSX',this)"><i class="fas fa-cogs me-2"></i>Sản xuất<i class="fas fa-chevron-down float-end mt-1" style="font-size:.7rem"></i></a>
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
            <h1 class="mb-1" style="font-size:1.6rem"><i class="fas fa-users me-2"></i>Quản lý khách hàng</h1>
            <p class="mb-0 opacity-75">Thêm, sửa, xóa thông tin khách hàng</p>
        </div>
        <button class="btn btn-light fw-semibold" onclick="openModal()"><i class="fas fa-plus me-2"></i>Thêm khách hàng</button>
    </div>

    <div id="alertBox" class="alert d-none mb-3"></div>

    <div class="card mb-4">
        <div class="card-body p-0">
            <div class="p-3"><input class="form-control" id="searchInput" placeholder="🔍 Tìm kiếm theo mã, tên, SĐT..." oninput="filterTable()"></div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr>
                        <th>Mã KH</th><th>Tên khách hàng</th><th>SĐT</th><th>Địa chỉ</th><th>Email</th><th>Loại KH</th><th class="text-center">Thao tác</th>
                    </tr></thead>
                    <tbody id="tbody"><tr><td colspan="7" class="text-center py-4 text-muted">Đang tải...</td></tr></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm/Sửa -->
<div class="modal fade" id="khModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header border-0 pb-0">
            <h5 class="modal-title fw-bold" id="modalTitle">Thêm khách hàng</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body pt-2">
            <div class="mb-3"><label class="form-label fw-semibold">Mã KH *</label>
                <input class="form-control" id="fMakh" placeholder="VD: KH001" required>
            </div>
            <div class="mb-3"><label class="form-label fw-semibold">Tên khách hàng *</label>
                <input class="form-control" id="fTenkh" placeholder="Tên đầy đủ" required>
            </div>
            <div class="row g-3">
                <div class="col-6 mb-3"><label class="form-label fw-semibold">SĐT</label>
                    <input class="form-control" id="fSdt" placeholder="0912345678"></div>
                <div class="col-6 mb-3"><label class="form-label fw-semibold">Loại KH</label>
                    <select class="form-select" id="fLoai"><option value="">-- Chọn loại --</option></select>
                </div>
            </div>
            <div class="mb-3"><label class="form-label fw-semibold">Email</label>
                <input class="form-control" id="fEmail" type="email" placeholder="email@example.com"></div>
            <div class="mb-3"><label class="form-label fw-semibold">Địa chỉ</label>
                <input class="form-control" id="fDiachi" placeholder="Địa chỉ"></div>
        </div>
        <div class="modal-footer border-0">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button class="btn btn-primary fw-semibold" onclick="saveCustomer()"><i class="fas fa-save me-1"></i>Lưu</button>
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

        async function load() {
            const headers = getHeaders();
            if (!headers) return window.location.href = 'dangnhap.php';
            try {
                const res = await fetch(API + '/customers', { headers });
                // ... rest of logic
            } catch(e) {}
        }

function toggleMenu(id){const m=document.getElementById(id);m.classList.toggle('open');event.preventDefault();}
function showAlert(msg,type='success'){const a=document.getElementById('alertBox');a.className=`alert alert-${type}`;a.textContent=msg;a.classList.remove('d-none');setTimeout(()=>a.classList.add('d-none'),4000);}
function filterTable(){const q=document.getElementById('searchInput').value.toLowerCase();document.querySelectorAll('#tbody tr').forEach(tr=>{tr.style.display=tr.textContent.toLowerCase().includes(q)?'':'none';});}

async function loadTypes(){
    const headers = getHeaders();
    if(!headers) return;
    const res=await fetch(API+'/customer-types',{headers});
    const data=await res.json();
    if(data.success){customerTypes=data.data.types;const sel=document.getElementById('fLoai');customerTypes.forEach(t=>{sel.innerHTML+=`<option value="${t.Maloaikh}">${t.Tenloaikh}</option>`;});}
}

async function loadCustomers(){
    try{
        const res=await fetch(API+'/customers',{headers});
        const data=await res.json();
        if(!data.success)throw new Error(data.message);
        const tb=document.getElementById('tbody');
        if(!data.data.customers.length){tb.innerHTML='<tr><td colspan="7" class="text-center py-4 text-muted">Chưa có khách hàng.</td></tr>';return;}
        tb.innerHTML=data.data.customers.map(r=>`<tr>
            <td><code class="fw-bold text-primary">${r.Makh}</code></td>
            <td><strong>${r.Tenkh}</strong></td>
            <td>${r.Sdtkh||''}</td>
            <td>${r.Diachikh||''}</td>
            <td>${r.Email||''}</td>
            <td><span class="badge bg-primary bg-opacity-10 text-primary">${r.Tenloaikh||r.Maloaikh||'—'}</span></td>
            <td class="text-center">
                <button class="btn btn-sm btn-outline-primary btn-action me-1" onclick="editCustomer('${r.Makh}','${r.Tenkh}','${r.Sdtkh||''}','${r.Diachikh||''}','${r.Email||''}','${r.Maloaikh||''}')"><i class="fas fa-edit"></i></button>
                <button class="btn btn-sm btn-outline-danger btn-action" onclick="deleteCustomer('${r.Makh}','${r.Tenkh}')"><i class="fas fa-trash"></i></button>
            </td>
        </tr>`).join('');
    }catch(e){document.getElementById('tbody').innerHTML=`<tr><td colspan="7" class="text-center text-danger py-4">Lỗi: ${e.message}</td></tr>`;}
}

function openModal(){
    editId=null;
    document.getElementById('modalTitle').textContent='Thêm khách hàng';
    document.getElementById('fMakh').value='';document.getElementById('fMakh').disabled=false;
    document.getElementById('fTenkh').value='';document.getElementById('fSdt').value='';
    document.getElementById('fEmail').value='';document.getElementById('fDiachi').value='';
    document.getElementById('fLoai').value='';
    new bootstrap.Modal(document.getElementById('khModal')).show();
}

function editCustomer(id,ten,sdt,diachi,email,loai){
    editId=id;
    document.getElementById('modalTitle').textContent='Sửa khách hàng';
    document.getElementById('fMakh').value=id;document.getElementById('fMakh').disabled=true;
    document.getElementById('fTenkh').value=ten;document.getElementById('fSdt').value=sdt;
    document.getElementById('fEmail').value=email;document.getElementById('fDiachi').value=diachi;
    document.getElementById('fLoai').value=loai;
    new bootstrap.Modal(document.getElementById('khModal')).show();
}

async function save() {
    const headers = getHeaders();
    if(!headers) return;
    const isEdit = editId !== null;
    const body = {
        Makh: document.getElementById('fMakh').value,
        Tenkh: document.getElementById('fTenkh').value,
        Sdtkh: document.getElementById('fSdt').value,
        Diachikh: document.getElementById('fDiachi').value,
        Email: document.getElementById('fEmail').value,
        Maloaikh: document.getElementById('fLoai').value
    };

    if(!body.Makh || !body.Tenkh) return alert('Vui lòng nhập mã và tên khách hàng!');

    try {
        const url = isEdit ? `${API}/customers/${body.Makh}` : `${API}/customers`;
        const method = isEdit ? 'PUT' : 'POST';
        
        const res = await fetch(url, {
            method,
            headers: { ...headers, 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        });
        const data = await res.json();
        
        if(data.success) {
            showAlert(isEdit ? 'Cập nhật thành công!' : 'Thêm mới thành công!');
            bootstrap.Modal.getInstance(document.getElementById('khModal')).hide();
            loadCustomers();
        } else {
            alert('Lỗi: ' + data.message);
        }
    } catch(e) {
        alert('Lỗi kết nối API');
    }
}

async function deleteCustomer(id) {
    const headers = getHeaders();
    if(!headers) return;
    if(!confirm(`Xác nhận xóa khách hàng ${id}?`)) return;
    try {
        const res = await fetch(`${API}/customers/${id}`, { method: 'DELETE', headers });
        const data = await res.json();
        if(data.success) { showAlert('Đã xóa khách hàng'); loadCustomers(); }
        else alert(data.message);
    } catch(e) { alert('Lỗi kết nối'); }
}

loadTypes();
loadCustomers();
</script>
</body>
</html>