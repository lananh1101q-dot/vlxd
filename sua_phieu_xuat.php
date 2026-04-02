<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Phiếu Xuất - VLXD</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .sidebar { background: linear-gradient(180deg, #198754, #0f5132); height: 100vh; position: fixed; width: 250px; color: white; padding-top: 20px; z-index: 1000; }
        .sidebar .nav-link { color: white !important; padding: 12px 20px; border-radius: 6px; margin: 4px 10px; transition: all 0.3s; }
        .sidebar .nav-link:hover { background: rgba(255,255,255,0.1); transform: translateX(5px); }
        .main-content { margin-left: 250px; padding: 30px; min-height: 100vh; }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.05); }
        .form-label { font-weight: 600; color: #495057; font-size: 0.9rem; }
        .table thead { background-color: #f8f9fa; }
        .btn-remove { color: #dc3545; cursor: pointer; }
        .readonly-id { background-color: #e9ecef !important; font-family: monospace; font-weight: bold; }
    </style>
</head>
<body>
    <nav class="sidebar">
        <div class="text-center mb-4"><h4><i class="fas fa-warehouse"></i> Quản Lý Kho</h4></div>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="trangchu.php"><i class="fas fa-home me-2"></i>Trang Chủ</a></li>
            <li class="nav-item"><a class="nav-link" href="danh_sach_phieu_xuat.php"><i class="fas fa-list me-2"></i>Danh sách phiếu xuất</a></li>
            <li class="nav-item"><hr class="bg-white opacity-25"></li>
            <li class="nav-item"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
        </ul>
    </nav>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark">Sửa Phiếu Xuất Kho</h2>
            <a href="danh_sach_phieu_xuat.php" class="btn btn-outline-secondary px-4"><i class="fas fa-arrow-left me-2"></i>Quay lại</a>
        </div>

        <div class="card p-4">
            <form id="editForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Mã Phiếu Xuất</label>
                        <input type="text" id="Maxuathang" class="form-control readonly-id" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Khách Hàng *</label>
                        <select id="Makh" class="form-select" required></select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kho Xuất *</label>
                        <select id="Makho" class="form-select" required></select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ngày Xuất *</label>
                        <input type="date" id="Ngayxuat" class="form-control" required>
                    </div>
                    <div class="col-12 mt-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea id="Ghichu" class="form-control" rows="2"></textarea>
                    </div>
                </div>

                <div class="mt-5 mb-3 d-flex justify-content-between align-items-end">
                    <h5 class="fw-bold mb-0 text-success"><i class="fas fa-dolly me-2"></i>Hàng hóa xuất kho</h5>
                    <button type="button" class="btn btn-outline-success btn-sm px-3" onclick="addRow()">
                        <i class="fas fa-plus me-1"></i>Thêm sản phẩm
                    </button>
                </div>

                <div class="table-responsive border rounded">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-secondary">
                            <tr>
                                <th width="40%">Sản phẩm / Hàng hóa</th>
                                <th width="20%">Số lượng</th>
                                <th width="25%">Đơn giá xuất</th>
                                <th width="10%" class="text-center">Xóa</th>
                            </tr>
                        </thead>
                        <tbody id="detailList"></tbody>
                    </table>
                </div>

                <div class="text-end mt-5">
                    <button type="button" class="btn btn-success btn-lg px-5 fw-bold shadow-sm" onclick="submitEdit()">
                        <i class="fas fa-check-circle me-2"></i>Cập nhật Phiếu Xuất
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const API = 'http://localhost:8000/api/v1';
        const headers = { 'Authorization': 'Bearer ' + localStorage.getItem('token'), 'Content-Type': 'application/json' };
        const urlParams = new URLSearchParams(window.location.search);
        const receiptId = urlParams.get('id');

        let products = [];

        async function init() {
            if (!receiptId) return window.location.href = 'danh_sach_phieu_xuat.php';
            
            try {
                const [resP, resK, resW] = await Promise.all([
                    fetch(API + '/products', { headers }),
                    fetch(API + '/customers', { headers }),
                    fetch(API + '/warehouses', { headers })
                ]);

                const dataP = await resP.json();
                const dataK = await resK.json();
                const dataW = await resW.json();

                products = dataP.data.products;
                
                // Populate Customers
                const selK = document.getElementById('Makh');
                selK.innerHTML = '<option value="">-- Chọn Khách hàng --</option>';
                dataK.data.customers.forEach(k => selK.innerHTML += `<option value="${k.Makh}">${k.Tenkh}</option>`);

                // Populate Warehouses
                const selW = document.getElementById('Makho');
                selW.innerHTML = '<option value="">-- Chọn Kho --</option>';
                dataW.data.warehouses.forEach(w => selW.innerHTML += `<option value="${w.Makho}">${w.Tenkho}</option>`);

                // Load receipt data
                const resR = await fetch(`${API}/export-receipts/${receiptId}`, { headers });
                const dataR = await resR.json();

                if (dataR.success) {
                    const r = dataR.data.receipt;
                    document.getElementById('Maxuathang').value = r.Maxuathang;
                    document.getElementById('Makh').value = r.Makh;
                    document.getElementById('Makho').value = r.Makho || '';
                    document.getElementById('Ngayxuat').value = r.Ngayxuat;
                    document.getElementById('Ghichu').value = r.Ghichu || '';

                    const tbody = document.getElementById('detailList');
                    tbody.innerHTML = '';
                    r.details.forEach(it => addRow(it));
                }

            } catch (err) {
                alert('Lỗi tải dữ liệu');
            }
        }

        function addRow(data = null) {
            const tbody = document.getElementById('detailList');
            const tr = document.createElement('tr');
            
            let options = products.map(p => `<option value="${p.Masp}" ${data && data.Masp === p.Masp ? 'selected' : ''}>${p.Masp} - ${p.Tensp}</option>`).join('');
            
            tr.innerHTML = `
                <td><select class="form-select select-sp">${options}</select></td>
                <td><input type="number" class="form-control text-center input-sl" value="${data ? data.Soluong : 1}" min="1"></td>
                <td><input type="number" class="form-control text-end input-dg" value="${data ? data.Dongiaxuat : 0}" step="0.01"></td>
                <td class="text-center"><i class="fas fa-times-circle btn-remove" onclick="this.closest('tr').remove()"></i></td>
            `;
            tbody.appendChild(tr);
        }

        async function submitEdit() {
            const body = {
                Makh: document.getElementById('Makh').value,
                Makho: document.getElementById('Makho').value,
                Ngayxuat: document.getElementById('Ngayxuat').value,
                Ghichu: document.getElementById('Ghichu').value,
                details: []
            };

            if (!body.Makh || !body.Makho || !body.Ngayxuat) return alert('Vui lòng nhập đủ thông tin!');

            document.querySelectorAll('#detailList tr').forEach(tr => {
                body.details.push({
                    Masp: tr.querySelector('.select-sp').value,
                    Soluong: tr.querySelector('.input-sl').value,
                    Dongiaxuat: tr.querySelector('.input-dg').value
                });
            });

            if (body.details.length === 0) return alert('Phải có ít nhất một sản phẩm!');

            try {
                const res = await fetch(`${API}/export-receipts/${receiptId}`, {
                    method: 'PUT',
                    headers,
                    body: JSON.stringify(body)
                });
                const data = await res.json();
                if (data.success) {
                    alert('Cập nhật phiếu xuất thành công!');
                    window.location.href = 'danh_sach_phieu_xuat.php';
                } else {
                    alert('Lỗi: ' + data.message);
                }
            } catch (err) { alert('Lỗi kết nối API'); }
        }

        init();
    </script>
</body>
</html>