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
            $stmt = $pdo->prepare("INSERT INTO Lenhsanxuat (Malenh, Masp, Ngaysanxuat, Soluongsanxuat, Trangthai, Ngaybatdau, Ngayketthuc) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $malenh,
                $body['Masp'],
                $body['Ngaysanxuat'] ?? date('Y-m-d'),
                $body['Soluongsanxuat'],
                $body['Trangthai'] ?? 'cho_xu_ly',
                $body['Ngaybatdau'] ?? null,
                $body['Ngayketthuc'] ?? null
            ]);
            jsonResponse(true, 'Production order created', ['id' => $malenh], 201);
        }
        else if ($method === 'PUT' && $id) {
            $body = getBody();
            // Handle status update sub-resource or full update
            if (isset($body['Trangthai']) && count($body) === 1) {
                $stmt = $pdo->prepare("UPDATE Lenhsanxuat SET Trangthai=? WHERE Malenh=?");
                $stmt->execute([$body['Trangthai'], $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE Lenhsanxuat SET Masp=?, Ngaysanxuat=?, Soluongsanxuat=?, Trangthai=?, Ngaybatdau=?, Ngayketthuc=? WHERE Malenh=?");
                $stmt->execute([
                    $body['Masp'],
                    $body['Ngaysanxuat'],
                    $body['Soluongsanxuat'],
                    $body['Trangthai'] ?? 'cho_xu_ly',
                    $body['Ngaybatdau'] ?? null,
                    $body['Ngayketthuc'] ?? null,
                    $id
                ]);
            }
            jsonResponse(true, 'Production order updated');
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

        $pdo->beginTransaction();
        try {
            // Cập nhật trạng thái
            $pdo->prepare("UPDATE Lenhsanxuat SET Trangthai='hoan_thanh', Ngayketthuc=? WHERE Malenh=?")
                ->execute([date('Y-m-d'), $malenh]);

            // Nhập thành phẩm vào tồn kho
            $masp = $order['Masp'];
            $sl   = $order['Soluongsanxuat'];
            $chk  = $pdo->prepare("SELECT 1 FROM Tonkho_sp WHERE Makho=? AND Masp=?");
            $chk->execute([$makho, $masp]);
            if ($chk->fetchColumn()) {
                $pdo->prepare("UPDATE Tonkho_sp SET Soluongton = Soluongton + ? WHERE Makho=? AND Masp=?")
                    ->execute([$sl, $makho, $masp]);
            } else {
                $pdo->prepare("INSERT INTO Tonkho_sp (Makho, Masp, Soluongton) VALUES (?, ?, ?)")
                    ->execute([$makho, $masp, $sl]);
            }

            $pdo->commit();
            jsonResponse(true, 'Sản xuất hoàn thành và đã nhập kho', ['Malenh' => $malenh]);
        } catch (Exception $e) {
            $pdo->rollBack();
            jsonResponse(false, 'Lỗi: ' . $e->getMessage(), null, 400);
        }
    }

    jsonResponse(false, 'Endpoint not found in Manufacturing Service', null, 404);
} catch (Exception $e) {
    jsonResponse(false, 'Server Error: ' . $e->getMessage(), null, 500);
}
