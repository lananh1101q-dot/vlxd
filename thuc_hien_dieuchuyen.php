<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Thực hiện điều chuyển - VLXD</title>
    <script>const token=localStorage.getItem('token');if(!token)window.location.href='dangnhap.php';</script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body{background:#f0f4f8;font-family:'Segoe UI',sans-serif}
        .main-content{max-width:960px;margin:40px auto;padding:20px}
        .page-header{background:linear-gradient(135deg,#065f46,#059669);color:white;border-radius:12px;padding:24px 28px;margin-bottom:24px}
        .card{border:none;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08)}
        .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px}
        .info-item{background:#f0fdf4;border-radius:8px;padding:12px 16px;border:1px solid #bbf7d0}
        .info-label{font-size:.75rem;color:#065f46;text-transform:uppercase;font-weight:700;margin-bottom:2px}
        .info-value{font-size:1rem;font-weight:700;color:#1e293b}
        .table th{background:#f0fdf4;color:#065f46;font-size:.8rem;text-transform:uppercase}
        .table td{vertical-align:middle}
        .warning-box{background:#fffbeb;border:2px solid #fcd34d;border-radius:10px;padding:16px 20px;margin-bottom:20px}
        .arrow-flow{display:flex;align-items:center;gap:12px;background:#f0fdf4;border-radius:10px;padding:14px 20px;margin-bottom:20px;border:1px solid #86efac}
        .kho-chip{background:white;border:1px solid #6ee7b7;border-radius:8px;padding:8px 14px;font-weight:700;color:#065f46;font-size:.9rem}
        .ton-ok{color:#16a34a;font-weight:700}
        .ton-low{color:#dc2626;font-weight:700}
    </style>
</head>
<body>
<div class="main-content">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-1" style="font-size:1.5rem"><i class="fas fa-check-double me-2"></i>Xác nhận thực hiện điều chuyển</h1>
            <p class="mb-0 opacity-75" id="subTitle">Đang tải thông tin phiếu...</p>
        </div>
        <a id="backBtn" href="danh_sach_phieu_dieuchuyen.php" class="btn btn-light btn-sm"><i class="fas fa-arrow-left me-1"></i>Danh sách</a>
    </div>

    <div id="alertBox" class="alert d-none mb-3"></div>

    <div id="mainContent" class="d-none">
        <div class="arrow-flow" id="arrowFlow"></div>

        <div class="card mb-4">
            <div class="card-body">
                <h6 class="fw-bold text-success mb-3"><i class="fas fa-info-circle me-1"></i>Thông tin phiếu</h6>
                <div class="info-grid" id="infoGrid"></div>
                <h6 class="fw-bold text-success mb-3"><i class="fas fa-boxes-stacked me-1"></i>Chi tiết sản phẩm & tồn kho hiện tại</h6>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr>
                            <th>Mã SP</th><th>Tên sản phẩm</th><th>ĐVT</th>
                            <th class="text-end">Số lượng ĐC</th><th class="text-end">Tồn kho xuất</th><th class="text-center">Trạng thái</th>
                        </tr></thead>
                        <tbody id="tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="warning-box" id="warnBox">
            <div class="d-flex align-items-start gap-3">
                <i class="fas fa-exclamation-triangle text-warning fs-4 mt-1"></i>
                <div>
                    <strong>Cảnh báo quan trọng!</strong>
                    <p class="mb-0 mt-1 text-muted">Hành động này sẽ <strong>cập nhật tồn kho vĩnh viễn</strong>: kho xuất giảm, kho nhập tăng số lượng tương ứng. Hãy kiểm tra kỹ tồn kho trước khi xác nhận.</p>
                </div>
            </div>
        </div>

        <div class="d-flex gap-3 justify-content-center" id="actionBar">
            <a href="danh_sach_phieu_dieuchuyen.php" class="btn btn-secondary px-4">Hủy bỏ</a>
            <button class="btn btn-success fw-semibold px-5" id="btnConfirm" onclick="execute()">
                <i class="fas fa-check me-2"></i>Xác nhận thực hiện điều chuyển
            </button>
        </div>
    </div>
</div>

<script>
const API='http://localhost:8000/api/v1';
const headers={'Authorization':'Bearer '+localStorage.getItem('token')};
const id=new URLSearchParams(location.search).get('id');
let transferData=null;

function fmtDate(s){if(!s)return'—';return new Date(s).toLocaleDateString('vi-VN');}
function showAlert(msg,type='success'){const a=document.getElementById('alertBox');a.className=`alert alert-${type}`;a.innerHTML=msg;a.classList.remove('d-none');}

async function loadInventory(makho, masp){
    try{
        const res=await fetch(API+'/inventory?type=sp&makho='+makho+'&masp='+masp,{headers});
        return null; // inventory endpoint returns aggregate, use direct check
    }catch(e){return null;}
}

async function load(){
    if(!id){showAlert('Không có mã phiếu trong URL. <a href="danh_sach_phieu_dieuchuyen.php">Quay lại danh sách</a>','danger');return;}
    try{
        const res=await fetch(API+'/transfers/'+encodeURIComponent(id),{headers});
        const data=await res.json();
        if(!data.success)throw new Error(data.message);
        const r=data.data.transfer;
        transferData=r;
        document.getElementById('subTitle').textContent='Phiếu: '+r.Madieuchuyen;
        document.getElementById('backBtn').href='chi_tiet_phieu_dieuchuyen.php?id='+encodeURIComponent(id);

        if(r.Trangthai==='da_thuc_hien'){
            showAlert('<strong>Phiếu này đã được thực hiện rồi.</strong> Tồn kho đã được cập nhật trước đó. <a href="danh_sach_phieu_dieuchuyen.php">Quay lại danh sách</a>','warning');
            return;
        }

        document.getElementById('arrowFlow').innerHTML=`
            <div class="kho-chip"><i class="fas fa-store-alt me-1"></i>${r.TenKhoXuat||r.Khoxuat}</div>
            <div class="text-center flex-grow-1"><i class="fas fa-long-arrow-alt-right fa-2x text-success"></i><div class="small text-muted">Điều chuyển</div></div>
            <div class="kho-chip"><i class="fas fa-store me-1"></i>${r.TenKhoNhap||r.Khonhap}</div>`;

        document.getElementById('infoGrid').innerHTML=`
            <div class="info-item"><div class="info-label">Mã phiếu</div><div class="info-value">${r.Madieuchuyen}</div></div>
            <div class="info-item"><div class="info-label">Ngày điều chuyển</div><div class="info-value">${fmtDate(r.Ngaydieuchuyen)}</div></div>
            <div class="info-item"><div class="info-label">Kho xuất</div><div class="info-value">${r.TenKhoXuat||r.Khoxuat}</div></div>
            <div class="info-item"><div class="info-label">Kho nhập</div><div class="info-value">${r.TenKhoNhap||r.Khonhap}</div></div>
            ${r.Ghichu?`<div class="info-item" style="grid-column:1/-1"><div class="info-label">Ghi chú</div><div class="info-value">${r.Ghichu}</div></div>`:''}`;

        const details=r.details||[];
        let allOk=true;
        document.getElementById('tbody').innerHTML=details.map(d=>{
            const ok=(d.TonKhoXuat===undefined)?true:(d.TonKhoXuat>=d.Soluong);
            if(!ok)allOk=false;
            return`<tr>
                <td><code class="text-success">${d.Masp}</code></td>
                <td>${d.Tensp||'—'}</td>
                <td>${d.Dvt||'—'}</td>
                <td class="text-end fw-bold">${Number(d.Soluong||0).toLocaleString('vi-VN')}</td>
                <td class="text-end ${ok?'ton-ok':'ton-low'}">${d.TonKhoXuat!==undefined?Number(d.TonKhoXuat).toLocaleString('vi-VN'):'—'}</td>
                <td class="text-center">${ok?'<span class="badge bg-success">✓ Đủ</span>':'<span class="badge bg-danger">✗ Thiếu</span>'}</td>
            </tr>`;
        }).join('');

        if(!allOk){document.getElementById('btnConfirm').disabled=true;showAlert('Một số sản phẩm không đủ tồn kho. Không thể thực hiện điều chuyển.','danger');}
        document.getElementById('mainContent').classList.remove('d-none');
    }catch(e){showAlert('Lỗi tải dữ liệu: '+e.message,'danger');}
}

async function execute(){
    if(!confirm('Xác nhận thực hiện điều chuyển?\nTồn kho '+(transferData?.TenKhoXuat||'kho xuất')+' sẽ giảm và '+(transferData?.TenKhoNhap||'kho nhập')+' sẽ tăng.'))return;
    document.getElementById('btnConfirm').disabled=true;
    document.getElementById('btnConfirm').innerHTML='<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';
    try{
        const res=await fetch(API+'/transfers/'+encodeURIComponent(id)+'/execute',{method:'POST',headers:{...headers,'Content-Type':'application/json'}});
        const data=await res.json();
        if(data.success){
            document.getElementById('actionBar').innerHTML='';
            document.getElementById('warnBox').classList.add('d-none');
            showAlert(`<strong><i class="fas fa-check-circle me-1"></i>Điều chuyển hoàn thành!</strong> Tồn kho hai kho đã được cập nhật. <a href="danh_sach_phieu_dieuchuyen.php" class="btn btn-success btn-sm ms-2">← Về danh sách</a>`);
        }else{
            showAlert(data.message||'Có lỗi xảy ra','danger');
            document.getElementById('btnConfirm').disabled=false;
            document.getElementById('btnConfirm').innerHTML='<i class="fas fa-check me-2"></i>Xác nhận thực hiện điều chuyển';
        }
    }catch(e){
        showAlert('Lỗi kết nối server','danger');
        document.getElementById('btnConfirm').disabled=false;
        document.getElementById('btnConfirm').innerHTML='<i class="fas fa-check me-2"></i>Xác nhận thực hiện điều chuyển';
    }
}

load();
</script>
</body>
</html>