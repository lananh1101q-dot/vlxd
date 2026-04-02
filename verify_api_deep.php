<?php
// Script lấy Token và kiểm tra SQL sâu
$base = "http://localhost:8000/api/v1";

// 1. Tạo Token giả lập cho 'admin' (NV01)
// Do gateway của chúng ta đang dùng base64_encode(json_encode(['Manv' => ...]))
$payload = ['Manv' => 'NV01', 'Vaitro' => 'admin'];
$token = base64_encode(json_encode($payload));
$headers = ["Authorization: Bearer $token"];

function test($url, $heads) {
    echo "Testing $url... ";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $heads);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo "Status: $code\n";
    if ($code == 500 || $code == 200) {
        $data = json_decode($resp, true);
        if (!$data['success']) {
            echo "ERROR: " . $data['message'] . "\n";
        } else {
            echo "- OK: " . count($data['data']) . " records/info retrieved.\n";
        }
    }
    curl_close($ch);
}

// Kiểm tra endpoint Tồn kho (Từng gặp lỗi JOIN)
test($base . "/inventory", $headers);
test($base . "/products", $headers);
test($base . "/customers", $headers);
