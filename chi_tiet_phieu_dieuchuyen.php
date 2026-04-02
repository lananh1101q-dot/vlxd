<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Chi tiết phiếu điều chuyển - VLXD</title>
    <script>const token=localStorage.getItem('token');if(!token)window.location.href='dangnhap.php';</script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body{background:#f0f4f8;font-family:'Segoe UI',sans-serif}
        .main-content{max-width:960px;margin:30px auto;padding:20px}
        .page-header{background:linear-gradient(135deg,#0f766e,#14b8a6);color:white;border-radius:12px;padding:24px 28px;margin-bottom:24px}
        .card{border:none;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08)}
        .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px}
        .info-item{background:#f0fdf4;border-radius:8px;padding:12px 16px;border:1px solid #bbf7d0}
        .info-label{font-size:.78rem;color:#047857;text-transform:uppercase;font-weight:700;margin-bottom:2px}
        .info-value{font-size:1rem;font-weight:600;color:#1e293b}
        .table th{background:#f0fdf4;color:#047857;font-size:.8rem;text-transform:uppercase;border-color:#bbf7d0}
        .arrow-box{background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:2px solid #86efac;border-radius:10px;padding:14px 20px;display:flex;align-items:center;gap:16px;margin-bottom:20px}
        .kho-badge{background:white;border:1px solid #bbf7d0;border-radius:8px;padding:8px 14px;font-weight:700;font-size:.9rem;color:#047857}
    </style>
</head>
<body>
<div class="main-content">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-1" style="font-size:1.5rem"><i class="fas fa-exchange-alt me-2"></i>Chi tiết phiếu điều chuyển</h1>
            <p class="mb-0 opacity-75" id="subTitle">Đang tải...</p>
        </div>
        <div class="d-flex gap-2">
            <a href="danh_sach_phieu_dieuchuyen.php" class="btn btn-light btn-sm"><i class="fas fa-arrow-left me-1"></i>Danh sách</a>
            <button class="btn btn-success btn-sm fw-semibold" id="btnThucHien" disabled onclick="executeTransfer()"><i class="fas fa-check me-1"></i>Thực hiện điều chuyển</button>
        </div>
    </div>

    <div id="alertBox" class="alert d-none mb-3"></div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="arrow-box" id="arrowBox">
                <span class="text-muted small">Đang tải...</span>
            </div>
            <div class="info-grid" id="infoGrid"></div>
            <h6 class="fw-bold text-success mb-3"><i class="fas fa-boxes-stacked me-1"></i>Sản phẩm điều chuyển</h6>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Mã SP</th><th>Tên sản phẩm</th><th>ĐVT</th><th class="text-end">Số lượng</th></tr></thead>
                    <tbody id="tbody"><tr><td colspan="4" class="text-center py-3 text-muted">Đang tải...</td></tr></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
const API='http://localhost:8000/api/v1';
const headers={'Authorization':'Bearer '+localStorage.getItem('token')};
const id=new URLSearchParams(location.search).get('id');
function fmtDate(s){if(!s)return'—';return new Date(s).toLocaleDateString('vi-VN');}
function showAlert(msg,type='success'){const a=document.getElementById('alertBox');a.className=`alert alert-${type}`;a.innerHTML=msg;a.classList.remove('d-none');}

async function load(){
    if(!id){showAlert('Không có mã phiếu điều chuyển','danger');return;}
    try{
        const res=await fetch(API+'/transfers/'+encodeURIComponent(id),{headers});
        const data=await res.json();
        if(!data.success)throw new Error(data.message);
        const r=data.data.transfer;
        document.getElementById('subTitle').textContent='Phiếu: '+r.Madieuchuyen;
        document.getElementById('arrowBox').innerHTML=`
            <div class="kho-badge"><i class="fas fa-store-alt me-1"></i>${r.TenKhoXuat||r.Khoxuat}</div>
            <div class="text-center flex-grow-1"><i class="fas fa-arrow-right fa-2x text-success"></i><div class="small text-muted mt-1">${fmtDate(r.Ngaydieuchuyen)}</div></div>
            <div class="kho-badge"><i class="fas fa-store me-1"></i>${r.TenKhoNhap||r.Khonhap}</div>`;
        document.getElementById('infoGrid').innerHTML=`
            <div class="info-item"><div class="info-label">Mã phiếu</div><div class="info-value">${r.Madieuchuyen}</div></div>
            <div class="info-item"><div class="info-label">Ngày điều chuyển</div><div class="info-value">${fmtDate(r.Ngaydieuchuyen)}</div></div>
            <div class="info-item"><div class="info-label">Kho xuất</div><div class="info-value">${r.TenKhoXuat||r.Khoxuat}</div></div>
            <div class="info-item"><div class="info-label">Kho nhập</div><div class="info-value">${r.TenKhoNhap||r.Khonhap}</div></div>
            <div class="info-item" style="grid-column:1/-1"><div class="info-label">Ghi chú</div><div class="info-value">${r.Ghichu||'—'}</div></div>`;
        const details=r.details||[];
        if(!details.length){document.getElementById('tbody').innerHTML='<tr><td colspan="4" class="text-center text-muted py-3">Không có chi tiết</td></tr>';return;}
        document.getElementById('tbody').innerHTML=details.map(d=>`<tr>
            <td><code>${d.Masp}</code></td>
            <td>${d.Tensp||'—'}</td>
            <td>${d.Dvt||'—'}</td>
            <td class="text-end fw-bold">${Number(d.Soluong||0).toLocaleString('vi-VN')}</td>
        </tr>`).join('');
        if(r.Trangthai!=='da_thuc_hien'){document.getElementById('btnThucHien').disabled=false;}
        else{document.getElementById('btnThucHien').textContent='Đã thực hiện';document.getElementById('btnThucHien').className='btn btn-sm btn-secondary';}
    }catch(e){showAlert('Lỗi: '+e.message,'danger');}
}

async function executeTransfer(){
    if(!confirm('Xác nhận thực hiện điều chuyển?\nTồn kho sẽ được cập nhật tự động.'))return;
    try{
        const res=await fetch(API+'/transfers/'+encodeURIComponent(id)+'/execute',{method:'POST',headers:{...headers,'Content-Type':'application/json'}});
        const data=await res.json();
        if(data.success){showAlert('<strong>Điều chuyển thành công!</strong> Tồn kho đã được cập nhật.');document.getElementById('btnThucHien').disabled=true;document.getElementById('btnThucHien').textContent='Đã thực hiện';}
        else showAlert(data.message,'danger');
    }catch(e){showAlert('Lỗi kết nối','danger');}
}
load();
</script>
</body>
</html>