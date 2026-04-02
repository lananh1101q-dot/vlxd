<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Tạo lệnh sản xuất - VLXD</title>
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
        .formula-list{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:12px}
        .formula-item{display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid #d1fae5}
        .formula-item:last-child{border-bottom:none}
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
        <li><a class="nav-link" href="khachhang.php"><i class="fas fa-users me-2"></i>Khách hàng</a></li>
        <li>
            <a class="nav-link" href="#" onclick="toggleMenu('menuSX');event.preventDefault()" style="background:rgba(124,58,237,.3);color:#ddd6fe!important"><i class="fas fa-cogs me-2"></i>Sản xuất<i class="fas fa-chevron-down float-end mt-1" style="font-size:.7rem"></i></a>
            <ul class="nav flex-column ms-3 submenu open" id="menuSX">
                <li><a class="nav-link" href="danh_sach_lenh_san_xuat.php"><i class="fas fa-list me-2"></i>Danh sách lệnh SX</a></li>
                <li><a class="nav-link" href="lenh_san_xuat.php" style="color:#ddd6fe!important"><i class="fas fa-plus-circle me-2"></i>Tạo lệnh SX</a></li>
            </ul>
        </li>
        <li><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
    </ul>
</nav>

<div class="main-content">
    <div class="page-header">
        <h1 class="mb-1" style="font-size:1.6rem"><i class="fas fa-plus-circle me-2"></i>Tạo lệnh sản xuất</h1>
        <p class="mb-0 opacity-75">Lên kế hoạch sản xuất thành phẩm</p>
    </div>

    <div id="alertBox" class="alert d-none mb-3"></div>

    <div class="row g-4">
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-4"><i class="fas fa-clipboard-list me-1"></i>Thông tin lệnh sản xuất</h6>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mã lệnh sản xuất</label>
                        <input class="form-control" id="fMalenh" placeholder="Để trống sẽ tự tạo">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Sản phẩm cần sản xuất *</label>
                        <select class="form-select" id="fMasp" onchange="loadFormula()"><option value="">-- Chọn sản phẩm --</option></select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Số lượng SX *</label>
                            <input type="number" class="form-control" id="fSlsx" min="1" placeholder="Nhập số lượng">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Ngày sản xuất *</label>
                            <input type="date" class="form-control" id="fNgaysx">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Ngày bắt đầu</label>
                            <input type="date" class="form-control" id="fNgaybd">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Ngày kết thúc dự kiến</label>
                            <input type="date" class="form-control" id="fNgaykt">
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button class="btn btn-primary fw-semibold px-4" onclick="submit()"><i class="fas fa-save me-2"></i>Tạo lệnh sản xuất</button>
                        <a href="danh_sach_lenh_san_xuat.php" class="btn btn-secondary">← Quay lại</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <h6 class="fw-bold text-success mb-3"><i class="fas fa-flask me-1"></i>Công thức sản xuất</h6>
                    <div id="formulaBox">
                        <p class="text-muted text-center py-4"><i class="fas fa-arrow-left me-1"></i>Chọn sản phẩm để xem công thức</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const API='http://localhost:8000/api/v1';
const headers={'Authorization':'Bearer '+localStorage.getItem('token')};

function toggleMenu(id){const m=document.getElementById(id);m.classList.toggle('open');}
function showAlert(msg,type='success'){const a=document.getElementById('alertBox');a.className=`alert alert-${type}`;a.innerHTML=msg;a.classList.remove('d-none');if(type==='success')setTimeout(()=>a.classList.add('d-none'),4000);}

async function loadProducts(){
    const res=await fetch(API+'/products',{headers});
    const data=await res.json();
    if(data.success)data.data.products.forEach(p=>{
        document.getElementById('fMasp').innerHTML+=`<option value="${p.Masp}">${p.Tensp} [${p.Masp}]</option>`;
    });
}

async function loadFormula(){
    const masp=document.getElementById('fMasp').value;
    if(!masp){document.getElementById('formulaBox').innerHTML='<p class="text-muted text-center py-4">Chọn sản phẩm để xem công thức</p>';return;}
    document.getElementById('formulaBox').innerHTML='<p class="text-muted">Đang tải...</p>';
    try{
        const res=await fetch(API+'/formulas?Masp='+masp,{headers});
        const data=await res.json();
        if(!data.success||!data.data.formulas.length){document.getElementById('formulaBox').innerHTML='<p class="text-muted text-center py-3"><i class="fas fa-exclamation-triangle me-1 text-warning"></i>Chưa có công thức</p>';return;}
        const sl=parseInt(document.getElementById('fSlsx').value)||1;
        document.getElementById('formulaBox').innerHTML=`<div class="formula-list">${data.data.formulas.map(f=>`
            <div class="formula-item">
                <div><strong>${f.Tennvl||f.Manvl}</strong><br><small class="text-muted">${f.Manvl}</small></div>
                <div class="text-end"><span class="badge bg-success">${f.Soluong} ${f.Dvt||''} × ${sl} = <strong>${(f.Soluong*sl).toLocaleString('vi-VN')}</strong></span></div>
            </div>`).join('')}</div>`;
    }catch(e){document.getElementById('formulaBox').innerHTML='<p class="text-danger">Lỗi: '+e.message+'</p>';}
}

document.getElementById('fSlsx').addEventListener('input',()=>{if(document.getElementById('fMasp').value)loadFormula();});

async function submit(){
    const h=getHeaders();if(!h)return;
    const masp=document.getElementById('fMasp').value;
    const sl=document.getElementById('fSlsx').value;
    const ngay=document.getElementById('fNgaysx').value;
    if(!masp||!sl||!ngay){showAlert('Vui lòng chọn sản phẩm, số lượng và ngày sản xuất','warning');return;}
    const payload={
        Malenh:document.getElementById('fMalenh').value||undefined,
        Masp:masp, Soluongsanxuat:parseInt(sl), Ngaysanxuat:ngay,
        Trangthai:'cho_xu_ly',
        Ngaybatdau:document.getElementById('fNgaybd').value||null,
        Ngayketthuc:document.getElementById('fNgaykt').value||null
    };
    try{
        const res=await fetch(API+'/production-orders',{method:'POST',headers:{...h,'Content-Type':'application/json'},body:JSON.stringify(payload)});
        const data=await res.json();
        if(data.success){showAlert(`<strong>Tạo lệnh sản xuất thành công!</strong> Mã: <code>${data.data.id}</code> &nbsp; <a href="danh_sach_lenh_san_xuat.php" class="btn btn-sm btn-primary">Xem danh sách</a>`);}
        else showAlert(data.message,'danger');
    }catch(e){showAlert('Lỗi kết nối','danger');}
}

document.getElementById('fNgaysx').valueAsDate=new Date();
loadProducts();
</script>
</body>
</html>