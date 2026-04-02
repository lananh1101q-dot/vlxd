<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Chi tiết phiếu xuất - VLXD</title>
    <script>const token=localStorage.getItem('token');if(!token)window.location.href='dangnhap.php';</script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body{background:#f0f4f8;font-family:'Segoe UI',sans-serif}
        .main-content{max-width:900px;margin:30px auto;padding:20px}
        .page-header{background:linear-gradient(135deg,#7c2d12,#ea580c);color:white;border-radius:12px;padding:24px 28px;margin-bottom:24px}
        .card{border:none;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08)}
        .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px}
        .info-item{background:#f8fafc;border-radius:8px;padding:12px 16px}
        .info-label{font-size:.78rem;color:#64748b;text-transform:uppercase;font-weight:600;margin-bottom:2px}
        .info-value{font-size:1rem;font-weight:600;color:#1e293b}
        .table th{background:#f8fafc;color:#475569;font-size:.8rem;text-transform:uppercase}
    </style>
</head>
<body>
<div class="main-content">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-1" style="font-size:1.5rem"><i class="fas fa-file-export me-2"></i>Chi tiết phiếu xuất kho</h1>
            <p class="mb-0 opacity-75" id="subTitle">Đang tải...</p>
        </div>
        <a href="danh_sach_phieu_xuat.php" class="btn btn-light"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
    </div>

    <div id="alertBox" class="alert alert-danger d-none"></div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="info-grid" id="infoGrid"><p class="text-muted">Đang tải...</p></div>
            <h6 class="fw-bold text-warning mb-3"><i class="fas fa-list-ul me-1"></i>Chi tiết sản phẩm xuất</h6>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Mã SP</th><th>Tên sản phẩm</th><th>ĐVT</th><th class="text-end">Số lượng</th><th class="text-end">Đơn giá</th><th class="text-end">Thành tiền</th></tr></thead>
                    <tbody id="tbody"><tr><td colspan="6" class="text-center py-3 text-muted">Đang tải...</td></tr></tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <div class="bg-warning bg-opacity-10 border border-warning rounded px-4 py-2 text-dark fw-bold">
                    Tổng tiền: <span class="text-success" id="tongTien">—</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const API='http://localhost:8000/api/v1';
const headers={'Authorization':'Bearer '+localStorage.getItem('token')};
const id=new URLSearchParams(location.search).get('id');
function fmtDate(s){if(!s)return'—';return new Date(s).toLocaleDateString('vi-VN');}
function fmtMoney(n){return Number(n||0).toLocaleString('vi-VN')+' đ';}

async function load(){
    if(!id){document.getElementById('alertBox').textContent='Không có mã phiếu';document.getElementById('alertBox').classList.remove('d-none');return;}
    try{
        const res=await fetch(API+'/export-receipts/'+encodeURIComponent(id),{headers});
        const data=await res.json();
        if(!data.success)throw new Error(data.message);
        const r=data.data.receipt;
        document.getElementById('subTitle').textContent='Phiếu: '+r.Maxuathang;
        document.getElementById('infoGrid').innerHTML=`
            <div class="info-item"><div class="info-label">Mã phiếu xuất</div><div class="info-value">${r.Maxuathang}</div></div>
            <div class="info-item"><div class="info-label">Khách hàng</div><div class="info-value">${r.Tenkh||r.Makh||'—'}</div></div>
            <div class="info-item"><div class="info-label">Kho xuất</div><div class="info-value">${r.Tenkho||r.Makho||'—'}</div></div>
            <div class="info-item"><div class="info-label">Ngày xuất</div><div class="info-value">${fmtDate(r.Ngayxuat)}</div></div>
            <div class="info-item" style="grid-column:1/-1"><div class="info-label">Ghi chú</div><div class="info-value">${r.Ghichu||'—'}</div></div>`;
        const details=r.details||[];
        if(!details.length){document.getElementById('tbody').innerHTML='<tr><td colspan="6" class="text-center text-muted py-3">Không có chi tiết</td></tr>';return;}
        let total=0;
        document.getElementById('tbody').innerHTML=details.map(d=>{const tt=(d.Soluong||0)*(d.Dongiaxuat||0);total+=tt;return`<tr>
            <td><code>${d.Masp}</code></td>
            <td>${d.Tensp||'—'}</td>
            <td>${d.Dvt||'—'}</td>
            <td class="text-end">${Number(d.Soluong||0).toLocaleString('vi-VN')}</td>
            <td class="text-end">${fmtMoney(d.Dongiaxuat)}</td>
            <td class="text-end fw-bold text-success">${fmtMoney(tt)}</td>
        </tr>`;}).join('');
        document.getElementById('tongTien').textContent=fmtMoney(total);
    }catch(e){document.getElementById('alertBox').textContent='Lỗi: '+e.message;document.getElementById('alertBox').classList.remove('d-none');}
}
load();
</script>
</body>
</html>
