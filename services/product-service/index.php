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
        jsonResponse(true, 'Product Service is running', ['version' => '1.0']);
    }

    // ===== PRODUCTS =====
    if ($resource === 'products') {
        if ($method === 'GET' && !$id) {
            $stmt = $pdo->query("SELECT t.Masp, t.Tensp, dm.Tendm, t.Madm, t.Dvt, t.Giaban
                FROM Sanpham t LEFT JOIN Danhmucsp dm ON t.Madm = dm.Madm ORDER BY t.Masp");
            jsonResponse(true, 'Products retrieved', ['products' => $stmt->fetchAll()]);
        }
        else if ($method === 'GET' && $id) {
            $stmt = $pdo->prepare("SELECT t.*, dm.Tendm FROM Sanpham t LEFT JOIN Danhmucsp dm ON t.Madm = dm.Madm WHERE t.Masp = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            if ($row) jsonResponse(true, 'Product found', $row);
            else jsonResponse(false, 'Product not found', null, 404);
        }
        else if ($method === 'POST') {
            $body = getBody();
            $chk = $pdo->prepare("SELECT 1 FROM Sanpham WHERE Masp = ?");
            $chk->execute([$body['Masp']]);
            if ($chk->fetchColumn()) jsonResponse(false, 'Mã sản phẩm đã tồn tại', null, 409);
            $stmt = $pdo->prepare("INSERT INTO Sanpham (Masp, Tensp, Madm, Dvt, Giaban) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$body['Masp'], $body['Tensp'], $body['Madm'] ?? null, $body['Dvt'] ?? '', $body['Giaban'] ?? 0]);
            jsonResponse(true, 'Product created', ['id' => $body['Masp']], 201);
        }
        else if ($method === 'PUT' && $id) {
            $body = getBody();
            $stmt = $pdo->prepare("UPDATE Sanpham SET Tensp=?, Madm=?, Dvt=?, Giaban=? WHERE Masp=?");
            $stmt->execute([$body['Tensp'], $body['Madm'] ?? null, $body['Dvt'] ?? '', $body['Giaban'] ?? 0, $id]);
            jsonResponse(true, 'Product updated');
        }
        else if ($method === 'DELETE' && $id) {
            $pdo->prepare("DELETE FROM Sanpham WHERE Masp=?")->execute([$id]);
            jsonResponse(true, 'Product deleted');
        }
    }

    // ===== CATEGORIES =====
    else if ($resource === 'categories') {
        if ($method === 'GET' && !$id) {
            $stmt = $pdo->query("SELECT * FROM Danhmucsp ORDER BY Madm");
            jsonResponse(true, 'Categories retrieved', ['categories' => $stmt->fetchAll()]);
        }
        else if ($method === 'GET' && $id) {
            $stmt = $pdo->prepare("SELECT * FROM Danhmucsp WHERE Madm = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            if ($row) jsonResponse(true, 'Category found', $row);
            else jsonResponse(false, 'Category not found', null, 404);
        }
        else if ($method === 'POST') {
            $body = getBody();
            $stmt = $pdo->prepare("INSERT INTO Danhmucsp (Madm, Tendm, Mota) VALUES (?, ?, ?)");
            $stmt->execute([$body['Madm'] ?? null, $body['Tendm'], $body['Mota'] ?? '']);
            jsonResponse(true, 'Category created', ['id' => $pdo->lastInsertId()], 201);
        }
        else if ($method === 'PUT' && $id) {
            $body = getBody();
            $stmt = $pdo->prepare("UPDATE Danhmucsp SET Tendm=?, Mota=? WHERE Madm=?");
            $stmt->execute([$body['Tendm'], $body['Mota'] ?? '', $id]);
            jsonResponse(true, 'Category updated');
        }
        else if ($method === 'DELETE' && $id) {
            $pdo->prepare("DELETE FROM Danhmucsp WHERE Madm=?")->execute([$id]);
            jsonResponse(true, 'Category deleted');
        }
    }

    // ===== SUPPLIERS (Nhà cung cấp) =====
    else if ($resource === 'suppliers') {
        if ($method === 'GET' && !$id) {
            $stmt = $pdo->query("SELECT * FROM Nhacungcap ORDER BY Mancc");
            jsonResponse(true, 'Suppliers retrieved', ['suppliers' => $stmt->fetchAll()]);
        }
        else if ($method === 'GET' && $id) {
            $stmt = $pdo->prepare("SELECT * FROM Nhacungcap WHERE Mancc = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            if ($row) jsonResponse(true, 'Supplier found', $row);
            else jsonResponse(false, 'Not found', null, 404);
        }
        else if ($method === 'POST') {
            $body = getBody();
            $stmt = $pdo->prepare("INSERT INTO Nhacungcap (Mancc, Tenncc, Sdtncc, Diachincc) VALUES (?, ?, ?, ?)");
            $stmt->execute([$body['Mancc'], $body['Tenncc'], $body['Sdtncc'] ?? '', $body['Diachincc'] ?? '']);
            jsonResponse(true, 'Supplier created', ['id' => $body['Mancc']], 201);
        }
        else if ($method === 'PUT' && $id) {
            $body = getBody();
            $stmt = $pdo->prepare("UPDATE Nhacungcap SET Tenncc=?, Sdtncc=?, Diachincc=? WHERE Mancc=?");
            $stmt->execute([$body['Tenncc'], $body['Sdtncc'] ?? '', $body['Diachincc'] ?? '', $id]);
            jsonResponse(true, 'Supplier updated');
        }
        else if ($method === 'DELETE' && $id) {
            $pdo->prepare("DELETE FROM Nhacungcap WHERE Mancc=?")->execute([$id]);
            jsonResponse(true, 'Supplier deleted');
        }
    }

    // ===== MATERIALS (Nguyên vật liệu) =====
    else if ($resource === 'materials') {
        if ($method === 'GET' && !$id) {
            $stmt = $pdo->query("SELECT * FROM Nguyenvatlieu ORDER BY Manvl");
            jsonResponse(true, 'Materials retrieved', ['materials' => $stmt->fetchAll()]);
        }
        else if ($method === 'GET' && $id) {
            $stmt = $pdo->prepare("SELECT * FROM Nguyenvatlieu WHERE Manvl = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            if ($row) jsonResponse(true, 'Material found', $row);
            else jsonResponse(false, 'Not found', null, 404);
        }
        else if ($method === 'POST') {
            $body = getBody();
            $stmt = $pdo->prepare("INSERT INTO Nguyenvatlieu (Manvl, Tennvl, Dvt, Giavon) VALUES (?, ?, ?, ?)");
            $stmt->execute([$body['Manvl'], $body['Tennvl'], $body['Dvt'] ?? '', $body['Giavon'] ?? 0]);
            jsonResponse(true, 'Material created', ['id' => $body['Manvl']], 201);
        }
        else if ($method === 'PUT' && $id) {
            $body = getBody();
            $stmt = $pdo->prepare("UPDATE Nguyenvatlieu SETTennvl=?, Dvt=?, Giavon=? WHERE Manvl=?");
            $stmt->execute([$body['Tennvl'], $body['Dvt'] ?? '', $body['Giavon'] ?? 0, $id]);
            jsonResponse(true, 'Material updated');
        }
        else if ($method === 'DELETE' && $id) {
            $pdo->prepare("DELETE FROM Nguyenvatlieu WHERE Manvl=?")->execute([$id]);
            jsonResponse(true, 'Material deleted');
        }
    }

    // ===== FORMULAS (Công thức sản phẩm) =====
    else if ($resource === 'formulas') {
        if ($method === 'GET' && isset($_GET['Masp'])) {
            $stmt = $pdo->prepare("SELECT c.*, n.Tennvl, n.Dvt FROM Congthucsanpham c
                LEFT JOIN Nguyenvatlieu n ON c.Manvl = n.Manvl WHERE c.Masp = ? ORDER BY c.Manvl");
            $stmt->execute([$_GET['Masp']]);
            jsonResponse(true, 'Formulas retrieved', ['formulas' => $stmt->fetchAll()]);
        }
        else if ($method === 'GET' && !$id) {
            $stmt = $pdo->query("SELECT c.Masp, s.Tensp, c.Manvl, n.Tennvl, c.Soluong, n.Dvt
                FROM Congthucsanpham c
                LEFT JOIN Sanpham s ON c.Masp = s.Masp
                LEFT JOIN Nguyenvatlieu n ON c.Manvl = n.Manvl ORDER BY c.Masp");
            jsonResponse(true, 'All formulas retrieved', ['formulas' => $stmt->fetchAll()]);
        }
        else if ($method === 'POST') {
            $body = getBody();
            // Check if exists
            $chk = $pdo->prepare("SELECT 1 FROM Congthucsanpham WHERE Masp=? AND Manvl=?");
            $chk->execute([$body['Masp'], $body['Manvl']]);
            if ($chk->fetchColumn()) {
                $pdo->prepare("UPDATE Congthucsanpham SET Soluong=? WHERE Masp=? AND Manvl=?")
                    ->execute([$body['Soluong'], $body['Masp'], $body['Manvl']]);
                jsonResponse(true, 'Formula updated');
            } else {
                $pdo->prepare("INSERT INTO Congthucsanpham (Masp, Manvl, Soluong) VALUES (?, ?, ?)")
                    ->execute([$body['Masp'], $body['Manvl'], $body['Soluong']]);
                jsonResponse(true, 'Formula created', null, 201);
            }
        }
        else if ($method === 'DELETE' && $id) {
            // ID format: SP01_VL01 (using _ as separator to avoid conflict with URL)
            $p = explode('_', $id, 2);
            if (count($p) === 2) {
                $pdo->prepare("DELETE FROM Congthucsanpham WHERE Masp=? AND Manvl=?")->execute([$p[0], $p[1]]);
            } else {
                $pdo->prepare("DELETE FROM Congthucsanpham WHERE Masp=?")->execute([$id]);
            }
            jsonResponse(true, 'Formula deleted');
        }
    }

    else {
        jsonResponse(false, 'Endpoint not found in Product Service', null, 404);
    }
} catch (Exception $e) {
    jsonResponse(false, 'Server Error: ' . $e->getMessage(), null, 500);
}
