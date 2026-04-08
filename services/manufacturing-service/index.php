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
    $action   = $parts[2] ?? null;

    if (!$resource) {
        jsonResponse(true, 'Manufacturing Service is running', ['version' => '1.0']);
    }

    // ===== PRODUCTION ORDERS (Lệnh sản xuất) =====
    if ($resource === 'production-orders') {
        if ($method === 'GET' && !$id) {
            $stmt = $pdo->query("SELECT ls.*, sp.Tensp FROM Lenhsanxuat ls
                LEFT JOIN vlxd_product.Sanpham sp ON ls.Masp = sp.Masp
                ORDER BY ls.Ngaysanxuat DESC");
            jsonResponse(true, 'Production orders retrieved', ['orders' => $stmt->fetchAll()]);
        }
        else if ($method === 'GET' && $id) {
            $stmt = $pdo->prepare("SELECT ls.*, sp.Tensp FROM Lenhsanxuat ls
                LEFT JOIN vlxd_product.Sanpham sp ON ls.Masp = sp.Masp
                WHERE ls.Malenh = ?");
            $stmt->execute([$id]);
            $order = $stmt->fetch();
            if (!$order) jsonResponse(false, 'Order not found', null, 404);
            jsonResponse(true, 'Order found', $order);
        }
        else if ($method === 'POST') {
            $body = getBody();
            $malenh = $body['Malenh'] ?? ('LSX' . time());
            $stmt = $pdo->prepare("INSERT INTO Lenhsanxuat (Malenh, Masp, Ngaysanxuat, Soluongsanxuat, Trangthai, Ngaybatdau, Ngayketthuc, Ghichu) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $malenh,
                $body['Masp'],
                $body['Ngaysanxuat'] ?? date('Y-m-d'),
                $body['Soluongsanxuat'],
                $body['Trangthai'] ?? 'cho_xu_ly',
                $body['Ngaybatdau'] ?? null,
                $body['Ngayketthuc'] ?? null,
                $body['Ghichu'] ?? null
            ]);
            jsonResponse(true, 'Production order created', ['id' => $malenh], 201);
        }
        else if ($method === 'PUT' && $id) {
            $body = getBody();
            // Handle status update sub-resource or full update
            if (isset($body['Trangthai']) && count($body) === 1) {
                $stmt = $pdo->prepare("UPDATE Lenhsanxuat SET Trangthai=? WHERE Malenh=?");
                $stmt->execute([$body['Trangthai'], $id]);
                jsonResponse(true, 'Production order status updated');
            } else {
                // Get existing data to merge
                $stmt = $pdo->prepare("SELECT * FROM Lenhsanxuat WHERE Malenh = ?");
                $stmt->execute([$id]);
                $existing = $stmt->fetch();
                if (!$existing) jsonResponse(false, 'Order not found', null, 404);

                $masp = $body['Masp'] ?? $existing['Masp'];
                $ngay = $body['Ngaysanxuat'] ?? $existing['Ngaysanxuat'];
                $sl   = $body['Soluongsanxuat'] ?? $existing['Soluongsanxuat'];
                $tt   = $body['Trangthai'] ?? $existing['Trangthai'];
                $nbd  = $body['Ngaybatdau'] ?? $existing['Ngaybatdau'];
                $nkt  = $body['Ngayketthuc'] ?? $existing['Ngayketthuc'];
                $gc   = $body['Ghichu'] ?? $existing['Ghichu'];

                $stmt = $pdo->prepare("UPDATE Lenhsanxuat SET Masp=?, Ngaysanxuat=?, Soluongsanxuat=?, Trangthai=?, Ngaybatdau=?, Ngayketthuc=?, Ghichu=? WHERE Malenh=?");
                $stmt->execute([$masp, $ngay, $sl, $tt, $nbd, $nkt, $gc, $id]);
                jsonResponse(true, 'Production order updated');
            }
        }
        else if ($method === 'DELETE' && $id) {
            $pdo->prepare("DELETE FROM Lenhsanxuat WHERE Malenh=?")->execute([$id]);
            jsonResponse(true, 'Production order deleted');
        }
    }

    // ===== COMPLETE PRODUCTION (Hoàn thành sản xuất - nhập SP vào kho) =====
    else if ($resource === 'complete-production' && $method === 'POST') {
        $body  = getBody();
        $malenh = $body['Malenh'];
        $makho  = $body['Makho'];

        // Lấy thông tin lệnh sản xuất
        $stmt = $pdo->prepare("SELECT * FROM Lenhsanxuat WHERE Malenh = ?");
        $stmt->execute([$malenh]);
        $order = $stmt->fetch();
        if (!$order) jsonResponse(false, 'Lệnh sản xuất không tìm thấy', null, 404);
        if ($order['Trangthai'] === 'hoan_thanh') jsonResponse(false, 'Lệnh này đã hoàn thành trước đó', null, 400);

        $pdo->beginTransaction();
        try {
            // 1. Cập nhật trạng thái lệnh
            $pdo->prepare("UPDATE Lenhsanxuat SET Trangthai='hoan_thanh', Ngayketthuc=? WHERE Malenh=?")
                ->execute([date('Y-m-d'), $malenh]);

            $masp = $order['Masp'];
            $slsx = $order['Soluongsanxuat'];

            // 2. Ghi nhận tiêu hao nguyên vật liệu dựa trên công thức (BOM)
            $stmtRecipe = $pdo->prepare("SELECT Manvl, Soluong FROM vlxd_product.Congthucsanpham WHERE Masp = ?");
            $stmtRecipe->execute([$masp]);
            $recipes = $stmtRecipe->fetchAll();

            foreach ($recipes as $item) {
                $requiredQty = $item['Soluong'] * $slsx;
                
                // Lưu vào chi tiết tiêu hao
                $pdo->prepare("INSERT INTO Chitiet_XuatNVL_Sanxuat (Malenh, Manvl, Soluong) VALUES (?, ?, ?)")
                    ->execute([$malenh, $item['Manvl'], $requiredQty]);
                
                // TỰ ĐỘNG TRỪ TỒN KHO NVL (Theo yêu cầu user)
                // Giả định NVL được trừ từ kho mặc định hoặc kho được chọn (ở đây tối giản trừ trong bảng tonkho_nvl)
                $pdo->prepare("UPDATE vlxd_warehouse.tonkho_nvl SET Soluongton = Soluongton - ? WHERE Manvl = ?")
                    ->execute([$requiredQty, $item['Manvl']]);
            }

            // 3. Ghi nhận nhập thành phẩm
            $pdo->prepare("INSERT INTO Chitiet_Nhapsanpham_Sanxuat (Malenh, Makho, Masp, Soluong) VALUES (?, ?, ?, ?)")
                ->execute([$malenh, $makho, $masp, $slsx]);

            // 4. Nhập thành phẩm vào tồn kho sản phẩm
            $chk  = $pdo->prepare("SELECT 1 FROM vlxd_warehouse.tonkho_sp WHERE Makho=? AND Masp=?");
            $chk->execute([$makho, $masp]);
            if ($chk->fetchColumn()) {
                $pdo->prepare("UPDATE vlxd_warehouse.tonkho_sp SET Soluongton = Soluongton + ? WHERE Makho=? AND Masp=?")
                    ->execute([$slsx, $makho, $masp]);
            } else {
                $pdo->prepare("INSERT INTO vlxd_warehouse.tonkho_sp (Makho, Masp, Soluongton) VALUES (?, ?, ?)")
                    ->execute([$makho, $masp, $slsx]);
            }

            $pdo->commit();
            jsonResponse(true, 'Sản xuất hoàn thành, đã ghi nhận chi tiết tiêu hao và nhập kho', ['Malenh' => $malenh]);
        } catch (Exception $e) {
            $pdo->rollBack();
            jsonResponse(false, 'Lỗi: ' . $e->getMessage(), null, 400);
        }
    }

    jsonResponse(false, 'Endpoint not found in Manufacturing Service', null, 404);
} catch (Exception $e) {
    jsonResponse(false, 'Server Error: ' . $e->getMessage(), null, 500);
}
