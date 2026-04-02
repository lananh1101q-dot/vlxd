<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo tồn kho - Quản lý kho VLXD</title>
    <script>const token=localStorage.getItem('token');if(!token)window.location.href='dangnhap.php';</script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background:#f0f4f8; font-family:'Segoe UI',sans-serif; }
        .sidebar { background:linear-gradient(180deg,#1e3a5f 0%,#0d2137 100%); height:100vh; position:fixed; width:250px; color:white; padding-top:20px; top:0; left:0; overflow-y:auto; z-index:100; box-shadow:4px 0 15px rgba(0,0,0,0.2); }
        .sidebar h4 { font-size:1rem; font-weight:700; padding:0 20px 15px; border-bottom:1px solid rgba(255,255,255,0.1); }
        .sidebar .nav-link { color:rgba(255,255,255,0.8)!important; padding:10px 20px; border-radius:6px; margin:2px 8px; transition:all 0.25s; font-size:0.88rem; }
        .sidebar .nav-link:hover { background:rgba(255,255,255,0.15)!important; color:#fff!important; transform:translateX(4px); }
        .sidebar .nav-link.active-page { background:rgba(59,130,246,0.35)!important; color:#93c5fd!important; }
        .submenu { display:none; } .submenu.open { display:block; }
        .main-content { margin-left:250px; padding:30px; }
        .page-header { background:linear-gradient(135deg,#1e3a5f,#2563eb); color:white; border-radius:12px; padding:24px 28px; margin-bottom:24px; box-shadow:0 4px 15px rgba(37,99,235,0.3); }
        .card { border:none; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,0.08); overflow:hidden; }
        .card-header { background:#fff; border-bottom:2px solid #e2e8f0; font-weight:700; padding:16px 20px; }
        .table th { background:#f8fafc; color:#475569; font-size:0.8rem; text-transform:uppercase; letter-spacing:.05em; border-bottom:2px solid #e2e8f0; }
        .table td { vertical-align:middle; color:#374151; }
        .badge-kho { background:#dbeafe; color:#1d4ed8; padding:3px 10px; border-radius:20px; font-size:0.78rem; font-weight:600; }
        .badge-dvt { background:#dcfce7; color:#15803d; padding:3px 10px; border-radius:20px; font-size:0.78rem; }
        .qty-low { color:#dc2626; font-weight:700; }
        .qty-ok  { color:#16a34a; font-weight:700; }
        .nav-tabs .nav-link { color:#64748b; border:none; padding:10px 20px; font-weight:600; }
        .nav-tabs .nav-link.active { color:#2563eb; border-bottom:3px solid #2563eb; background:none; }
        @media(max-width:768px){.sidebar{position:relative;width:100%;height:auto}.main-content{margin-left:0}}
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
                <li><a class="nav-link" href="Congthucsanpham.php"><i class="fas fa-file-invoice me-2"></i>Công thức SP</a></li>
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
        <li>
            <a class="nav-link" href="#" onclick="toggleMenu('menuDC',this)"><i class="fas fa-exchange-alt me-2"></i>Điều chuyển<i class="fas fa-chevron-down float-end mt-1" style="font-size:.7rem"></i></a>
            <ul class="nav flex-column ms-3 submenu" id="menuDC">
                <li><a class="nav-link" href="danh_sach_phieu_dieuchuyen.php"><i class="fas fa-list me-2"></i>Danh sách</a></li>
                <li><a class="nav-link" href="phieu_dieuchuyen.php"><i class="fas fa-plus-circle me-2"></i>Tạo phiếu</a></li>
            </ul>
        </li>
        <li>
            <a class="nav-link active-page" href="#" onclick="toggleMenu('menuBC',this)"><i class="fas fa-chart-bar me-2"></i>Báo cáo & Thống kê<i class="fas fa-chevron-down float-end mt-1" style="font-size:.7rem"></i></a>
            <ul class="nav flex-column ms-3 submenu open" id="menuBC">
                <li><a class="nav-link active-page" href="tonkho.php"><i class="fas fa-warehouse me-2"></i>Tồn kho tổng</a></li>
                <li><a class="nav-link" href="tonkho_sp.php"><i class="fas fa-cubes me-2"></i>Tồn kho thành phẩm</a></li>
                <li><a class="nav-link" href="tonkho_nvl.php"><i class="fas fa-boxes-stacked me-2"></i>Tồn kho NVL</a></li>
            </ul>
        </li>
        <li>
            <a class="nav-link" href="#" onclick="toggleMenu('menuKH',this)"><i class="fas fa-users me-2"></i>Khách hàng<i class="fas fa-chevron-down float-end mt-1" style="font-size:.7rem"></i></a>
            <ul class="nav flex-column ms-3 submenu" id="menuKH">
                <li><a class="nav-link" href="khachhang.php"><i class="fas fa-user me-2"></i>Khách hàng</a></li>
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
    <div class="page-header">
        <h1 class="mb-1" style="font-size:1.6rem"><i class="fas fa-warehouse me-2"></i>Báo cáo tồn kho</h1>
        <p class="mb-0 opacity-75">Xem tồn kho thành phẩm và nguyên vật liệu theo kho</p>
    </div>

    <div id="alertBox" class="alert alert-warning d-none">Đang tải dữ liệu...</div>

    <div class="card mb-4">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="invTabs">
                <li class="nav-item"><a class="nav-link active" onclick="showTab('sp')" href="#" id="tabSP">
                    <i class="fas fa-cubes me-1"></i>Tồn kho thành phẩm
                </a></li>
                <li class="nav-item"><a class="nav-link" onclick="showTab('nvl')" href="#" id="tabNVL">
                    <i class="fas fa-layer-group me-1"></i>Tồn kho nguyên vật liệu
                </a></li>
            </ul>
        </div>
        <div class="card-body p-0">
            <div id="paneSP">
                <div class="p-3"><input class="form-control" id="searchSP" placeholder="🔍 Tìm theo kho, mã SP, tên..." oninput="filterTable('tbodySP','searchSP')"></div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr>
                            <th>Kho</th><th>Mã SP</th><th>Tên sản phẩm</th><th>ĐVT</th>
                            <th class="text-end">Số lượng tồn</th>
                        </tr></thead>
                        <tbody id="tbodySP"><tr><td colspan="5" class="text-center py-4 text-muted">Đang tải...</td></tr></tbody>
                    </table>
                </div>
            </div>
            <div id="paneNVL" class="d-none">
                <div class="p-3"><input class="form-control" id="searchNVL" placeholder="🔍 Tìm theo kho, mã NVL, tên..." oninput="filterTable('tbodyNVL','searchNVL')"></div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr>
                            <th>Kho</th><th>Mã NVL</th><th>Tên NVL</th><th>ĐVT</th>
                            <th class="text-end">Số lượng tồn</th>
                        </tr></thead>
                        <tbody id="tbodyNVL"><tr><td colspan="5" class="text-center py-4 text-muted">Đang tải...</td></tr></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const API = 'http://localhost:8000/api/v1';
const headers = { 'Authorization': 'Bearer ' + localStorage.getItem('token') };

function toggleMenu(id, el) {
    const m = document.getElementById(id);
    m.classList.toggle('open');
    event.preventDefault();
}

function showTab(tab) {
    document.getElementById('paneSP').classList.toggle('d-none', tab !== 'sp');
    document.getElementById('paneNVL').classList.toggle('d-none', tab !== 'nvl');
    document.getElementById('tabSP').classList.toggle('active', tab === 'sp');
    document.getElementById('tabNVL').classList.toggle('active', tab === 'nvl');
}

function filterTable(tbodyId, inputId) {
    const query = document.getElementById(inputId).value.toLowerCase();
    document.querySelectorAll('#' + tbodyId + ' tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(query) ? '' : 'none';
    });
}

function fmt(n) { return Number(n||0).toLocaleString('vi-VN'); }

async function loadInventory() {
    try {
        const res = await fetch(API + '/inventory', { headers });
        const data = await res.json();
        if (!data.success) throw new Error(data.message);

        // Thành phẩm
        const tbSP = document.getElementById('tbodySP');
        if (data.data.products.length === 0) {
            tbSP.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Chưa có dữ liệu tồn kho thành phẩm.</td></tr>';
        } else {
            tbSP.innerHTML = data.data.products.map(r => `
                <tr>
                    <td><span class="badge-kho">[${r.Makho}] ${r.Tenkho}</span></td>
                    <td><code>${r.Masp}</code></td>
                    <td><strong>${r.Tensp}</strong></td>
                    <td><span class="badge-dvt">${r.Dvt}</span></td>
                    <td class="text-end ${r.Soluongton < 10 ? 'qty-low' : 'qty-ok'}">${fmt(r.Soluongton)}</td>
                </tr>`).join('');
        }

        // NVL
        const tbNVL = document.getElementById('tbodyNVL');
        if (data.data.materials.length === 0) {
            tbNVL.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Chưa có dữ liệu tồn kho NVL.</td></tr>';
        } else {
            tbNVL.innerHTML = data.data.materials.map(r => `
                <tr>
                    <td><span class="badge-kho">[${r.Makho}] ${r.Tenkho}</span></td>
                    <td><code>${r.Manvl}</code></td>
                    <td><strong>${r.Tennvl}</strong></td>
                    <td><span class="badge-dvt">${r.Dvt}</span></td>
                    <td class="text-end ${r.Soluongton < 10 ? 'qty-low' : 'qty-ok'}">${fmt(r.Soluongton)}</td>
                </tr>`).join('');
        }
    } catch(e) {
        document.getElementById('alertBox').classList.remove('d-none');
        document.getElementById('alertBox').textContent = 'Lỗi tải dữ liệu: ' + e.message;
    }
}

loadInventory();
</script>
</body>
</html>
