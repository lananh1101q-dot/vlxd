<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = str_replace('/api/v1', '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$parts = array_values(array_filter(explode('/', $path)));

function jsonResponse($success, $message, $data = null, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    exit;
}

function getBody() {
    return json_decode(file_get_contents('php://input'), true) ?? $_POST;
}

try {
    $resource = $parts[0] ?? null;
    $id       = $parts[1] ?? null;
    $subAction = $parts[2] ?? null;   // e.g. "execute" in /transfers/:id/execute

    if (!$resource) {
        jsonResponse(true, 'Warehouse Service is running', ['version' => '1.0']);
    }

    // ===== WAREHOUSES (Danh sách kho) =====
    if ($resource === 'warehouses') {
        if ($method === 'GET') {
            $stmt = $pdo->query("SELECT Makho, Tenkho, Diachi FROM Kho ORDER BY Makho");
            jsonResponse(true, 'Warehouses retrieved', ['warehouses' => $stmt->fetchAll()]);
        }
        if ($method === 'POST') {
            $body = getBody();
            $stmt = $pdo->prepare("INSERT INTO Kho (Makho, Tenkho, Diachi) VALUES (?, ?, ?)");
            $stmt->execute([$body['Makho'], $body['Tenkho'], $body['Diachi'] ?? '']);
            jsonResponse(true, 'Warehouse created', ['id' => $body['Makho']], 201);
        }
    }

    // ===== INVENTORY (Tồn kho) =====
    else if ($resource === 'inventory') {
        if ($method === 'GET') {
            // Tồn kho sản phẩm
            $sp = $pdo->query("SELECT tk.Makho, k.Tenkho, tk.Masp, sp.Tensp, sp.Dvt, tk.Soluongton
                FROM Tonkho_sp tk
                JOIN Kho k ON tk.Makho = k.Makho
                JOIN vlxd_product.Sanpham sp ON tk.Masp = sp.Masp
                ORDER BY k.Tenkho, sp.Tensp")->fetchAll();
            // Tồn kho NVL
            $nvl = $pdo->query("SELECT tk.Makho, k.Tenkho, tk.Manvl, nvl.Tennvl, nvl.Dvt, tk.Soluongton
                FROM Tonkho_nvl tk
                JOIN Kho k ON tk.Makho = k.Makho
                JOIN vlxd_product.Nguyenvatlieu nvl ON tk.Manvl = nvl.Manvl
                ORDER BY k.Tenkho, nvl.Tennvl")->fetchAll();
            jsonResponse(true, 'Inventory retrieved', ['products' => $sp, 'materials' => $nvl]);
        }
    }

    // ===== IMPORT RECEIPTS (Phiếu nhập kho) =====
    else if ($resource === 'import-receipts') {
        if ($method === 'GET' && !$id) {
            $stmt = $pdo->query("SELECT pn.*, ncc.Tenncc, k.Tenkho,
                (SELECT COUNT(*) FROM Chitiet_Phieunhap WHERE Manhaphang = pn.Manhaphang) as SoMatHang
                FROM Phieunhap pn
                LEFT JOIN vlxd_product.Nhacungcap ncc ON pn.Mancc = ncc.Mancc
                LEFT JOIN Kho k ON pn.Makho = k.Makho
                ORDER BY pn.Ngaynhaphang DESC");
            jsonResponse(true, 'Import receipts retrieved', ['receipts' => $stmt->fetchAll()]);
        }
        else if ($method === 'GET' && $id) {
            $stmt = $pdo->prepare("SELECT pn.*, ncc.Tenncc, k.Tenkho FROM Phieunhap pn
                LEFT JOIN vlxd_product.Nhacungcap ncc ON pn.Mancc = ncc.Mancc
                LEFT JOIN Kho k ON pn.Makho = k.Makho
                WHERE pn.Manhaphang = ?");
            $stmt->execute([$id]);
            $receipt = $stmt->fetch();
            if (!$receipt) jsonResponse(false, 'Receipt not found', null, 404);

            $stmtD = $pdo->prepare("SELECT ct.*, nvl.Tennvl, nvl.Dvt FROM Chitiet_Phieunhap ct
                LEFT JOIN vlxd_product.Nguyenvatlieu nvl ON ct.Manvl = nvl.Manvl
                WHERE ct.Manhaphang = ?");
            $stmtD->execute([$id]);
            $receipt['details'] = $stmtD->fetchAll();
            jsonResponse(true, 'Receipt details retrieved', ['receipt' => $receipt]);
        }
        else if ($method === 'POST') {
            $body = getBody();
            $maPN   = $body['Manhaphang'] ?? ('PN' . time());
            $mancc  = $body['Mancc'];
            $makho  = $body['Makho'];
            $ngay   = $body['Ngaynhaphang'] ?? date('Y-m-d');
            $ghichu = $body['Ghichu'] ?? '';
            $details = $body['details'] ?? [];

            if (empty($details)) jsonResponse(false, 'Vui lòng thêm chi tiết phiếu nhập', null, 400);

            $pdo->beginTransaction();
            try {
                $tongTien = 0;
                foreach ($details as $d) {
                    $tongTien += ($d['Soluong'] ?? 0) * ($d['Dongianhap'] ?? 0);
                }
                $stmt = $pdo->prepare("INSERT INTO Phieunhap (Manhaphang, Mancc, Makho, Ngaynhaphang, Tongtiennhap, Ghichu) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$maPN, $mancc, $makho, $ngay, $tongTien, $ghichu]);

                $stmtDet = $pdo->prepare("INSERT INTO Chitiet_Phieunhap (Manhaphang, Manvl, Soluong, Dongianhap) VALUES (?, ?, ?, ?)");
                $stmtChk = $pdo->prepare("SELECT 1 FROM Tonkho_nvl WHERE Makho = ? AND Manvl = ?");
                $stmtUpd = $pdo->prepare("UPDATE Tonkho_nvl SET Soluongton = Soluongton + ? WHERE Makho = ? AND Manvl = ?");
                $stmtIns = $pdo->prepare("INSERT INTO Tonkho_nvl (Makho, Manvl, Soluongton) VALUES (?, ?, ?)");

                foreach ($details as $d) {
                    $stmtDet->execute([$maPN, $d['Manvl'], $d['Soluong'], $d['Dongianhap'] ?? 0]);
                    $stmtChk->execute([$makho, $d['Manvl']]);
                    if ($stmtChk->fetchColumn()) {
                        $stmtUpd->execute([$d['Soluong'], $makho, $d['Manvl']]);
                    } else {
                        $stmtIns->execute([$makho, $d['Manvl'], $d['Soluong']]);
                    }
                }
                $pdo->commit();
                jsonResponse(true, 'Import receipt created', ['id' => $maPN], 201);
            } catch (Exception $e) {
                $pdo->rollBack();
                jsonResponse(false, 'Lỗi tạo phiếu nhập: ' . $e->getMessage(), null, 400);
            }
        }
        else if ($method === 'PUT' && $id) {
            $body = getBody();
            $mancc  = $body['Mancc'];
            $makho  = $body['Makho'];
            $ngay   = $body['Ngaynhaphang'];
            $ghichu = $body['Ghichu'] ?? '';
            $details = $body['details'] ?? [];

            if (empty($details)) jsonResponse(false, 'Vui lòng thêm chi tiết phiếu nhập', null, 400);

            $pdo->beginTransaction();
            try {
                // 1. Lấy kho cũ và chi tiết cũ để hoàn tồn kho
                $oldPhieu = $pdo->prepare("SELECT Makho FROM Phieunhap WHERE Manhaphang = ?");
                $oldPhieu->execute([$id]);
                $oldMakho = $oldPhieu->fetchColumn();

                $oldDetails = $pdo->prepare("SELECT Manvl, Soluong FROM Chitiet_Phieunhap WHERE Manhaphang = ?");
                $oldDetails->execute([$id]);
                foreach ($oldDetails->fetchAll() as $row) {
                    $pdo->prepare("UPDATE Tonkho_nvl SET Soluongton = GREATEST(0, Soluongton - ?) WHERE Makho = ? AND Manvl = ?")
                        ->execute([$row['Soluong'], $oldMakho, $row['Manvl']]);
                }

                // 2. Xóa chi tiết cũ
                $pdo->prepare("DELETE FROM Chitiet_Phieunhap WHERE Manhaphang = ?")->execute([$id]);

                // 3. Cập nhật thông tin phiếu chính
                $tongTien = 0;
                foreach ($details as $d) $tongTien += ($d['Soluong'] ?? 0) * ($d['Dongianhap'] ?? 0);
                
                $stmt = $pdo->prepare("UPDATE Phieunhap SET Mancc = ?, Makho = ?, Ngaynhaphang = ?, Tongtiennhap = ?, Ghichu = ? WHERE Manhaphang = ?");
                $stmt->execute([$mancc, $makho, $ngay, $tongTien, $ghichu, $id]);

                // 4. Thêm chi tiết mới và cập nhật tồn kho mới
                $stmtDet = $pdo->prepare("INSERT INTO Chitiet_Phieunhap (Manhaphang, Manvl, Soluong, Dongianhap) VALUES (?, ?, ?, ?)");
                foreach ($details as $d) {
                    $stmtDet->execute([$id, $d['Manvl'], $d['Soluong'], $d['Dongianhap'] ?? 0]);
                    
                    $stmtChk = $pdo->prepare("SELECT 1 FROM Tonkho_nvl WHERE Makho = ? AND Manvl = ?");
                    $stmtChk->execute([$makho, $d['Manvl']]);
                    if ($stmtChk->fetchColumn()) {
                        $pdo->prepare("UPDATE Tonkho_nvl SET Soluongton = Soluongton + ? WHERE Makho = ? AND Manvl = ?")
                            ->execute([$d['Soluong'], $makho, $d['Manvl']]);
                    } else {
                        $pdo->prepare("INSERT INTO Tonkho_nvl (Makho, Manvl, Soluongton) VALUES (?, ?, ?)")
                            ->execute([$makho, $d['Manvl'], $d['Soluong']]);
                    }
                }

                $pdo->commit();
                jsonResponse(true, 'Import receipt updated');
            } catch (Exception $e) {
                $pdo->rollBack();
                jsonResponse(false, 'Lỗi cập nhật: ' . $e->getMessage(), null, 400);
            }
        }
        else if ($method === 'PUT' && $id) {
            $body = getBody();
            $makh    = $body['Makh'];
            $makho   = $body['Makho'];
            $ngay    = $body['Ngayxuat'];
            $ghichu  = $body['Ghichu'] ?? '';
            $details = $body['details'] ?? [];

            if (empty($details)) jsonResponse(false, 'Vui lòng thêm chi tiết phiếu xuất', null, 400);

            $pdo->beginTransaction();
            try {
                // 1. Lấy kho cũ và chi tiết cũ để hoàn tồn kho
                $oldPhieu = $pdo->prepare("SELECT Makho FROM Phieuxuat WHERE Maxuathang = ?");
                $oldPhieu->execute([$id]);
                $oldMakho = $oldPhieu->fetchColumn();

                $oldDetails = $pdo->prepare("SELECT Masp, Soluong FROM Chitiet_Phieuxuat WHERE Maxuathang = ?");
                $oldDetails->execute([$id]);
                foreach ($oldDetails->fetchAll() as $row) {
                    $pdo->prepare("UPDATE Tonkho_sp SET Soluongton = Soluongton + ? WHERE Makho = ? AND Masp = ?")
                        ->execute([$row['Soluong'], $oldMakho, $row['Masp']]);
                }

                // 2. Kiểm tra tồn kho mới trước khi thực hiện
                foreach ($details as $d) {
                    $chk = $pdo->prepare("SELECT Soluongton FROM Tonkho_sp WHERE Makho = ? AND Masp = ?");
                    $chk->execute([$makho, $d['Masp']]);
                    $ton = $chk->fetchColumn();
                    if ($ton === false || $ton < $d['Soluong']) {
                        throw new Exception("Sản phẩm {$d['Masp']} không đủ tồn kho tại kho mới (còn ".($ton?:0).")");
                    }
                }

                // 3. Xóa chi tiết cũ và cập nhật phiếu chính
                $pdo->prepare("DELETE FROM Chitiet_Phieuxuat WHERE Maxuathang = ?")->execute([$id]);
                
                $tongTien = 0;
                foreach ($details as $d) $tongTien += ($d['Soluong'] ?? 0) * ($d['Dongiaxuat'] ?? 0);
                
                $stmt = $pdo->prepare("UPDATE Phieuxuat SET Makh = ?, Makho = ?, Ngayxuat = ?, Tongtienxuat = ?, Ghichu = ? WHERE Maxuathang = ?");
                $stmt->execute([$makh, $makho, $ngay, $tongTien, $ghichu, $id]);

                // 4. Thêm chi tiết mới và trừ tồn kho
                $stmtDet = $pdo->prepare("INSERT INTO Chitiet_Phieuxuat (Maxuathang, Masp, Soluong, Dongiaxuat) VALUES (?, ?, ?, ?)");
                $stmtTru = $pdo->prepare("UPDATE Tonkho_sp SET Soluongton = Soluongton - ? WHERE Makho = ? AND Masp = ?");
                foreach ($details as $d) {
                    $stmtDet->execute([$id, $d['Masp'], $d['Soluong'], $d['Dongiaxuat'] ?? 0]);
                    $stmtTru->execute([$d['Soluong'], $makho, $d['Masp']]);
                }

                $pdo->commit();
                jsonResponse(true, 'Export receipt updated');
            } catch (Exception $e) {
                $pdo->rollBack();
                jsonResponse(false, 'Lỗi cập nhật: ' . $e->getMessage(), null, 400);
            }
        }
        else if ($method === 'DELETE' && $id) {
            $pdo->beginTransaction();
            try {
                // Lấy thông tin để hoàn tồn kho
                $phieu = $pdo->prepare("SELECT Makho FROM Phieunhap WHERE Manhaphang = ?");
                $phieu->execute([$id]);
                $phieuRow = $phieu->fetch();
                if ($phieuRow) {
                    $ct = $pdo->prepare("SELECT Manvl, Soluong FROM Chitiet_Phieunhap WHERE Manhaphang = ?");
                    $ct->execute([$id]);
                    foreach ($ct->fetchAll() as $row) {
                        $pdo->prepare("UPDATE Tonkho_nvl SET Soluongton = GREATEST(0, Soluongton - ?) WHERE Makho = ? AND Manvl = ?")
                            ->execute([$row['Soluong'], $phieuRow['Makho'], $row['Manvl']]);
                    }
                }
                $pdo->prepare("DELETE FROM Chitiet_Phieunhap WHERE Manhaphang = ?")->execute([$id]);
                $pdo->prepare("DELETE FROM Phieunhap WHERE Manhaphang = ?")->execute([$id]);
                $pdo->commit();
                jsonResponse(true, 'Import receipt deleted');
            } catch (Exception $e) {
                $pdo->rollBack();
                jsonResponse(false, 'Lỗi xóa phiếu: ' . $e->getMessage(), null, 400);
            }
        }
    }

    // ===== EXPORT RECEIPTS (Phiếu xuất kho) =====
    else if ($resource === 'export-receipts') {
        if ($method === 'GET' && !$id) {
            $stmt = $pdo->query("SELECT px.*, kh.Tenkh, k.Tenkho,
                (SELECT COUNT(*) FROM Chitiet_Phieuxuat WHERE Maxuathang = px.Maxuathang) as SoMatHang
                FROM Phieuxuat px
                LEFT JOIN vlxd_customer.Khachhang kh ON px.Makh = kh.Makh
                LEFT JOIN Kho k ON px.Makho = k.Makho
                ORDER BY px.Ngayxuat DESC");
            jsonResponse(true, 'Export receipts retrieved', ['receipts' => $stmt->fetchAll()]);
        }
        else if ($method === 'GET' && $id) {
            $stmt = $pdo->prepare("SELECT px.*, kh.Tenkh, k.Tenkho FROM Phieuxuat px
                LEFT JOIN vlxd_customer.Khachhang kh ON px.Makh = kh.Makh
                LEFT JOIN Kho k ON px.Makho = k.Makho
                WHERE px.Maxuathang = ?");
            $stmt->execute([$id]);
            $receipt = $stmt->fetch();
            if (!$receipt) jsonResponse(false, 'Export receipt not found', null, 404);

            $stmtD = $pdo->prepare("SELECT ct.*, sp.Tensp, sp.Dvt FROM Chitiet_Phieuxuat ct
                LEFT JOIN vlxd_product.Sanpham sp ON ct.Masp = sp.Masp
                WHERE ct.Maxuathang = ?");
            $stmtD->execute([$id]);
            $receipt['details'] = $stmtD->fetchAll();
            jsonResponse(true, 'Export receipt details', ['receipt' => $receipt]);
        }
        else if ($method === 'POST') {
            $body    = getBody();
            $mapx    = $body['Maxuathang'] ?? ('PX' . time());
            $makh    = $body['Makh'];
            $makho   = $body['Makho'];
            $ngay    = $body['Ngayxuat'] ?? date('Y-m-d');
            $ghichu  = $body['Ghichu'] ?? '';
            $details = $body['details'] ?? [];

            if (empty($details)) jsonResponse(false, 'Vui lòng thêm chi tiết phiếu xuất', null, 400);

            $pdo->beginTransaction();
            try {
                $tongTien = 0;
                foreach ($details as $d) {
                    $tongTien += ($d['Soluong'] ?? 0) * ($d['Dongiaxuat'] ?? 0);
                    // Kiểm tra tồn kho
                    $chk = $pdo->prepare("SELECT Soluongton FROM Tonkho_sp WHERE Makho = ? AND Masp = ?");
                    $chk->execute([$makho, $d['Masp']]);
                    $ton = $chk->fetchColumn();
                    if ($ton === false || $ton < $d['Soluong']) {
                        throw new Exception("Sản phẩm {$d['Masp']} không đủ tồn kho ({$ton}).");
                    }
                }

                $stmt = $pdo->prepare("INSERT INTO Phieuxuat (Maxuathang, Makh, Makho, Ngayxuat, Tongtienxuat, Ghichu, Trangthai) VALUES (?, ?, ?, ?, ?, ?, 'hoan_thanh')");
                $stmt->execute([$mapx, $makh, $makho, $ngay, $tongTien, $ghichu]);

                foreach ($details as $d) {
                    $pdo->prepare("INSERT INTO Chitiet_Phieuxuat (Maxuathang, Masp, Soluong, Dongiaxuat) VALUES (?, ?, ?, ?)")
                        ->execute([$mapx, $d['Masp'], $d['Soluong'], $d['Dongiaxuat'] ?? 0]);
                    $pdo->prepare("UPDATE Tonkho_sp SET Soluongton = Soluongton - ? WHERE Makho = ? AND Masp = ?")
                        ->execute([$d['Soluong'], $makho, $d['Masp']]);
                }
                $pdo->commit();
                jsonResponse(true, 'Export receipt created', ['id' => $mapx], 201);
            } catch (Exception $e) {
                $pdo->rollBack();
                jsonResponse(false, 'Lỗi tạo phiếu xuất: ' . $e->getMessage(), null, 400);
            }
        }
        else if ($method === 'DELETE' && $id) {
            $pdo->beginTransaction();
            try {
                $phieu = $pdo->prepare("SELECT Makho FROM Phieuxuat WHERE Maxuathang = ?");
                $phieu->execute([$id]);
                $phieuRow = $phieu->fetch();
                if ($phieuRow) {
                    $ct = $pdo->prepare("SELECT Masp, Soluong FROM Chitiet_Phieuxuat WHERE Maxuathang = ?");
                    $ct->execute([$id]);
                    foreach ($ct->fetchAll() as $row) {
                        $pdo->prepare("UPDATE Tonkho_sp SET Soluongton = Soluongton + ? WHERE Makho = ? AND Masp = ?")
                            ->execute([$row['Soluong'], $phieuRow['Makho'], $row['Masp']]);
                    }
                }
                $pdo->prepare("DELETE FROM Chitiet_Phieuxuat WHERE Maxuathang = ?")->execute([$id]);
                $pdo->prepare("DELETE FROM Phieuxuat WHERE Maxuathang = ?")->execute([$id]);
                $pdo->commit();
                jsonResponse(true, 'Export receipt deleted');
            } catch (Exception $e) {
                $pdo->rollBack();
                jsonResponse(false, 'Lỗi xóa phiếu: ' . $e->getMessage(), null, 400);
            }
        }
    }

    // ===== TRANSFERS (Phiếu điều chuyển) =====
    else if ($resource === 'transfers') {
        if ($method === 'GET' && !$id) {
            $stmt = $pdo->query("SELECT p.Madieuchuyen, kx.Tenkho as TenKhoxuat, kn.Tenkho as TenKhonhap, 
                p.Ngaydieuchuyen, p.Ghichu,
                (SELECT COUNT(*) FROM Chitiet_Phieudieuchuyen WHERE Madieuchuyen = p.Madieuchuyen) as SoMatHang
                FROM Phieudieuchuyen p
                JOIN Kho kx ON p.Khoxuat = kx.Makho
                JOIN Kho kn ON p.Khonhap = kn.Makho
                ORDER BY p.Ngaydieuchuyen DESC");
            jsonResponse(true, 'Transfers retrieved', ['transfers' => $stmt->fetchAll()]);
        }
        else if ($method === 'GET' && $id) {
            $stmt = $pdo->prepare("SELECT p.*, kx.Tenkho as TenKhoxuat, kn.Tenkho as TenKhonhap 
                FROM Phieudieuchuyen p
                JOIN Kho kx ON p.Khoxuat = kx.Makho
                JOIN Kho kn ON p.Khonhap = kn.Makho
                WHERE p.Madieuchuyen = ?");
            $stmt->execute([$id]);
            $master = $stmt->fetch();
            if (!$master) jsonResponse(false, 'Transfer not found', null, 404);
            $stmtD = $pdo->prepare("SELECT c.*, sp.Tensp, sp.Dvt FROM Chitiet_Phieudieuchuyen c 
                LEFT JOIN vlxd_product.Sanpham sp ON c.Masp = sp.Masp WHERE c.Madieuchuyen = ?");
            $stmtD->execute([$id]);
            $master['details'] = $stmtD->fetchAll();
            jsonResponse(true, 'Transfer details retrieved', ['transfer' => $master]);
        }
        else if ($method === 'POST') {
            $body    = getBody();
            $madc    = $body['Madieuchuyen'] ?? ('DC' . time());
            $khoxuat = $body['Khoxuat'];
            $khonhap = $body['Khonhap'];
            $ngay    = $body['Ngaydieuchuyen'] ?? date('Y-m-d');
            $ghichu  = $body['Ghichu'] ?? '';
            $details = $body['details'] ?? [];

            if ($khoxuat === $khonhap) jsonResponse(false, 'Kho xuất và nhập không được trùng nhau', null, 400);

            $pdo->beginTransaction();
            try {
                $pdo->prepare("INSERT INTO Phieudieuchuyen (Madieuchuyen, Khoxuat, Khonhap, Ngaydieuchuyen, Ghichu) VALUES (?, ?, ?, ?, ?)")
                    ->execute([$madc, $khoxuat, $khonhap, $ngay, $ghichu]);

                foreach ($details as $d) {
                    $masp = $d['Masp'];
                    $sl   = $d['Soluong'];
                    $chk  = $pdo->prepare("SELECT Soluongton FROM Tonkho_sp WHERE Makho = ? AND Masp = ?");
                    $chk->execute([$khoxuat, $masp]);
                    $ton = $chk->fetchColumn();
                    if ($ton === false || $ton < $sl) throw new Exception("Sản phẩm $masp không đủ tồn trong kho xuất.");
                    $pdo->prepare("INSERT INTO Chitiet_Phieudieuchuyen (Madieuchuyen, Masp, Soluong) VALUES (?, ?, ?)")->execute([$madc, $masp, $sl]);
                    $pdo->prepare("UPDATE Tonkho_sp SET Soluongton = Soluongton - ? WHERE Makho = ? AND Masp = ?")->execute([$sl, $khoxuat, $masp]);
                    $stmtChk = $pdo->prepare("SELECT 1 FROM Tonkho_sp WHERE Makho = ? AND Masp = ?");
                    $stmtChk->execute([$khonhap, $masp]);
                    if ($stmtChk->fetchColumn()) {
                        $pdo->prepare("UPDATE Tonkho_sp SET Soluongton = Soluongton + ? WHERE Makho = ? AND Masp = ?")->execute([$sl, $khonhap, $masp]);
                    } else {
                        $pdo->prepare("INSERT INTO Tonkho_sp (Makho, Masp, Soluongton) VALUES (?, ?, ?)")->execute([$khonhap, $masp, $sl]);
                    }
                }
                $pdo->commit();
                jsonResponse(true, 'Transfer completed successfully', ['id' => $madc], 201);
            } catch (Exception $e) {
                $pdo->rollBack();
                jsonResponse(false, 'Lỗi: ' . $e->getMessage(), null, 400);
            }
        }
        else if ($method === 'DELETE' && $id) {
            $pdo->prepare("DELETE FROM Chitiet_Phieudieuchuyen WHERE Madieuchuyen = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM Phieudieuchuyen WHERE Madieuchuyen = ?")->execute([$id]);
            jsonResponse(true, 'Transfer deleted');
        }
        // POST /transfers/:id/execute — thực hiện điều chuyển (cập nhật tồn kho)
        else if ($method === 'POST' && $id && $subAction === 'execute') {
            $stmt = $pdo->prepare("SELECT p.*, kx.Tenkho as TenKhoXuat, kn.Tenkho as TenKhoNhap
                FROM Phieudieuchuyen p
                JOIN Kho kx ON p.Khoxuat = kx.Makho
                JOIN Kho kn ON p.Khonhap = kn.Makho
                WHERE p.Madieuchuyen = ?");
            $stmt->execute([$id]);
            $phieu = $stmt->fetch();
            if (!$phieu) jsonResponse(false, 'Phiếu điều chuyển không tồn tại', null, 404);
            if (($phieu['Trangthai'] ?? '') === 'da_thuc_hien') jsonResponse(false, 'Phiếu đã được thực hiện rồi', null, 409);

            $stmtD = $pdo->prepare("SELECT Masp, Soluong FROM Chitiet_Phieudieuchuyen WHERE Madieuchuyen = ?");
            $stmtD->execute([$id]);
            $details = $stmtD->fetchAll();
            if (empty($details)) jsonResponse(false, 'Phiếu không có chi tiết sản phẩm', null, 400);

            $pdo->beginTransaction();
            try {
                foreach ($details as $d) {
                    $masp = $d['Masp']; $sl = $d['Soluong'];
                    $chk = $pdo->prepare("SELECT Soluongton FROM Tonkho_sp WHERE Makho = ? AND Masp = ?");
                    $chk->execute([$phieu['Khoxuat'], $masp]);
                    $ton = $chk->fetchColumn();
                    if ($ton === false || $ton < $sl) throw new Exception("Sản phẩm $masp không đủ tồn trong kho xuất (cần $sl, có ".($ton?:0).")");
                    $pdo->prepare("UPDATE Tonkho_sp SET Soluongton = Soluongton - ? WHERE Makho = ? AND Masp = ?")
                        ->execute([$sl, $phieu['Khoxuat'], $masp]);
                    $chkNhap = $pdo->prepare("SELECT 1 FROM Tonkho_sp WHERE Makho = ? AND Masp = ?");
                    $chkNhap->execute([$phieu['Khonhap'], $masp]);
                    if ($chkNhap->fetchColumn()) {
                        $pdo->prepare("UPDATE Tonkho_sp SET Soluongton = Soluongton + ? WHERE Makho = ? AND Masp = ?")
                            ->execute([$sl, $phieu['Khonhap'], $masp]);
                    } else {
                        $pdo->prepare("INSERT INTO Tonkho_sp (Makho, Masp, Soluongton) VALUES (?, ?, ?)")
                            ->execute([$phieu['Khonhap'], $masp, $sl]);
                    }
                }
                // Cập nhật trạng thái phiếu
                $pdo->prepare("UPDATE Phieudieuchuyen SET Trangthai = 'da_thuc_hien' WHERE Madieuchuyen = ?")
                    ->execute([$id]);
                $pdo->commit();
                jsonResponse(true, 'Điều chuyển thành công! Tồn kho đã được cập nhật.');
            } catch (Exception $e) {
                $pdo->rollBack();
                jsonResponse(false, 'Lỗi: ' . $e->getMessage(), null, 400);
            }
        }
    }

    jsonResponse(false, 'Endpoint not found in Warehouse Service', null, 404);
} catch (Exception $e) {
    jsonResponse(false, 'Server Error: ' . $e->getMessage(), null, 500);
}
