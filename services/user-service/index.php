<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/api/v1', '', $path);
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
    $id = $parts[1] ?? null;

    if (!$resource) {
        jsonResponse(true, 'User Service is running', ['version' => '1.0']);
    }

    // ===== AUTH =====
    if ($resource === 'auth') {
        $action = $parts[1] ?? null;

        if ($action === 'login' && $method === 'POST') {
            $body = getBody();
            $u = trim($body['Tendangnhap'] ?? '');
            $p = trim($body['Matkhau'] ?? '');

            if (!$u || !$p) {
                jsonResponse(false, 'Vui lòng nhập đầy đủ tài khoản và mật khẩu.', null, 400);
            }

            $stmt = $pdo->prepare("SELECT * FROM Nguoidung WHERE Tendangnhap = ? ");
            $stmt->execute([$u]);
            $user = $stmt->fetch();

            $valid = false;
            if ($user) {
                // Hỗ trợ cả plain text và bcrypt hash
                if (isset($user['Matkhau'])) {
                    if (password_verify($p, $user['Matkhau'])) {
                        $valid = true;
                    } elseif ($p === $user['Matkhau']) {
                        $valid = true;
                    }
                }
            }

            if ($valid) {
                // Tạo token base64 tương thích với API Gateway
                $payload = [
                    'Manv'       => $user['Manv'],
                    'Tendangnhap'=> $user['Tendangnhap'],
                    'Vaitro'     => $user['Vaitro'],
                    'exp'        => time() + 86400
                ];
                $token = base64_encode(json_encode($payload));

                jsonResponse(true, 'Đăng nhập thành công', [
                    'token' => $token,
                    'user'  => [
                        'Manv'        => $user['Manv'],
                        'Tendangnhap' => $user['Tendangnhap'],
                        'Hovaten'     => $user['Hovaten'],
                        'Email'       => $user['Email'],
                        'Vaitro'      => $user['Vaitro'],
                    ]
                ]);
            } else {
                jsonResponse(false, 'Sai tài khoản hoặc mật khẩu!', null, 401);
            }
        }
        jsonResponse(false, 'Auth endpoint not found', null, 404);
    }

    // ===== USERS =====
    if ($resource === 'users') {
        // GET all users
        if ($method === 'GET' && !$id) {
            $stmt = $pdo->query("SELECT Manv, Tendangnhap, Hovaten, Email, Vaitro FROM Nguoidung ORDER BY Manv");
            jsonResponse(true, 'Users retrieved', ['users' => $stmt->fetchAll()]);
        }

        // GET single user
        if ($method === 'GET' && $id) {
            $stmt = $pdo->prepare("SELECT Manv, Tendangnhap, Hovaten, Email, Vaitro FROM Nguoidung WHERE Manv = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch();
            if ($user) jsonResponse(true, 'User found', $user);
            else jsonResponse(false, 'User not found', null, 404);
        }

        // POST create user
        if ($method === 'POST') {
            $body = getBody();
            // Kiểm tra trùng tên đăng nhập
            $chk = $pdo->prepare("SELECT Manv FROM Nguoidung WHERE Tendangnhap = ?");
            $chk->execute([$body['Tendangnhap']]);
            if ($chk->fetch()) {
                jsonResponse(false, 'Tên đăng nhập đã tồn tại', null, 409);
            }
            $hashedPw = password_hash($body['Matkhau'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO Nguoidung (Manv, Tendangnhap, Matkhau, Hovaten, Email, Vaitro) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $body['Manv'],
                $body['Tendangnhap'],
                $hashedPw,
                $body['Hovaten'] ?? '',
                $body['Email'] ?? '',
                $body['Vaitro'] ?? 'staff'
            ]);
            jsonResponse(true, 'User created successfully', ['id' => $body['Manv']], 201);
        }

        // PUT update user
        if ($method === 'PUT' && $id) {
            $body = getBody();
            $fields = [];
            $params = [];
            if (isset($body['Hovaten'])) { $fields[] = 'Hovaten = ?'; $params[] = $body['Hovaten']; }
            if (isset($body['Email']))   { $fields[] = 'Email = ?';   $params[] = $body['Email']; }
            if (isset($body['Vaitro'])) { $fields[] = 'Vaitro = ?';  $params[] = $body['Vaitro']; }
            if (isset($body['Matkhau']) && $body['Matkhau']) {
                $fields[] = 'Matkhau = ?';
                $params[] = password_hash($body['Matkhau'], PASSWORD_DEFAULT);
            }
            if (empty($fields)) jsonResponse(false, 'No fields to update', null, 400);
            $params[] = $id;
            $stmt = $pdo->prepare("UPDATE Nguoidung SET " . implode(', ', $fields) . " WHERE Manv = ?");
            $stmt->execute($params);
            jsonResponse(true, 'User updated');
        }

        // DELETE user
        if ($method === 'DELETE' && $id) {
            $stmt = $pdo->prepare("DELETE FROM Nguoidung WHERE Manv = ?");
            $stmt->execute([$id]);
            jsonResponse(true, 'User deleted');
        }
    }

    jsonResponse(false, 'Endpoint not found in User Service', null, 404);
} catch (Exception $e) {
    jsonResponse(false, 'Server Error: ' . $e->getMessage(), null, 500);
}