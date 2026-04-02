<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Hoàn thành lệnh sản xuất - VLXD</title>
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
        .info-item{background:#f5f3ff;border-radius:8px;padding:12px 16px;border:1px solid #ddd6fe;margin-bottom:12px}
        .info-label{font-size:.75rem;color:#7c3aed;text-transform:uppercase;font-weight:700;margin-bottom:2px}
        .info-value{font-size:1.05rem;font-weight:700;color:#1e293b}
        .nvl-row{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:12px 16px;margin-bottom:8px;display:flex;justify-content:space-between;align-items:center}
        .nvl-need{background:#7c3aed;color:white;border-radius:6px;padding:4px 10px;font-size:.82rem;font-weight:700}
        .status-badge{padding:4px 12px;border-radius:20px;font-size:.76rem;font-weight:700}
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
            <a class="nav-link" href="#" onclick="toggleMenu('menuSX');event.preventDefault()" style="background:rgba(124,58,237,.3);color:#ddd6fe!important"><i class="fas fa-cogs me-2"></i>Sản xuất<i class="fas fa-chevron-down float-end mt-1" style="font-size:.7rem"></i></a>
            <ul class="nav flex-column ms-3 submenu open" id="menuSX">
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
            <h1 class="mb-1" style="font-size:1.5rem"><i class="fas fa-industry me-2"></i>Hoàn thành lệnh sản xuất</h1>
            <p class="mb-0 opacity-75" id="subTitle">Đang tải...</p>
        </div>
        <a href="danh_sach_lenh_san_xuat.php" class="btn btn-light btn-sm"><i class="fas fa-arrow-left me-1"></i>Danh sách</a>
    </div>

    <div id="alertBox" class="alert d-none mb-3"></div>

    <div id="mainContent" class="d-none">
        <div class="row g-4">
            <div class="col-md-5">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="fw-bold text-primary mb-3"><i class="fas fa-clipboard me-1"></i>Thông tin lệnh sản xuất</h6>
                        <div id="lenhInfo"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="fw-bold text-success mb-3"><i class="fas fa-flask me-1"></i>Nguyên vật liệu cần xuất kho</h6>
                        <div id="nvlList"><p class="text-muted">Đang tải công thức...</p></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4" id="khoForm">
            <div class="card-body">
                <h6 class="fw-bold text-purple mb-3" style="color:#7c3aed"><i class="fas fa-warehouse me-1"></i>Chọn kho thực hiện sản xuất</h6>
                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Kho xuất NVL & nhập thành phẩm *</label>
                        <select class="form-select" id="selKho"><option value="">-- Chọn kho --</option></select>
                        <div class="form-text">NVL sẽ được trừ, thành phẩm sẽ được nhập vào kho này</div>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-success w-100 fw-semibold" id="btnHoanThanh" onclick="completeProduction()">
                            <i class="fas fa-check-circle me-2"></i>Hoàn thành sản xuất
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const API='http://localhost:8000/api/v1';
const headers={'Authorization':'Bearer '+localStorage.getItem('token')};
const id=new URLSearchParams(location.search).get('id');
let lenhData=null;

function toggleMenu(i){document.getElementById(i).classList.toggle('open');}
function showAlert(msg,type='success'){const a=document.getElementById('alertBox');a.className=`alert alert-${type}`;a.innerHTML=msg;a.classList.remove('d-none');}

async function load(){
    if(!id){showAlert('Không có mã lệnh sản xuất','danger');return;}
    try{
        const [rLenh,rKho]=await Promise.all([
            fetch(API+'/production-orders/'+encodeURIComponent(id),{headers}),
            fetch(API+'/warehouses',{headers})
        ]);
        const [dLenh,dKho]=await Promise.all([rLenh.json(),rKho.json()]);
        if(!dLenh.success)throw new Error(dLenh.message||'Không tìm thấy lệnh sản xuất');
        // Manufacturing service trả data trực tiếp (không bọc trong .order)
        lenhData = dLenh.data && dLenh.data.order ? dLenh.data.order : dLenh.data;
        if(!lenhData || !lenhData.Malenh) throw new Error('Không tìm thấy lệnh sản xuất: '+id);

        document.getElementById('subTitle').textContent='Lệnh: '+lenhData.Malenh;
        const statusMap={cho_xu_ly:'Chờ xử lý',dang_san_xuat:'Đang sản xuất',hoan_thanh:'Hoàn thành',huy:'Đã hủy'};
        const statusColor={cho_xu_ly:'warning',dang_san_xuat:'primary',hoan_thanh:'success',huy:'danger'};
        const st=lenhData.Trangthai||'cho_xu_ly';
        document.getElementById('lenhInfo').innerHTML=`
            <div class="info-item"><div class="info-label">Mã lệnh</div><div class="info-value">${lenhData.Malenh}</div></div>
            <div class="info-item"><div class="info-label">Sản phẩm</div><div class="info-value">${lenhData.Tensp||lenhData.Masp}</div></div>
            <div class="info-item"><div class="info-label">Số lượng sản xuất</div><div class="info-value text-purple" style="color:#7c3aed;font-size:1.4rem">${Number(lenhData.Soluongsanxuat||0).toLocaleString('vi-VN')}</div></div>
            <div class="info-item"><div class="info-label">Trạng thái</div><div class="info-value"><span class="status-badge bg-${statusColor[st]||'secondary'} bg-opacity-15 text-${statusColor[st]||'secondary'}">${statusMap[st]||st}</span></div></div>`;

        // Load formulas
        const rF=await fetch(API+'/formulas?Masp='+lenhData.Masp,{headers});
        const dF=await rF.json();
        if(dF.success&&dF.data.formulas.length){
            const sl=lenhData.Soluongsanxuat||1;
            document.getElementById('nvlList').innerHTML=dF.data.formulas.map(f=>`
                <div class="nvl-row">
                    <div><strong>${f.Tennvl||f.Manvl}</strong><br><small class="text-muted">${f.Manvl} — ${f.Dvt||''}</small></div>
                    <span class="nvl-need">${Number(f.Soluong||0).toLocaleString('vi-VN')} × ${sl} = <strong>${Number((f.Soluong||0)*sl).toLocaleString('vi-VN')}</strong></span>
                </div>`).join('');
        }else{document.getElementById('nvlList').innerHTML='<p class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Chưa có công thức sản phẩm!</p>';}

        // Load warehouses
        if(dKho.success)dKho.data.warehouses.forEach(k=>{
            document.getElementById('selKho').innerHTML+=`<option value="${k.Makho}">[${k.Makho}] ${k.Tenkho}</option>`;
        });

        if(st==='hoan_thanh'){
            document.getElementById('khoForm').innerHTML='<div class="card-body"><div class="alert alert-success mb-0"><i class="fas fa-check-circle me-2"></i>Lệnh sản xuất này đã được hoàn thành rồi.</div></div>';
        }
        document.getElementById('mainContent').classList.remove('d-none');
    }catch(e){showAlert('Lỗi: '+e.message,'danger');}
}

async function completeProduction(){
    const makho=document.getElementById('selKho').value;
    if(!makho){alert('Vui lòng chọn kho!');return;}
    if(!confirm('Xác nhận hoàn thành sản xuất?\n• NVL sẽ bị trừ khỏi kho\n• Thành phẩm sẽ được nhập vào kho\n• Lệnh sẽ được đánh dấu Hoàn thành'))return;

    document.getElementById('btnHoanThanh').disabled=true;
    document.getElementById('btnHoanThanh').innerHTML='<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';

    try{
        const res=await fetch(API+'/complete-production',{
            method:'POST',
            headers:{...headers,'Content-Type':'application/json'},
            body:JSON.stringify({Malenh:id,Makho:makho})
        });
        const data=await res.json();
        if(data.success){
            document.getElementById('khoForm').innerHTML='';
            showAlert(`<strong><i class="fas fa-check-circle me-1"></i>Hoàn thành sản xuất thành công!</strong><br>Thành phẩm đã được nhập kho. <a href="danh_sach_lenh_san_xuat.php" class="btn btn-success btn-sm ms-2">← Danh sách</a>`);
            // Reload to show updated status
            setTimeout(load,1500);
        }else{
            showAlert(data.message||'Có lỗi xảy ra','danger');
            document.getElementById('btnHoanThanh').disabled=false;
            document.getElementById('btnHoanThanh').innerHTML='<i class="fas fa-check-circle me-2"></i>Hoàn thành sản xuất';
        }
    }catch(e){
        showAlert('Lỗi kết nối server','danger');
        document.getElementById('btnHoanThanh').disabled=false;
        document.getElementById('btnHoanThanh').innerHTML='<i class="fas fa-check-circle me-2"></i>Hoàn thành sản xuất';
    }
}

load();
</script>
</body>
</html>