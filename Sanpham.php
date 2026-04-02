<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Sản phẩm - VLXD</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .sidebar { background: linear-gradient(180deg, #007bff, #0056b3); height: 100vh; position: fixed; width: 250px; color: white; padding-top: 20px; z-index: 1000; }
        .sidebar .nav-link { color: white !important; padding: 12px 20px; border-radius: 5px; margin: 4px 10px; transition: all 0.3s; }
        .sidebar .nav-link:hover { background: rgba(255,255,255,0.2); transform: translateX(5px); }
        .main-content { margin-left: 250px; padding: 30px; min-height: 100vh; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .table thead { background-color: #f1f3f5; }
        .chu { font-weight: 700; color: #d30b0b; }
        .chip { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; background: #e8f0fe; color: #1967d2; }
        .btn-action { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; margin: 0 2px; }
        .d-none { display: none !important; }
    </style>
</head>
<body>
    <nav class="sidebar">
        <div class="text-center mb-4"><h4><i class="fas fa-warehouse"></i> Quản Lý Kho</h4></div>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="trangchu.php"><i class="fas fa-home me-2"></i>Trang Chủ</a></li>
            <li class="nav-item">
                <a class="nav-link active" href="Sanpham.php"><i class="fas fa-cube me-2"></i>Sản phẩm</a>
            </li>
            <li class="nav-item"><a class="nav-link" href="dmsp.php"><i class="fas fa-tags me-2"></i>Danh mục</a></li>
            <li class="nav-item"><a class="nav-link" href="Nhacungcap.php"><i class="fas fa-truck me-2"></i>Nhà cung cấp</a></li>
            <li class="nav-item"><a class="nav-link" href="khachhang.php"><i class="fas fa-users me-2"></i>Khách hàng</a></li>
            <li class="nav-item"><hr class="bg-white opacity-25"></li>
            <li class="nav-item"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
        </ul>
    </nav>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark">Danh sách Sản phẩm</h2>
            <button class="btn btn-success fw-bold px-4" onclick="openModal()">
                <i class="fas fa-plus me-2"></i>Thêm sản phẩm
            </button>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-5">
                        <input type="text" id="tkten" class="form-control" placeholder="Tìm theo tên sản phẩm..." onkeyup="filterProducts()">
                    </div>
                    <div class="col-md-4">
                        <select id="tkdm" class="form-select" onchange="filterProducts()">
                            <option value="">Tất cả danh mục</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Mã SP</th>
                                <th>Tên sản phẩm</th>
                                <th>Danh mục</th>
                                <th>Đơn vị</th>
                                <th class="text-end">Giá bán</th>
                                <th class="text-center pe-4">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="productList">
                            <tr><td colspan="6" class="text-center py-5 text-muted">Đang tải dữ liệu...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Thêm/Sửa Sản phẩm -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">Thêm Sản phẩm</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm">
                        <input type="hidden" id="editMode" value="false">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mã sản phẩm *</label>
                            <input type="text" id="Masp" class="form-control" required placeholder="Ví dụ: SP001">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên sản phẩm *</label>
                            <input type="text" id="Tensp" class="form-control" required placeholder="Nhập tên sản phẩm">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Danh mục *</label>
                            <select id="Madm" class="form-select" required>
                                <option value="">-- Chọn danh mục --</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Đơn vị *</label>
                                <input type="text" id="Dvt" class="form-control" required placeholder="Cái, Bộ, m2...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Giá bán *</label>
                                <input type="number" id="Giaban" class="form-control" required placeholder="0">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary px-4 fw-bold" onclick="saveProduct()">Lưu sản phẩm</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Define a helper to get headers with a fresh token
        const getHeaders = () => {
            const token = localStorage.getItem('token');
            if (!token) return null;
            return { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' };
        };
        const API = 'http://localhost:8000/api/v1';
        let allProducts = [];
        const modal = new bootstrap.Modal(document.getElementById('productModal'));

        async function loadData() {
            try {
                const headers = getHeaders();
                if(!headers) return window.location.href='dangnhap.php';

                // Tải song song Sản phẩm và Danh mục
                const [resP, resC] = await Promise.all([
                    fetch(API + '/products', { headers }),
                    fetch(API + '/categories', { headers })
                ]);

                const dataP = await resP.json();
                const dataC = await resC.json();

                if(dataC.success) {
                    const sel = document.getElementById('Madm');
                    const filterSel = document.getElementById('tkdm');
                    sel.innerHTML = '<option value="">-- Chọn danh mục --</option>';
                    dataC.data.categories.forEach(c => {
                        sel.innerHTML += `<option value="${c.Madm}">${c.Tendm}</option>`;
                        filterSel.innerHTML += `<option value="${c.Madm}">${c.Tendm}</option>`;
                    });
                }

                if(dataP.success) {
                    allProducts = dataP.data.products || [];
                    renderProducts(allProducts);
                }
            } catch(err) {
                document.getElementById('productList').innerHTML = '<tr><td colspan="6" class="text-danger text-center py-4">Lỗi kết nối hệ thống</td></tr>';
            }
        }

        function renderProducts(list) {
            const tbody = document.getElementById('productList');
            tbody.innerHTML = list.length ? '' : '<tr><td colspan="6" class="text-center py-4 text-muted">Không tìm thấy sản phẩm nào</td></tr>';
            list.forEach(item => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="ps-4"><code>${item.Masp}</code></td>
                    <td class="chu">${item.Tensp}</td>
                    <td><span class="chip">${item.Tendm || item.Madm}</span></td>
                    <td>${item.Dvt}</td>
                    <td class="text-end fw-bold">${parseInt(item.Giaban || 0).toLocaleString('vi-VN')} đ</td>
                    <td class="text-center pe-4">
                        <button class="btn btn-outline-primary btn-action" onclick="openModal('${item.Masp}')"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-outline-danger btn-action" onclick="deleteProduct('${item.Masp}')"><i class="fas fa-trash"></i></button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function filterProducts() {
            const ten = document.getElementById('tkten').value.toLowerCase();
            const dm = document.getElementById('tkdm').value;
            const filtered = allProducts.filter(p => 
                (p.Tensp.toLowerCase().includes(ten) || p.Masp.toLowerCase().includes(ten)) &&
                (dm === '' || p.Madm === dm)
            );
            renderProducts(filtered);
        }

        function openModal(id = null) {
            document.getElementById('productForm').reset();
            const editModeInput = document.getElementById('editMode');
            const maInput = document.getElementById('Masp');

            if (id) {
                document.getElementById('modalTitle').innerText = 'Sửa Sản phẩm';
                const p = allProducts.find(x => x.Masp === id);
                if (p) {
                    editModeInput.value = "true";
                    maInput.value = p.Masp;
                    maInput.readOnly = true;
                    document.getElementById('Tensp').value = p.Tensp;
                    document.getElementById('Madm').value = p.Madm;
                    document.getElementById('Dvt').value = p.Dvt;
                    document.getElementById('Giaban').value = p.Giaban;
                }
            } else {
                document.getElementById('modalTitle').innerText = 'Thêm Sản phẩm';
                editModeInput.value = "false";
                maInput.readOnly = false;
            }
            modal.show();
        }

        async function saveProduct() {
            const headers = getHeaders();
            if(!headers) return;
            const isEdit = document.getElementById('editMode').value === "true";
            const id = document.getElementById('Masp').value;
            const body = {
                Masp: id,
                Tensp: document.getElementById('Tensp').value,
                Madm: document.getElementById('Madm').value,
                Dvt: document.getElementById('Dvt').value,
                Giaban: document.getElementById('Giaban').value
            };

            if(!body.Masp || !body.Tensp || !body.Madm) return alert('Vui lòng điền đủ thông tin bắt buộc!');

            try {
                const url = isEdit ? `${API}/products/${id}` : `${API}/products`;
                const method = isEdit ? 'PUT' : 'POST';
                
                const res = await fetch(url, { method, headers, body: JSON.stringify(body) });
                const data = await res.json();
                
                if(data.success) {
                    alert('Lưu thành công!');
                    modal.hide();
                    loadData();
                } else {
                    alert('Lỗi: ' + data.message);
                }
            } catch(err) {
                alert('Lỗi kết nối API');
            }
        }

        async function deleteProduct(id) {
            if(!confirm(`Xác nhận xóa sản phẩm ${id}?`)) return;
            const headers = getHeaders();
            if(!headers) return;
            try {
                const res = await fetch(`${API}/products/${id}`, { method: 'DELETE', headers });
                const data = await res.json();
                if(data.success) {
                    alert('Đã xóa!');
                    loadData();
                } else alert('Lỗi: ' + data.message);
            } catch(err) { alert('Lỗi kết nối API'); }
        }

        loadData();
    </script>
</body>
</html>