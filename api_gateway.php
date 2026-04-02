<?php
/**
 * API Gateway - Simple Router with Role-Based Access Control
 * Routes all API requests to appropriate handlers
 */

session_start();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/role_helper.php';

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Parse request
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/vlxd/api_gateway.php', '', $path);
$query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);

$parts = array_filter(explode('/', $path));
$parts = array_values($parts);

/**
 * Response helper
 */
function apiResponse($success, $message, $data = null, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('c')
    ]);
    exit;
}

/**
 * Get request body
 */
function getRequestBody() {
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? $_POST;
}

/**
 * Check token from header
 */
function getAuthToken() {
    $headers = getallheaders();
    if (isset($headers['Authorization'])) {
        $match = [];
        if (preg_match('/Bearer\s+(.+)/', $headers['Authorization'], $match)) {
            $token = trim($match[1]);
            if ($token === 'null' || $token === 'undefined' || empty($token)) {
                apiResponse(false, 'Unauthorized: Token is null or undefined from frontend', null, 401);
            }
            return $token;
        }
    }
    return null;
}

// ===== ROUTES =====

try {
    // User Service Routes
    if (isset($parts[0]) && $parts[0] === 'users') {
        // GET /users - List all users (Admin only)
        if ($method === 'GET' && count($parts) === 1) {
            if (!isAdmin()) {
                apiResponse(false, 'Unauthorized: Admin only', null, 403);
            }
            
            $stmt = $pdo->query("SELECT Manv, Tendangnhap, Hovaten, Email, Vaitro FROM Nguoidung ORDER BY Manv");
            $users = $stmt->fetchAll();
            apiResponse(true, 'Users retrieved', ['users' => $users]);
        }
        
        // GET /users/:id - Get user by ID
        elseif ($method === 'GET' && count($parts) === 2) {
            $userId = $parts[1];
            $stmt = $pdo->prepare("SELECT Manv, Tendangnhap, Hovaten, Email, Vaitro FROM Nguoidung WHERE Manv = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if (!$user) {
                apiResponse(false, 'User not found', null, 404);
            }
            apiResponse(true, 'User retrieved', $user);
        }
        
        // POST /users - Create user (Admin only)
        elseif ($method === 'POST' && count($parts) === 1) {
            if (!isAdmin()) {
                apiResponse(false, 'Unauthorized: Admin only', null, 403);
            }
            
            $body = getRequestBody();
            $stmt = $pdo->prepare("INSERT INTO Nguoidung (Manv, Tendangnhap, Matkhau, Hovaten, Email, Vaitro) 
                                  VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $body['Manv'],
                $body['Tendangnhap'],
                password_hash($body['Matkhau'], PASSWORD_DEFAULT),
                $body['Hovaten'] ?? '',
                $body['Email'] ?? '',
                $body['Vaitro'] ?? 'staff'
            ]);
            
            apiResponse(true, 'User created', ['id' => $body['Manv']]);
        }
        
        // DELETE /users/:id - Delete user (Admin only)
        elseif ($method === 'DELETE' && count($parts) === 2) {
            if (!isAdmin()) {
                apiResponse(false, 'Unauthorized: Admin only', null, 403);
            }
            
            $userId = $parts[1];
            $stmt = $pdo->prepare("DELETE FROM Nguoidung WHERE Manv = ?");
            $stmt->execute([$userId]);
            
            apiResponse(true, 'User deleted');
        }
    }
    
    // Product Service Routes
    elseif (isset($parts[0]) && $parts[0] === 'products') {
        // GET /products - List products (All roles)
        if ($method === 'GET' && count($parts) === 1) {
            $stmt = $pdo->query("SELECT Masp, Tensp, Madm, Dvt, Giaban FROM Sanpham ORDER BY Masp");
            $products = $stmt->fetchAll();
            apiResponse(true, 'Products retrieved', ['products' => $products]);
        }
        
        // POST /products - Create product (Staff/Admin)
        elseif ($method === 'POST' && count($parts) === 1) {
            if (!hasAnyRole([ROLE_ADMIN, ROLE_STAFF])) {
                apiResponse(false, 'Unauthorized', null, 403);
            }
            
            $body = getRequestBody();
            $stmt = $pdo->prepare("INSERT INTO Sanpham (Masp, Tensp, Madm, Dvt, Giaban) 
                                  VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $body['Masp'],
                $body['Tensp'],
                $body['Madm'] ?? 1,
                $body['Dvt'] ?? '',
                $body['Giaban'] ?? 0
            ]);
            
            apiResponse(true, 'Product created', ['id' => $body['Masp']]);
        }
    }
    
    // Warehouse Service Routes
    elseif (isset($parts[0]) && $parts[0] === 'warehouses') {
        // GET /warehouses - List warehouses (All roles)
        if ($method === 'GET') {
            $stmt = $pdo->query("SELECT Makho, Tenkho, Diachi FROM Kho ORDER BY Makho");
            $warehouses = $stmt->fetchAll();
            apiResponse(true, 'Warehouses retrieved', ['warehouses' => $warehouses]);
        }
    }
    
    // Customer Service Routes
    elseif (isset($parts[0]) && $parts[0] === 'customers') {
        // GET /customers - List customers (All roles)
        if ($method === 'GET') {
            $stmt = $pdo->query("SELECT Makh, Tenkh, Sdtkh, Diachikh FROM Khachhang ORDER BY Makh");
            $customers = $stmt->fetchAll();
            apiResponse(true, 'Customers retrieved', ['customers' => $customers]);
        }
    }
    
    // Health check
    elseif (isset($parts[0]) && $parts[0] === 'health') {
        apiResponse(true, 'API Gateway is running', ['status' => 'healthy']);
    }
    
    // Not found
    else {
        apiResponse(false, 'Endpoint not found', null, 404);
    }
    
} catch (Exception $e) {
    apiResponse(false, 'Error: ' . $e->getMessage(), null, 500);
}
