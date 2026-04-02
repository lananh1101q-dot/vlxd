<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$servicesUrl = [
    'users' => 'http://127.0.0.1:3001',
    'auth' => 'http://127.0.0.1:3001',
    'products' => 'http://127.0.0.1:3002',
    'categories' => 'http://127.0.0.1:3002',
    'materials' => 'http://127.0.0.1:3002',
    'formulas' => 'http://127.0.0.1:3002',
    'suppliers' => 'http://127.0.0.1:3002',
    'warehouses' => 'http://127.0.0.1:3003',
    'inventory' => 'http://127.0.0.1:3003',
    'import-receipts' => 'http://127.0.0.1:3003',
    'export-receipts' => 'http://127.0.0.1:3003',
    'transfers' => 'http://127.0.0.1:3003',
    'manufacturing' => 'http://127.0.0.1:3004',
    'production-orders' => 'http://127.0.0.1:3004',
    'complete-production' => 'http://127.0.0.1:3004',
    'customers' => 'http://127.0.0.1:3005',
    'customer-types' => 'http://127.0.0.1:3005',
];

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];
$parsedUrl = parse_url($requestUri);
$path = $parsedUrl['path'];
$pathParts = explode('/', trim($path, '/'));

$startIndex = 0;
foreach ($pathParts as $index => $part) {
    if ($part === 'v1') {
        $startIndex = $index + 1;
        break;
    }
}

if (!isset($pathParts[$startIndex])) {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'API Gateway is running']);
    exit;
}

$resource = $pathParts[$startIndex];

// AUTH MIDDLEWARE
if ($resource !== 'auth') {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        $authHeader = $requestHeaders['Authorization'] ?? $requestHeaders['authorization'] ?? $authHeader;
    }
    
    // Very simple token check (Expecting "Bearer base64...")
    if (empty($authHeader) || !preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized: Missing or Invalid Token']);
        exit;
    }
    
    // Basic decode signature verification check
    $token = trim($matches[1]);
    
    if ($token === 'null' || $token === 'undefined' || empty($token)) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized: Token is null or undefined. Please login again.']);
        exit;
    }

    $decoded = base64_decode($token, true); // Strict mode
    if ($decoded === false) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized: Invalid Base64 Encoding', 'debug_token' => substr($token, 0, 10)]);
        exit;
    }

    $payload = json_decode($decoded, true);
    if (!$payload || !isset($payload['Manv'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized: Invalid Token Payload', 'payload_debug' => $payload]);
        exit;
    }
}

if (!isset($servicesUrl[$resource])) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => "Service for resource '{$resource}' not found"]);
    exit;
}

$targetBaseUrl = $servicesUrl[$resource];
$targetUri = rtrim($targetBaseUrl, '/') . substr($requestUri, strpos($requestUri, '/api/v1'));

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $targetUri);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

$headers = [];
if (function_exists('getallheaders')) {
    foreach (getallheaders() as $name => $value) {
        if (strtolower($name) !== 'host' && strtolower($name) !== 'content-length') {
            $headers[] = "$name: $value";
        }
    }
}

$body = file_get_contents('php://input');
if ($body) {
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    $headers[] = 'Content-Length: ' . strlen($body);
    $hasContentType = false;
    foreach($headers as $h) {
        if (stripos($h, 'Content-Type') !== false) {
            $hasContentType = true;
            break;
        }
    }
    if (!$hasContentType) {
       $headers[] = 'Content-Type: application/json';
    }
}

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    http_response_code(502);
    echo json_encode(['success' => false, 'message' => 'Bad Gateway: ' . curl_error($ch)]);
} else {
    http_response_code($httpCode);
    foreach(explode("\n", curl_getinfo($ch, CURLINFO_HEADER_OUT) ?? '') as $header) {
        if (stripos($header, 'Content-Type:') === 0) {
            header($header);
        }
    }
    echo $response;
}
curl_close($ch);
