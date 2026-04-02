<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Nhà cung cấp - VLXD</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .sidebar { background: linear-gradient(180deg, #1e3a5f, #0d2137); height: 100vh; position: fixed; width: 250px; color: white; padding-top: 20px; z-index: 1000; }
        .sidebar .nav-link { color: white !important; padding: 12px 20px; border-radius: 6px; margin: 4px 10px; transition: all 0.3s; }
        .sidebar .nav-link:hover { background: rgba(255,255,255,0.1); transform: translateX(5px); }
        .main-content { margin-left: 250px; padding: 30px; min-height: 100vh; }
        .card { border: none; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .table thead { background-color: #f1f3f5; }
        .btn-action { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; margin: 0 2px; }
        .chu { font-weight: 700; color: #0d6efd; }
    </style>
</head>
<body>
    <nav class="sidebar">
        <div class="text-center mb-4"><h4><i class="fas fa-warehouse"></i> Quản Lý Kho</h4></div>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="trangchu.php"><i class="fas fa-home me-2"></i>Trang Chủ</a></li>
            <li class="nav-item"><a class="nav-link" href="Sanpham.php"><i class="fas fa-cube me-2"></i>Sản phẩm</a></li>
            <li class="nav-item"><a class="nav-link" href="dmsp.php"><i class="fas fa-tags me-2"></i>Danh mục</a></li>
            <li class="nav-item"><a class="nav-link active" href="Nhacungcap.php"><i class="fas fa-truck me-2"></i>Nhà cung cấp</a></li>
            <li class="nav-item"><a class="nav-link" href="khachhang.php"><i class="fas fa-users me-2"></i>Khách hàng</a></li>
            <li class="nav-item"><hr class="bg-white opacity-25"></li>
            <li class="nav-item"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
        </ul>
    </nav>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark">Nhà cung cấp</h2>
            <button class="btn btn-primary fw-bold px-4 shadow-sm" onclick="openModal()">
                <i class="fas fa-plus me-2"></i>Thêm Nhà cung cấp
            </button>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" id="tkten" class="form-control border-start-0" placeholder="Tìm theo tên hoặc mã NCC..." onkeyup="filterSuppliers()">
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
                                <th class="ps-4">Mã NCC</th>
                                <th>Tên nhà cung cấp</th>
                                <th>Số điện thoại</th>
                                <th>Địa chỉ</th>
                                <th class="text-center pe-4">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="supplierList">
                            <tr><td colspan="5" class="text-center py-5 text-muted">Đang tải dữ liệu...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Thêm/Sửa Nhà cung cấp -->
    <div class="modal fade" id="supplierModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalTitle">Thêm Nhà cung cấp</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="supplierForm">
                        <input type="hidden" id="editMode" value="false">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Mã Nhà cung cấp *</label>
                            <input type="text" id="Mancc" class="form-control" required placeholder="Ví dụ: NCC001">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Tên Nhà cung cấp *</label>
                            <input type="text" id="Tenncc" class="form-control" required placeholder="Nhập tên đầy đủ">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Số điện thoại</label>
                            <input type="text" id="Sdtncc" class="form-control" placeholder="Ví dụ: 0912xxxxxx">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Địa chỉ</label>
                            <textarea id="Diachincc" class="form-control" rows="3" placeholder="Nhập địa chỉ liên hệ"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary px-4 fw-bold" onclick="saveSupplier()">Lưu Nhà cung cấp</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const getHeaders = () => {
            const token = localStorage.getItem('token');
            if (!token) return null;
            return { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' };
        };
        const API = 'http://localhost:8000/api/v1';
        let allSuppliers = [];
        const modal = new bootstrap.Modal(document.getElementById('supplierModal'));

        async function loadData() {
            const headers = getHeaders();
            if (!headers) return window.location.href = 'dangnhap.php';
            try {
                const res = await fetch(API + '/suppliers', { headers });
                const data = await res.json();
                if(data.success) {
                    allSuppliers = data.data.suppliers || [];
                    renderSuppliers(allSuppliers);
                }
            } catch(err) {
                document.getElementById('supplierList').innerHTML = '<tr><td colspan="5" class="text-danger text-center py-4">Lỗi kết nối API Gateway</td></tr>';
            }
        }

        function renderSuppliers(list) {
            const tbody = document.getElementById('supplierList');
            tbody.innerHTML = list.length ? '' : '<tr><td colspan="5" class="text-center py-4 text-muted">Không tìm thấy NCC nào</td></tr>';
            list.forEach(item => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="ps-4"><code>${item.Mancc}</code></td>
                    <td class="chu">${item.Tenncc}</td>
                    <td>${item.Sdtncc || '—'}</td>
                    <td><small class="text-secondary">${item.Diachincc || '—'}</small></td>
                    <td class="text-center pe-4">
                        <button class="btn btn-outline-primary btn-action" onclick="openModal('${item.Mancc}')"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-outline-danger btn-action" onclick="deleteSupplier('${item.Mancc}')"><i class="fas fa-trash"></i></button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function filterSuppliers() {
            const key = document.getElementById('tkten').value.toLowerCase();
            const filtered = allSuppliers.filter(c => 
                c.Tenncc.toLowerCase().includes(key) || c.Mancc.toLowerCase().includes(key)
            );
            renderSuppliers(filtered);
        }

        function openModal(id = null) {
            document.getElementById('supplierForm').reset();
            const editModeInput = document.getElementById('editMode');
            const maInput = document.getElementById('Mancc');

            if (id) {
                document.getElementById('modalTitle').innerText = 'Sửa Nhà cung cấp';
                const s = allSuppliers.find(x => x.Mancc === id);
                if (s) {
                    editModeInput.value = "true";
                    maInput.value = s.Mancc;
                    maInput.readOnly = true;
                    document.getElementById('Tenncc').value = s.Tenncc;
                    document.getElementById('Sdtncc').value = s.Sdtncc || '';
                    document.getElementById('Diachincc').value = s.Diachincc || '';
                }
            } else {
                document.getElementById('modalTitle').innerText = 'Thêm Nhà cung cấp';
                editModeInput.value = "false";
                maInput.readOnly = false;
            }
            modal.show();
        }

        async function saveSupplier() {
            const headers = getHeaders();
            if(!headers) return;
            const isEdit = document.getElementById('editMode').value === "true";
            const id = document.getElementById('Mancc').value;
            const body = {
                Mancc: id,
                Tenncc: document.getElementById('Tenncc').value,
                Sdtncc: document.getElementById('Sdtncc').value,
                Diachincc: document.getElementById('Diachincc').value
            };
            const method = isEdit ? 'PUT' : 'POST';
            const url = isEdit ? API + '/suppliers/' + id : API + '/suppliers';

            try {
                const res = await fetch(url, {
                    method,
                    headers: { ...headers, 'Content-Type': 'application/json' },
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

        async function deleteSupplier(id) {
            if(!confirm(`Xác nhận xóa nhà cung cấp ${id}?`)) return;
            const headers = getHeaders();
            if(!headers) return;
            try {
                const res = await fetch(`${API}/suppliers/${id}`, { method: 'DELETE', headers });
                const data = await res.json();
                if(data.success) { alert('Đã xóa!'); loadData(); } else alert('Lỗi: ' + data.message);
            } catch(err) { alert('Lỗi kết nối API'); }
        }

        loadData();
    </script>
</body>
</html>