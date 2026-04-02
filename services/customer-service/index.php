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

    if (!$resource) {
        jsonResponse(true, 'Customer Service is running', ['version' => '1.0']);
    }

    // ===== CUSTOMERS (Khách hàng) =====
    if ($resource === 'customers') {
        if ($method === 'GET' && !$id) {
            $stmt = $pdo->query("SELECT kh.*, lkh.Tenloaikh FROM Khachhang kh 
                LEFT JOIN Loaikhachhang lkh ON kh.Maloaikh = lkh.Maloaikh
                ORDER BY kh.Makh");
            jsonResponse(true, 'Customers retrieved', ['customers' => $stmt->fetchAll()]);
        }
        else if ($method === 'GET' && $id) {
            $stmt = $pdo->prepare("SELECT kh.*, lkh.Tenloaikh FROM Khachhang kh
                LEFT JOIN Loaikhachhang lkh ON kh.Maloaikh = lkh.Maloaikh
                WHERE kh.Makh = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            if ($row) jsonResponse(true, 'Customer found', $row);
            else jsonResponse(false, 'Customer not found', null, 404);
        }
        else if ($method === 'POST') {
            $body = getBody();
            $stmt = $pdo->prepare("INSERT INTO Khachhang (Makh, Tenkh, Sdtkh, Diachikh, Maloaikh) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$body['Makh'], $body['Tenkh'], $body['Sdtkh'] ?? '', $body['Diachikh'] ?? '', $body['Maloaikh'] ?? null]);
            jsonResponse(true, 'Customer created', ['id' => $body['Makh']], 201);
        }
        else if ($method === 'PUT' && $id) {
            $body = getBody();
            $stmt = $pdo->prepare("UPDATE Khachhang SET Tenkh=?, Sdtkh=?, Diachikh=?, Maloaikh=? WHERE Makh=?");
            $stmt->execute([$body['Tenkh'], $body['Sdtkh'] ?? '', $body['Diachikh'] ?? '', $body['Maloaikh'] ?? null, $id]);
            jsonResponse(true, 'Customer updated');
        }
        else if ($method === 'DELETE' && $id) {
            $pdo->prepare("DELETE FROM Khachhang WHERE Makh=?")->execute([$id]);
            jsonResponse(true, 'Customer deleted');
        }
    }

    // ===== CUSTOMER-TYPES (Loại khách hàng) =====
    else if ($resource === 'customer-types') {
        if ($method === 'GET' && !$id) {
            $stmt = $pdo->query("SELECT * FROM Loaikhachhang ORDER BY Maloaikh");
            jsonResponse(true, 'Customer types retrieved', ['types' => $stmt->fetchAll()]);
        }
        else if ($method === 'GET' && $id) {
            $stmt = $pdo->prepare("SELECT * FROM Loaikhachhang WHERE Maloaikh = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            if ($row) jsonResponse(true, 'Type found', $row);
            else jsonResponse(false, 'Type not found', null, 404);
        }
        else if ($method === 'POST') {
            $body = getBody();
            $stmt = $pdo->prepare("INSERT INTO Loaikhachhang (Maloaikh, Tenloaikh, Mota) VALUES (?, ?, ?)");
            $stmt->execute([$body['Maloaikh'], $body['Tenloaikh'], $body['Mota'] ?? '']);
            jsonResponse(true, 'Customer type created', ['id' => $body['Maloaikh']], 201);
        }
        else if ($method === 'PUT' && $id) {
            $body = getBody();
            $stmt = $pdo->prepare("UPDATE Loaikhachhang SET Tenloaikh=?, Mota=? WHERE Maloaikh=?");
            $stmt->execute([$body['Tenloaikh'], $body['Mota'] ?? '', $id]);
            jsonResponse(true, 'Customer type updated');
        }
        else if ($method === 'DELETE' && $id) {
            $pdo->prepare("DELETE FROM Loaikhachhang WHERE Maloaikh=?")->execute([$id]);
            jsonResponse(true, 'Customer type deleted');
        }
    }

    jsonResponse(false, 'Endpoint not found in Customer Service', null, 404);
} catch (Exception $e) {
    jsonResponse(false, 'Server Error: ' . $e->getMessage(), null, 500);
}
