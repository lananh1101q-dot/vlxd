<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Tạo phiếu xuất kho - VLXD</title>
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
        .card-header-custom{background:linear-gradient(135deg,#fff7ed,#ffedd5);border-bottom:1px solid #fed7aa;padding:16px 20px;font-weight:700;color:#92400e}
        .detail-row{background:#fff;border-radius:8px;border:1px solid #e2e8f0;padding:12px;margin-bottom:8px}
        .total-bar{background:linear-gradient(135deg,#ecfdf5,#d1fae5);border-radius:8px;padding:16px;margin-top:16px}
    </style>
</head>
<body>
<nav class="sidebar">
    <div class="text-center mb-3"><h4><i class="fas fa-warehouse me-2"></i>Quản Lý Kho</h4></div>
    <ul class="nav flex-column">
        <li><a class="nav-link" href="trangchu.php"><i class="fas fa-home me-2"></i>Trang Chủ</a></li>
        <li><a class="nav-link" href="danh_sach_phieu_nhap.php"><i class="fas fa-file-import me-2"></i>Phiếu nhập kho</a></li>
        <li>
            <a class="nav-link" href="#" onclick="toggleMenu('menuXuat');event.preventDefault()" style="background:rgba(234,88,12,.3);color:#fed7aa!important"><i class="fas fa-file-export me-2"></i>Phiếu xuất<i class="fas fa-chevron-down float-end mt-1" style="font-size:.7rem"></i></a>
            <ul class="nav flex-column ms-3 submenu open" id="menuXuat">
                <li><a class="nav-link" href="danh_sach_phieu_xuat.php"><i class="fas fa-list me-2"></i>Danh sách</a></li>
                <li><a class="nav-link" href="phieu_xuat.php" style="color:#fed7aa!important"><i class="fas fa-plus-circle me-2"></i>Tạo phiếu xuất</a></li>
            </ul>
        </li>
        <li><a class="nav-link" href="danh_sach_phieu_dieuchuyen.php"><i class="fas fa-exchange-alt me-2"></i>Điều chuyển</a></li>
        <li><a class="nav-link" href="tonkho.php"><i class="fas fa-chart-bar me-2"></i>Báo cáo tồn kho</a></li>
        <li><a class="nav-link" href="khachhang.php"><i class="fas fa-users me-2"></i>Khách hàng</a></li>
        <li><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
    </ul>
</nav>

<div class="main-content">
    <div class="page-header">
        <h1 class="mb-1" style="font-size:1.6rem"><i class="fas fa-file-export me-2"></i>Tạo phiếu xuất kho</h1>
        <p class="mb-0 opacity-75">Xuất thành phẩm cho khách hàng và cập nhật tồn kho</p>
    </div>

    <div id="alertBox" class="alert d-none mb-3"></div>

    <div class="card">
        <div class="card-header-custom">Thông tin phiếu xuất</div>
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Mã xuất hàng</label>
                    <input class="form-control" id="fMa" placeholder="Để trống sẽ tự tạo">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Khách hàng *</label>
                    <select class="form-select" id="fKh"><option value="">-- Chọn KH --</option></select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kho xuất *</label>
                    <select class="form-select" id="fKho"><option value="">-- Chọn kho --</option></select>
                </div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Ngày xuất *</label>
                    <input type="date" class="form-control" id="fNgay">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Ghi chú</label>
                    <input class="form-control" id="fGhichu" placeholder="Ghi chú (tuỳ chọn)">
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-warning mb-0"><i class="fas fa-list-ul me-1"></i>Chi tiết sản phẩm xuất</h6>
                <button class="btn btn-sm btn-outline-warning" onclick="addRow()"><i class="fas fa-plus me-1"></i>Thêm dòng</button>
            </div>

            <div id="detailContainer"></div>

            <div class="total-bar d-flex justify-content-between align-items-center">
                <span class="fw-bold text-success">Tổng tiền:</span>
                <span class="fw-bold fs-5 text-success" id="totalAmount">0 đ</span>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button class="btn btn-warning fw-semibold px-4 text-dark" onclick="submitReceipt()"><i class="fas fa-save me-2"></i>Lưu phiếu xuất</button>
                <a href="danh_sach_phieu_xuat.php" class="btn btn-secondary">← Quay lại</a>
            </div>
        </div>
    </div>
</div>

<script>
const API='http://localhost:8000/api/v1';
const headers={'Authorization':'Bearer '+localStorage.getItem('token')};
let productsData=[];

function toggleMenu(id){const m=document.getElementById(id);m.classList.toggle('open');}
function showAlert(msg,type='success'){const a=document.getElementById('alertBox');a.className=`alert alert-${type}`;a.innerHTML=msg;a.classList.remove('d-none');if(type==='success')setTimeout(()=>a.classList.add('d-none'),4000);}
function fmtMoney(n){return Number(n||0).toLocaleString('vi-VN')+' đ';}
function getSPOptions(){return '<option value="">-- Chọn SP --</option>'+productsData.map(p=>`<option value="${p.Masp}">${p.Tensp} (${p.Dvt||''})</option>`).join('');}

function calcTotal(){
    let total=0;
    document.querySelectorAll('.detail-row').forEach(row=>{
        total+=parseFloat(row.querySelector('.inp-sl').value||0)*parseFloat(row.querySelector('.inp-dg').value||0);
    });
    document.getElementById('totalAmount').textContent=fmtMoney(total);
}

function addRow(){
    const div=document.createElement('div');div.className='detail-row';
    div.innerHTML=`
        <div class="row g-2 align-items-end">
            <div class="col-md-5"><label class="form-label small fw-semibold mb-1">Sản phẩm</label>
                <select class="form-select form-select-sm inp-mat">${getSPOptions()}</select>
            </div>
            <div class="col-md-2"><label class="form-label small fw-semibold mb-1">Số lượng</label>
                <input type="number" min="1" class="form-control form-control-sm inp-sl" placeholder="0" oninput="calcTotal()"></div>
            <div class="col-md-3"><label class="form-label small fw-semibold mb-1">Đơn giá (đ)</label>
                <input type="number" min="0" class="form-control form-control-sm inp-dg" placeholder="0" oninput="calcTotal()"></div>
            <div class="col-md-2"><button class="btn btn-sm btn-outline-danger w-100 mt-3" onclick="removeRow(this)"><i class="fas fa-trash"></i> Xóa</button></div>
        </div>`;
    document.getElementById('detailContainer').appendChild(div);
}

function removeRow(btn){btn.closest('.detail-row').remove();calcTotal();if(!document.querySelectorAll('.detail-row').length)addRow();}

async function loadDropdowns(){
    try{
        const [resKH,resKho,resSP]=await Promise.all([
            fetch(API+'/customers',{headers}),
            fetch(API+'/warehouses',{headers}),
            fetch(API+'/products',{headers})
        ]);
        const [dKH,dKho,dSP]=await Promise.all([resKH.json(),resKho.json(),resSP.json()]);
        if(dKH.success)dKH.data.customers.forEach(k=>{document.getElementById('fKh').innerHTML+=`<option value="${k.Makh}">${k.Tenkh}</option>`;});
        if(dKho.success)dKho.data.warehouses.forEach(k=>{document.getElementById('fKho').innerHTML+=`<option value="${k.Makho}">[${k.Makho}] ${k.Tenkho}</option>`;});
        if(dSP.success){productsData=dSP.data.products;document.querySelectorAll('.inp-mat').forEach(s=>s.innerHTML=getSPOptions());}
    }catch(e){showAlert('Lỗi tải dữ liệu: '+e.message,'danger');}
}

async function submitReceipt(){
    const makh=document.getElementById('fKh').value;
    const makho=document.getElementById('fKho').value;
    const ngay=document.getElementById('fNgay').value;
    if(!makh||!makho||!ngay){showAlert('Vui lòng chọn Khách hàng, Kho và Ngày xuất','warning');return;}
    const details=[];
    document.querySelectorAll('.detail-row').forEach(row=>{
        const masp=row.querySelector('.inp-mat').value;
        const sl=parseFloat(row.querySelector('.inp-sl').value||0);
        const dg=parseFloat(row.querySelector('.inp-dg').value||0);
        if(masp&&sl>0)details.push({Masp:masp,Soluong:sl,Dongiaxuat:dg});
    });
    if(!details.length){showAlert('Vui lòng thêm ít nhất một sản phẩm','warning');return;}
    const payload={Maxuathang:document.getElementById('fMa').value||undefined,Makh:makh,Makho:makho,Ngayxuat:ngay,Ghichu:document.getElementById('fGhichu').value,details};
    try{
        const res=await fetch(API+'/export-receipts',{method:'POST',headers:{...headers,'Content-Type':'application/json'},body:JSON.stringify(payload)});
        const data=await res.json();
        if(data.success){showAlert(`<strong>Tạo phiếu xuất thành công!</strong> Mã: <code>${data.data.id}</code> &nbsp; <a href="danh_sach_phieu_xuat.php" class="btn btn-sm btn-warning">← Về danh sách</a>`);document.getElementById('fMa').value='';document.getElementById('fGhichu').value='';document.getElementById('detailContainer').innerHTML='';addRow();calcTotal();}
        else showAlert(data.message,'danger');
    }catch(e){showAlert('Lỗi kết nối server','danger');}
}

document.getElementById('fNgay').valueAsDate=new Date();
loadDropdowns();
addRow();
</script>
</body>
</html>
