<?php
session_start();
require_once __DIR__ . '/role_helper.php';

// 1. Kiểm tra bảo mật: Nếu chưa đăng nhập thì bắt quay lại trang dangnhap.php
if (!isset($_SESSION['user'])) {
    header("Location: dangnhap.php");
    exit;
}

if (!isset($_SESSION['user'])) {
    header("Location: dangnhap.php");
    exit;
}

$role = $_SESSION['user']['role'] ?? 'guest';//

// PHÂN QUYỀN MENU
$menus = [
    'admin' => ['all' => true],
    'staff' => ['phieunhap'=>true,'phieuxuat'=>true,'khachhang'=>true,'baocao'=>true,'sanpham'=>true],
    'sanxuat' => ['sanxuat'=>true,'baocao'=>true]
];

$permission = $menus[$role] ?? [];

// Lấy thông tin người dùng từ Session để hiển thị
$user = $_SESSION['user'];
$role = $user['role'] ?? 'guest';
$roleName = getRoleName($role);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nguyên vật liệu - VLXD</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .sidebar { background: linear-gradient(180deg, #007bff, #0056b3); height: 100vh; position: fixed; width: 250px; color: white; padding-top: 20px; z-index: 1000; }
        .sidebar .nav-link { color: white !important; padding: 12px 20px; border-radius: 6px; margin: 4px 10px; transition: all 0.3s; }
        .sidebar .nav-link:hover { background: rgba(255,255,255,0.2); transform: translateX(5px); }
        .main-content { margin-left: 250px; padding: 30px; min-height: 100vh; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .chu { font-weight: 700; color: #198754; }
        .btn-action { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; margin: 0 2px; }
    </style>
</head>
<body>
    <nav class="sidebar">
    <div class="text-center mb-4">
        <h4><i class="fas fa-warehouse"></i> Quản Lý Kho</h4>
        <div style="font-size: 0.85rem; margin-top: 10px; padding: 10px; background-color: rgba(255,255,255,0.1); border-radius: 5px;">
            <div><strong><?= htmlspecialchars($user['fullname'] ?? $user['username']) ?></strong></div>
            <div style="font-size: 0.75rem; margin-top: 5px;">
                <i class="fas fa-user-circle"></i> <?= $role ?>
            </div>
        </div>
    </div>

    <ul class="nav flex-column">

        <!-- Trang chủ -->
        <li class="nav-item">
            <a class="nav-link" href="trangchu.php"><i class="fas fa-home"></i> Trang Chủ</a>
        </li>

        <!-- SẢN PHẨM -->
        <?php if (!empty($permission['sanpham']) || isset($permission['all'])): ?>
        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0)" id="btnSanPham">
                <i class="fas fa-box"></i> Quản lý sản phẩm
            </a>
            <ul class="nav flex-column ms-3 d-none" id="submenuSanPham">
                <li><a class="nav-link" href="Sanpham.php">Sản phẩm</a></li>
                <li><a class="nav-link" href="dmsp.php">Danh mục</a></li>
                <li><a class="nav-link" href="Nhacungcap.php">Nhà cung cấp</a></li>
            </ul>
        </li>
        <?php endif; ?>

        <!-- PHIẾU NHẬP -->
        <?php if (!empty($permission['phieunhap']) || isset($permission['all'])): ?>
        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0)" id="btnPhieuNhap">
                <i class="fas fa-file-import"></i> Phiếu nhập
            </a>
            <ul class="nav flex-column ms-3 d-none" id="submenuPhieuNhap">
                <li><a class="nav-link" href="danh_sach_phieu_nhap.php">Danh sách</a></li>
                <li><a class="nav-link" href="phieu_nhap.php">Tạo phiếu</a></li>
            </ul>
        </li>
        <?php endif; ?>
   <!-- PHIẾU xuất -->
        <?php if (!empty($permission['phieuxuat']) || isset($permission['all'])): ?>
        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0)" id="btnPhieuXuat">
                <i class="fas fa-file-import"></i> Phiếu xuất
            </a>
            <ul class="nav flex-column ms-3 d-none" id="submenuPhieuXuat">
                <li><a class="nav-link" href="danh_sach_phieu_xuat.php">Danh sách</a></li>
                <li><a class="nav-link" href="phieu_xuat.php">Tạo phiếu</a></li>
            </ul>
        </li>
        <?php endif; ?>
        <!-- PHIẾU điều chuyển -->
        <?php if (!empty($permission['phieudc']) || isset($permission['all'])): ?>
        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0)" id="btnPhieudc">
                <i class="fas fa-file-export"></i> Phiếu điều chuyển
            </a>
            <ul class="nav flex-column ms-3 d-none" id="submenuPhieudc">
                <li><a class="nav-link" href="danh_sach_phieu_dieuchuyen.php">Danh sách</a></li>
                <li><a class="nav-link" href="phieu_dieuchuyen.php">Tạo phiếu</a></li>
            </ul>
        </li>
        <?php endif; ?>
     

        <!-- SẢN XUẤT -->
        <?php if (!empty($permission['sanxuat']) || isset($permission['all'])): ?>
        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0)" id="btnSanXuat">
                <i class="fas fa-cogs"></i> Sản xuất
            </a>
            <ul class="nav flex-column ms-3 d-none" id="submenuSanXuat">
                <li><a class="nav-link" href="danh_sach_lenh_san_xuat.php">Danh sách</a></li>
                <li><a class="nav-link" href="lenh_san_xuat.php">Tạo lệnh</a></li>
            </ul>
        </li>
        <?php endif; ?>

        <!-- BÁO CÁO -->
        <?php if (!empty($permission['baocao']) || isset($permission['all'])): ?>
        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0)" id="btnBaoCao">
                <i class="fas fa-chart-bar"></i> Báo cáo
            </a>
            <ul class="nav flex-column ms-3 d-none" id="submenuBaoCao">
                <li><a class="nav-link" href="tonkho.php">Tồn kho</a></li>
            </ul>
        </li>
        <?php endif; ?>

        <!-- KHÁCH HÀNG -->
        <?php if (!empty($permission['khachhang']) || isset($permission['all'])): ?>
        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0)" id="btnKhachHang">
                <i class="fas fa-users"></i> Khách hàng
            </a>
            <ul class="nav flex-column ms-3 d-none" id="submenuKhachHang">
                <li><a class="nav-link" href="khachhang.php">Khách hàng</a></li>
                <li><a class="nav-link" href="loaikhachhang.php">Loại KH</a></li>
            </ul>
        </li>
        <?php endif; ?>

        <!-- LOGOUT -->
        <li class="nav-item mt-4 pt-3 border-top">
            <a class="nav-link text-danger" href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </a>
        </li>

    </ul>
