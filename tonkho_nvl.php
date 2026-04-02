<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Tồn kho nguyên vật liệu - Quản lý kho VLXD</title>
    <script>const token=localStorage.getItem('token');if(!token)window.location.href='dangnhap.php';</script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body{background:#f0f4f8;font-family:'Segoe UI',sans-serif}
        .sidebar{background:linear-gradient(180deg,#1e3a5f,#0d2137);height:100vh;position:fixed;width:250px;color:white;padding-top:20px;top:0;left:0;overflow-y:auto;z-index:100}
        .sidebar h4{font-size:1rem;font-weight:700;padding:0 20px 15px;border-bottom:1px solid rgba(255,255,255,.1)}
        .sidebar .nav-link{color:rgba(255,255,255,.8)!important;padding:10px 20px;border-radius:6px;margin:2px 8px;transition:all .25s;font-size:.88rem}
        .sidebar .nav-link:hover{background:rgba(255,255,255,.15)!important;color:#fff!important;transform:translateX(4px)}
        .main-content{margin-left:250px;padding:30px}
        .page-header{background:linear-gradient(135deg,#166534,#16a34a);color:white;border-radius:12px;padding:24px 28px;margin-bottom:24px;box-shadow:0 4px 15px rgba(22,163,74,.3)}
        .card{border:none;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08)}
        .table th{background:#f8fafc;color:#475569;font-size:.8rem;text-transform:uppercase}
        .badge-kho{background:#dbeafe;color:#1d4ed8;padding:3px 10px;border-radius:20px;font-size:.78rem;font-weight:600}
        .badge-dvt{background:#fef9c3;color:#a16207;padding:3px 10px;border-radius:20px;font-size:.78rem}
    </style>
</head>
<body>
<nav class="sidebar">
    <div class="text-center mb-3"><h4><i class="fas fa-warehouse me-2"></i>Quản Lý Kho</h4></div>
    <ul class="nav flex-column">
        <li><a class="nav-link" href="trangchu.php"><i class="fas fa-home me-2"></i>Trang Chủ</a></li>
        <li><a class="nav-link" href="danh_sach_phieu_nhap.php"><i class="fas fa-file-import me-2"></i>Phiếu nhập kho</a></li>
        <li><a class="nav-link" href="danh_sach_phieu_xuat.php"><i class="fas fa-file-export me-2"></i>Phiếu xuất</a></li>
        <li><a class="nav-link" href="danh_sach_phieu_dieuchuyen.php"><i class="fas fa-exchange-alt me-2"></i>Điều chuyển</a></li>
        <li><a class="nav-link" href="tonkho.php"><i class="fas fa-chart-bar me-2"></i>Tồn kho tổng</a></li>
        <li><a class="nav-link" href="tonkho_sp.php"><i class="fas fa-cubes me-2"></i>Tồn kho thành phẩm</a></li>
        <li><a class="nav-link" href="tonkho_nvl.php" style="background:rgba(22,163,74,.3);color:#86efac!important"><i class="fas fa-boxes-stacked me-2"></i>Tồn kho NVL</a></li>
        <li><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
    </ul>
</nav>
<div class="main-content">
    <div class="page-header">
        <h1 class="mb-1" style="font-size:1.6rem"><i class="fas fa-boxes-stacked me-2"></i>Tồn kho nguyên vật liệu</h1>
        <p class="mb-0 opacity-75">Danh sách tồn kho NVL theo kho</p>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="p-3"><input class="form-control" id="searchInput" placeholder="🔍 Tìm theo kho, mã NVL, tên..." oninput="filterTable()"></div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Kho</th><th>Mã NVL</th><th>Tên nguyên vật liệu</th><th>ĐVT</th><th class="text-end">Số lượng tồn</th></tr></thead>
                    <tbody id="tbody"><tr><td colspan="5" class="text-center py-4 text-muted">Đang tải...</td></tr></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
const API='http://localhost:8000/api/v1';
const headers={'Authorization':'Bearer '+localStorage.getItem('token')};
function filterTable(){
    const q=document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('#tbody tr').forEach(tr=>{tr.style.display=tr.textContent.toLowerCase().includes(q)?'':'none';});
}
async function load(){
    try{
        const res=await fetch(API+'/inventory',{headers});
        const data=await res.json();
        const tb=document.getElementById('tbody');
        if(!data.success)throw new Error(data.message);
        const rows=data.data.materials;
        if(!rows.length){tb.innerHTML='<tr><td colspan="5" class="text-center py-4 text-muted">Chưa có dữ liệu tồn kho NVL.</td></tr>';return;}
        tb.innerHTML=rows.map(r=>`<tr>
            <td><span class="badge-kho">[${r.Makho}] ${r.Tenkho}</span></td>
            <td><code>${r.Manvl}</code></td>
            <td><strong>${r.Tennvl}</strong></td>
            <td><span class="badge-dvt">${r.Dvt}</span></td>
            <td class="text-end fw-bold ${parseInt(r.Soluongton)<10?'text-danger':'text-success'}">${Number(r.Soluongton||0).toLocaleString('vi-VN')}</td>
        </tr>`).join('');
    }catch(e){document.getElementById('tbody').innerHTML=`<tr><td colspan="5" class="text-center text-danger py-4">Lỗi: ${e.message}</td></tr>`;}
}
load();
</script>
</body>
</html>
