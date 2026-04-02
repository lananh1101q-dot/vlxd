<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Công Thức Sản Phẩm - VLXD</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .sidebar { background: linear-gradient(180deg, #6610f2, #4b0bb8); height: 100vh; position: fixed; width: 250px; color: white; padding-top: 20px; z-index: 1000; }
        .sidebar .nav-link { color: white !important; padding: 12px 20px; border-radius: 6px; margin: 4px 10px; transition: all 0.3s; }
        .sidebar .nav-link:hover { background: rgba(255,255,255,0.1); transform: translateX(5px); }
        .main-content { margin-left: 250px; padding: 30px; min-height: 100vh; }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .table thead { background-color: #f8f9fa; }
        .chu { font-weight: 700; color: #6610f2; }
        .badge-qty { font-size: 0.9rem; padding: 5px 12px; border-radius: 20px; }
    </style>
</head>
<body>
    <nav class="sidebar">
        <div class="text-center mb-4"><h4><i class="fas fa-warehouse"></i> Quản Lý Kho</h4></div>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="trangchu.php"><i class="fas fa-home me-2"></i>Trang Chủ</a></li>
            <li class="nav-item"><a class="nav-link" href="Sanpham.php"><i class="fas fa-cube me-2"></i>Sản phẩm</a></li>
            <li class="nav-item"><a class="nav-link" href="Nguyenvatlieu.php"><i class="fas fa-seedling me-2"></i>Nguyên vật liệu</a></li>
            <li class="nav-item"><a class="nav-link active" href="Congthucsanpham.php"><i class="fas fa-flask me-2"></i>Công thức sản phẩm</a></li>
            <li class="nav-item"><hr class="bg-white opacity-25"></li>
            <li class="nav-item"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
        </ul>
    </nav>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark">Định mức & Công thức</h2>
        </div>

        <div class="card mb-4 p-4">
            <h5 class="fw-bold mb-3"><i class="fas fa-plus-circle me-2 text-primary"></i>Thêm/Cập nhật định mức</h5>
            <form id="formulaForm" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Sản phẩm hoàn thiện</label>
                    <select id="Masp" class="form-select" required>
                        <option value="">-- Chọn sản phẩm --</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Nguyên vật liệu cần dùng</label>
                    <select id="Manvl" class="form-select" required>
                        <option value="">-- Chọn nguyên vật liệu --</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Số lượng</label>
                    <input type="number" id="Soluong" class="form-control" step="0.01" value="1" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary w-100 fw-bold" onclick="saveFormula()">
                        <i class="fas fa-save me-1"></i>Lưu định mức
                    </button>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Sản phẩm thành phẩm</th>
                                <th>Nguyên vật liệu thành phần</th>
                                <th class="text-center">Định mức (SL)</th>
                                <th>Đơn vị tính</th>
                                <th class="text-center pe-4">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="formulaList">
                            <tr><td colspan="5" class="text-center py-5 text-muted">Đang tải dữ liệu...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const API = 'http://localhost:8000/api/v1';
        const headers = { 'Authorization': 'Bearer ' + localStorage.getItem('token'), 'Content-Type': 'application/json' };

        async function loadDropdowns() {
            try {
                const [resP, resM] = await Promise.all([
                    fetch(API + '/products', { headers }),
                    fetch(API + '/materials', { headers })
                ]);
                const dataP = await resP.json();
                const dataM = await resM.json();

                const selP = document.getElementById('Masp');
                dataP.data.products.forEach(p => selP.innerHTML += `<option value="${p.Masp}">${p.Masp} - ${p.Tensp}</option>`);

                const selM = document.getElementById('Manvl');
                dataM.data.materials.forEach(m => selM.innerHTML += `<option value="${m.Manvl}">${m.Manvl} - ${m.Tennvl} (${m.Dvt})</option>`);
            } catch (err) { console.error('Lỗi tải dropdown'); }
        }

        async function loadFormulas() {
            try {
                const res = await fetch(API + '/formulas', { headers });
                const data = await res.json();
                const tbody = document.getElementById('formulaList');
                tbody.innerHTML = '';
                
                if (data.success && data.data.formulas) {
                    let lastMasp = '';
                    data.data.formulas.forEach(f => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="ps-4 ${f.Masp === lastMasp ? 'opacity-25' : 'fw-bold chu'}">${f.Masp === lastMasp ? '' : f.Tensp}</td>
                            <td><span class="text-primary fw-semibold">${f.Tennvl}</span></td>
                            <td class="text-center"><span class="badge bg-info bg-opacity-10 text-info badge-qty">${f.Soluong}</span></td>
                            <td><small class="text-muted">${f.Dvt || ''}</small></td>
                            <td class="text-center pe-4">
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteFormula('${f.Masp}', '${f.Manvl}')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                        lastMasp = f.Masp;
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Chưa có công thức nào</td></tr>';
                }
            } catch (err) {
                document.getElementById('formulaList').innerHTML = '<tr><td colspan="5" class="text-danger text-center py-4">Lỗi kết nối máy chủ</td></tr>';
            }
        }

        async function saveFormula() {
            const body = {
                Masp: document.getElementById('Masp').value,
                Manvl: document.getElementById('Manvl').value,
                Soluong: document.getElementById('Soluong').value
            };

            if (!body.Masp || !body.Manvl || !body.Soluong) return alert('Vui lòng chọn đủ thông tin!');

            try {
                const res = await fetch(API + '/formulas', {
                    method: 'POST',
                    headers,
                    body: JSON.stringify(body)
                });
                const data = await res.json();
                if (data.success) {
                    alert('Lưu thành công!');
                    loadFormulas();
                } else alert('Lỗi: ' + data.message);
            } catch (err) { alert('Lỗi API'); }
        }

        async function deleteFormula(masp, manvl) {
            if (!confirm(`Xóa thành phần này khỏi công thức?`)) return;
            try {
                // Sử dụng định dạng Masp_Manvl như đã quy định trong product-service
                const res = await fetch(`${API}/formulas/${masp}_${manvl}`, { method: 'DELETE', headers });
                const data = await res.json();
                if (data.success) loadFormulas();
                else alert('Lỗi: ' + data.message);
            } catch (err) { alert('Lỗi API'); }
        }

        loadDropdowns();
        loadFormulas();
    </script>
</body>
</html>