</nav>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark">Nguyên vật liệu</h2>
            <button class="btn btn-success fw-bold px-4" onclick="openModal()">
                <i class="fas fa-plus me-2"></i>Thêm Nguyên vật liệu
            </button>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" id="tkten" class="form-control border-start-0" placeholder="Tìm theo tên hoặc mã nguyên vật liệu..." onkeyup="filterMaterials()">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Mã NVL</th>
                                <th>Tên Nguyên vật liệu</th>
                                <th>Đơn vị tính</th>
                                <th class="text-end">Giá vốn ước tính</th>
                                <th class="text-center pe-4">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="materialList">
                            <tr><td colspan="5" class="text-center py-5 text-muted">Đang tải dữ liệu...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Thêm/Sửa NVL -->
    <div class="modal fade" id="materialModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold" id="modalTitle">Thêm Nguyên vật liệu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="materialForm">
                        <input type="hidden" id="editMode" value="false">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mã Nguyên vật liệu *</label>
                            <input type="text" id="Manvl" class="form-control" required placeholder="Ví dụ: NVL001">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên Nguyên vật liệu *</label>
                            <input type="text" id="Tennvl" class="form-control" required placeholder="Nhập tên vật liệu">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Đơn vị tính *</label>
                                <input type="text" id="Dvt" class="form-control" required placeholder="Kg, m3, bao...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Giá vốn (nếu có)</label>
                                <input type="number" id="Giavon" class="form-control" placeholder="0">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-success px-4 fw-bold" onclick="saveMaterial()">Lưu thông tin</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
document.addEventListener("DOMContentLoaded", function () {

    function toggleMenu(btnId, subId) {
        const btn = document.getElementById(btnId);
        const sub = document.getElementById(subId);

        if (btn && sub) {
            btn.addEventListener("click", function () {
                sub.classList.toggle("d-none");
            });
        }
    }

    // GỌI CHO TẤT CẢ MENU
    toggleMenu("btnSanPham", "submenuSanPham");
    toggleMenu("btnPhieuNhap", "submenuPhieuNhap");
    toggleMenu("btnPhieuXuat", "submenuPhieuXuat");
     toggleMenu("btnPhieudc", "submenuPhieudc");
    toggleMenu("btnSanXuat", "submenuSanXuat");
    toggleMenu("btnBaoCao", "submenuBaoCao");
    toggleMenu("btnKhachHang", "submenuKhachHang");

});
</script>
    <script>
        const getHeaders = () => {
            const token = localStorage.getItem('token');
            if (!token) return null;
            return { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' };
        };
        const API = 'http://localhost:8000/api/v1';
        let allMaterials = [];
        const modal = new bootstrap.Modal(document.getElementById('materialModal'));

        async function loadData() {
            const headers = getHeaders();
            if (!headers) return window.location.href = 'dangnhap.php';
            try {
                const res = await fetch(API + '/materials', { headers });
                const data = await res.json();
                if(data.success) {
                    allMaterials = data.data.materials || [];
                    renderMaterials(allMaterials);
                }
            } catch(err) {
                document.getElementById('materialList').innerHTML = '<tr><td colspan="5" class="text-danger text-center py-4">Lỗi kết nối API</td></tr>';
            }
        }

        function renderMaterials(list) {
            const tbody = document.getElementById('materialList');
            tbody.innerHTML = list.length ? '' : '<tr><td colspan="5" class="text-center py-4 text-muted">Không tìm thấy dữ liệu</td></tr>';
            list.forEach(item => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="ps-4"><code>${item.Manvl}</code></td>
                    <td class="chu">${item.Tennvl}</td>
                    <td>${item.Dvt || '—'}</td>
                    <td class="text-end fw-bold">${parseInt(item.Giavon || 0).toLocaleString('vi-VN')} đ</td>
                    <td class="text-center pe-4">
                        <button class="btn btn-outline-primary btn-action" onclick="openModal('${item.Manvl}')"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-outline-danger btn-action" onclick="deleteMaterial('${item.Manvl}')"><i class="fas fa-trash"></i></button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function filterMaterials() {
            const key = document.getElementById('tkten').value.toLowerCase();
            const filtered = allMaterials.filter(m => 
                m.Tennvl.toLowerCase().includes(key) || m.Manvl.toLowerCase().includes(key)
            );
            renderMaterials(filtered);
        }

        function openModal(id = null) {
            document.getElementById('materialForm').reset();
            const editModeInput = document.getElementById('editMode');
            const maInput = document.getElementById('Manvl');

            if (id) {
                document.getElementById('modalTitle').innerText = 'Sửa Nguyên vật liệu';
                const m = allMaterials.find(x => x.Manvl === id);
                if (m) {
                    editModeInput.value = "true";
                    maInput.value = m.Manvl;
                    maInput.readOnly = true;
                    document.getElementById('Tennvl').value = m.Tennvl;
                    document.getElementById('Dvt').value = m.Dvt || '';
                    document.getElementById('Giavon').value = m.Giavon || '';
                }
            } else {
                document.getElementById('modalTitle').innerText = 'Thêm Nguyên vật liệu';
                editModeInput.value = "false";
                maInput.readOnly = false;
            }
            modal.show();
        }

        async function saveMaterial() {
            const headers = getHeaders();
            if(!headers) return;
            const isEdit = document.getElementById('editMode').value === "true";
            const id = document.getElementById('Manvl').value;
            const body = {
                Manvl: id,
                Tennvl: document.getElementById('Tennvl').value,
                Dvt: document.getElementById('Dvt').value,
                Giavon: document.getElementById('Giavon').value
            };
            const method = isEdit ? 'PUT' : 'POST';
            const url = isEdit ? API + '/materials/' + id : API + '/materials';

            if(!body.Manvl || !body.Tennvl) return alert('Vui lòng điền đủ thông tin bắt buộc!');

            try {
                const res = await fetch(url, { 
                    method, 
                    headers, 
                    body: JSON.stringify(body) 
                });
                const data = await res.json();
                
                if(data.success) {
                    alert('Lưu thành công!');
                    modal.hide();
                    loadData();
                } else {
                    alert('Lỗi: ' + data.message);
                }
            } catch(err) { alert('Lỗi kết nối API'); }
        }

        async function deleteMaterial(id) {
            if(!confirm(`Xác nhận xóa nguyên vật liệu ${id}?`)) return;
            const headers = getHeaders();
            if(!headers) return;
            try {
                const res = await fetch(`${API}/materials/${id}`, { method: 'DELETE', headers });
                const data = await res.json();
                if(data.success) { alert('Đã xóa!'); loadData(); } else alert('Lỗi: ' + data.message);
            } catch(err) { alert('Lỗi kết nối API'); }
        }

        loadData();
    </script>
</body>
</html>
